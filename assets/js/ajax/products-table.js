// Archivo: assets/js/ajax/products-table.js
let table = null;

let activeFilters = {
  status: "",
  stock: "",
  priceFrom: "",
  priceTo: ""
};

document.addEventListener("DOMContentLoaded", function () {
  // Función reutilizable para cerrar modal y reenfocar
  function closeModalAndFocusTarget(modalId, focusTargetId) {
    const modalEl = document.getElementById(modalId);
    if (!modalEl) return;
    if (document.activeElement instanceof HTMLElement) {
      document.activeElement.blur();
    }
    const modalInst = bootstrap.Modal.getInstance(modalEl);
    if (modalInst) {
      modalInst.hide();
    }
    if (focusTargetId) {
      setTimeout(() => {
        document.getElementById(focusTargetId)?.focus();
      }, 300);
    }
  }

  // Evitar warnings aria-hidden al cerrar modal
  document.querySelectorAll('[data-bs-dismiss="modal"]').forEach((btn) => {
    btn.addEventListener("click", () => btn.blur());
  });
  ["addProductModal", "editProductModal", "deleteProductModal"].forEach(
    (modalId) => {
      const modalEl = document.getElementById(modalId);
      if (modalEl) {
        modalEl.addEventListener("hide.bs.modal", function () {
          const active = document.activeElement;
          if (active && modalEl.contains(active)) {
            active.blur();
          }
        });
      }
    }
  );

  // Contenedor de la tabla
  var productsTableElement = document.getElementById("products-table");
  if (!productsTableElement) return;

  //var deleteProductID = null;


  console.log("Inicializando Tabulator con paginación remota...");
  table = new Tabulator("#products-table", {
    layout: "fitColumns", // CAMBIADO: de fitDataFill a fitColumns para evitar columna vacía
    placeholder: "Cargando productos...",

    // Configuración moderna de paginación
    pagination: "remote",
    paginationSize: 20,
    paginationSizeSelector: [
      10, 20, 30, 50, 100, 200, 500, 1000, 2000, 3000, 4000, 5000, 10000,
    ],
    paginationButtonCount: 7,

    // Configuración AJAX
    ajaxURL: BASE_URL + "api/products.php?action=list",
    ajaxConfig: "GET",
    ajaxParams: function () {
      return {
        status: activeFilters.status,
        stock: activeFilters.stock,
        priceFrom: activeFilters.priceFrom,
        priceTo: activeFilters.priceTo
      };
    },
    paginationDataSent: {
      page: "page",
      size: "size",
    },

    // Indicador de carga moderno
    ajaxRequesting: function (url, params) {
      console.log("Tabulator request:", url, params);
      document.querySelector("#products-table").style.opacity = "0.7";
    },

    ajaxResponse: function (url, params, response) {
      // restablecer opacidad
      document.querySelector("#products-table").style.opacity = "1";

      // respuestas compatibles:
      // 1) { last_page, page, total, data: [...] }
      // 2) o directamente un array [...]
      if (!response) return [];

      if (Array.isArray(response.data)) {
        return response.data;
      }

      if (Array.isArray(response)) {
        return response;
      }

      // fallback: si no hay array en data intentar detectar 'data' como objeto
      if (Array.isArray(response)) return response;

      console.warn("Respuesta inesperada para ajaxResponse:", response);
      return [];
    },

    paginationDataReceived: {
      last_page: "last_page",
      data: "data",
      page: "page",
      total: "total"
    },

    // Configuración de columnas - TODAS VISIBLES CON ESPACIOS OPTIMIZADOS
    columns: [
      {
        title: "ID",
        field: "product_id",
        hozAlign: "center",
        sorter: "number",
        widthGrow: 1,
      },
      {
        title: "Código",
        field: "product_code",
        widthGrow: 1,
      },
      {
        title: "Código Barras",
        field: "barcode",
        widthGrow: 1,
      },
      {
        title: "Name",
        field: "product_name",
        widthGrow: 1,
        formatter: function (cell) {
          const data = cell.getData();
          const productId = data.product_id;
          const name = cell.getValue();
          const link =
            BASE_URL + "product_detail?id=" + encodeURIComponent(productId);
          return `<a href="${link}" class="text-decoration-none fw-semibold">${name}</a>`;
        },
      },
      {
        title: "Ubicación",
        field: "location",
        widthGrow: 1,
        headerSort: true,
        formatter: function (cell) {
          const value = cell.getValue();
          if (!value) return '<span class="text-muted">Sin ubicación</span>';
          return `<span class="badge bg-light text-dark border">${value}</span>`;
        },
      },
      {
        title: "Precio",
        field: "price",
        hozAlign: "right",
        widthGrow: 1,
        headerSort: true,
        formatter: function (cell) {
          const value = cell.getValue();
          if (value == null || value === "")
            return '<span class="text-muted">N/A</span>';
          return `<span class="fw-bold">${parseFloat(value).toLocaleString(
            "es-MX",
            { minimumFractionDigits: 2, maximumFractionDigits: 2 }
          )}</span>`;
        },
      },
      {
        title: "Stock",
        field: "stock",
        hozAlign: "center",
        sorter: "number",
        widthGrow: 1,
        headerSort: true,
        formatter: function (cell) {
          const stock = parseInt(cell.getValue());
          const row = cell.getData();
          const desiredStock = parseInt(row.desired_stock);
          let badgeClass = "bg-secondary";
          if (!isNaN(stock) && !isNaN(desiredStock)) {
            if (stock < desiredStock) badgeClass = "bg-danger";
            else if (stock === desiredStock)
              badgeClass = "bg-warning text-dark";
            else badgeClass = "bg-success";
          }
          return `<span class="badge ${badgeClass}">${stock}</span>`;
        },
      },
      {
        title: "Fecha",
        field: "registration_date",
        widthGrow: 1,
        headerSort: true,
        formatter: function (cell) {
          const value = cell.getValue();
          if (!value) return '<span class="text-muted">N/A</span>';
          const date = new Date(value);
          if (isNaN(date.getTime()))
            return '<span class="text-muted">Fecha inválida</span>';
          const day = String(date.getDate()).padStart(2, "0");
          const month = String(date.getMonth() + 1).padStart(2, "0");
          const year = date.getFullYear();
          return `<small class="text-muted">${day}/${month}/${year}</small>`;
        },
        cssClass: "small",
      },
      {
        title: "Imagen",
        field: "image_url",
        hozAlign: "center",
        widthGrow: 1,
        formatter: function (cell) {
          const row = cell.getData();
          if (!row.image_url) {
            return '<div class="d-flex justify-content-center"><i class="fas fa-image text-muted" style="font-size:25px;"></i></div>';
          }
          const version = row.image_version || Date.now();
          const src = BASE_URL + row.image_url + "?v=" + version;
          // REEMPLAZA SOLO EL "return" DE ESA FUNCIÓN FORMATTER
          return `<div class="d-flex justify-content-center">
      <img src="${src}"
      data-src="${src}"
      style="max-height:40px; max-width:40px; border-radius:6px; object-fit:cover; cursor:pointer;"
      alt="Imagen"
       loading="lazy"
       class="border shadow-sm product-thumb"/>
     </div>`;
        },
      },
      {
        title: "Acciones",
        hozAlign: "center",
        widthGrow: 1,
        headerSort: false,
        formatter: function () {
          const isMobile = window.innerWidth < 768;
          if (isMobile) {
            return `
          <div class="btn-group-vertical btn-group-sm d-grid gap-1" role="group">
            <button class="btn btn-outline-primary btn-sm edit-btn" title="Editar">
              Editar
            </button>
            <button class="btn btn-outline-danger btn-sm delete-btn" title="Borrar">
              Borrar
            </button>
          </div>`;
          } else {
            return `
          <div class="btn-group">
            <button class="btn btn-sm btn-outline-primary edit-btn me-1">
              <i class="bi bi-pencil-square"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger delete-btn">
              <i class="bi bi-trash3"></i>
            </button>
          </div>`;
          }
        },
        cellClick: function (e, cell) {
          const rowData = cell.getRow().getData();
          // EDITAR
          if (
            e.target.classList.contains("edit-btn") ||
            e.target.closest(".edit-btn")
          ) {
            document.getElementById("edit-product-id").value =
              rowData.product_id;
            document.getElementById("edit-product-code").value =
              rowData.product_code || "";
            document.getElementById("edit-barcode").value =
              rowData.barcode || "";
            document.getElementById("edit-product-name").value =
              rowData.product_name || "";
            document.getElementById("edit-product-description").value =
              rowData.product_description || "";
            document.getElementById("edit-location").value =
              rowData.location || "";
            document.getElementById("edit-price").value = rowData.price ?? "";
            document.getElementById("edit-stock").value = rowData.stock ?? "";
            document.getElementById("edit-category").value =
              rowData.category_id ?? "";
            document.getElementById("edit-supplier").value =
              rowData.supplier_id ?? "";
            document.getElementById("edit-unit").value = rowData.unit_id ?? "";
            document.getElementById("edit-currency").value =
              rowData.currency_id ?? "";
            document.getElementById("edit-subcategory").value =
              rowData.subcategory_id ?? "";
            document.getElementById("edit-desired-stock").value =
              rowData.desired_stock ?? "";
            document.getElementById("edit-status").value =
              rowData.status != null ? rowData.status : "1";

            // --- LÍNEAS SOLICITADAS: rellenar inputs de medidas y peso ---
            // Asegúrate de que los elementos existan en el modal (IDs mostrados abajo)
            // === CAMPOS NUEVOS (EDIT) ===
            document.getElementById("edit-sale-price").value =
              rowData.sale_price ?? "";
            document.getElementById("edit-weight").value = rowData.weight ?? "";
            document.getElementById("edit-height").value = rowData.height ?? "";
            document.getElementById("edit-length").value = rowData.length ?? "";
            document.getElementById("edit-width").value = rowData.width ?? "";
            document.getElementById("edit-diameter").value =
              rowData.diameter ?? "";

            // ----------------------------------------------------------------

            var editModalEl = document.getElementById("editProductModal");
            if (editModalEl) new bootstrap.Modal(editModalEl).show();
          }
          // ELIMINAR
          if (
            e.target.classList.contains("delete-btn") ||
            e.target.closest(".delete-btn")
          ) {

            // ELIMINAR
            // --- DENTRO DE cellClick -> ELIMINAR ---
            if (e.target.classList.contains("delete-btn") || e.target.closest(".delete-btn")) {
              const productId = rowData.product_id;
              const productName = rowData.product_name || '';
              const stockActual = parseInt(rowData.stock) || 0;

              // Configuración dinámica del mensaje
              let title = '¿Eliminar producto?';
              let icon = 'warning';
              let htmlContent = `Estás a punto de eliminar <strong>${productName}</strong>.`;

              // Si hay stock, cambiamos la advertencia a algo más serio
              if (stockActual > 0) {
                title = '<span style="color: #d33">¡Atención: Stock Disponible!</span>';
                icon = 'error';
                htmlContent = `El producto <strong>${productName}</strong> todavía tiene <strong>${stockActual} unidades</strong> en existencia.<br><br><span class="text-danger fw-bold">¿Realmente deseas eliminarlo? Esto afectará los inventarios.</span>`;
              }

              Swal.fire({
                title: title,
                html: htmlContent,
                icon: icon,
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar de todos modos',
                cancelButtonText: 'Cancelar',
                buttonsStyling: false,
                customClass: {
                  confirmButton: 'btn btn-danger px-4',
                  cancelButton: 'btn btn-secondary ms-2'
                }
              }).then((result) => {
                if (result.isConfirmed) {
                  // Ejecutar la petición fetch para borrar
                  fetch(BASE_URL + "api/products.php?action=delete", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ product_id: productId, product_name: productName }),
                  })
                    .then(res => res.json())
                    .then(data => {
                      if (data.success) {
                        Swal.fire({
                          icon: "success",
                          title: "Eliminado",
                          text: "El producto ha sido borrado y el log registrado.",
                          toast: true,
                          position: "top-end",
                          timer: 3000,
                          showConfirmButton: false
                        });
                        table.setData(); // Recargar tabla
                      } else {
                        throw new Error(data.message);
                      }
                    })
                    .catch(err => {
                      Swal.fire("Error", err.message, "error");
                    });
                }
              });
            }

            /*
            deleteProductID = rowData.product_id;
            var deleteModalEl = document.getElementById("deleteProductModal");
            if (deleteModalEl) new bootstrap.Modal(deleteModalEl).show();
            */
          }
        },
      },
    ],

    // Configuración adicional para mejorar la experiencia
    headerSort: true,
    headerSortTristate: true,
    movableColumns: false,
    resizableColumns: true,
    tooltips: true,

    // Eventos adicionales para mejor UX
    dataLoaded: function (data) {
      console.log("Datos cargados:", data.length, "productos");
    },

    renderComplete: function () {
      this.element.style.transition = "opacity 0.3s ease";
    },

    rowClick: function (e, row) {
      // row.getElement().style.backgroundColor = "#f8f9fa";
    },
  });


  // --- Definición de autoFilters (no se ejecuta automáticamente)
  // --- Inicializador seguro de filtros: espera a que 'table' exista y evita inicializar dos veces
  (function safeAutoFilters() {
    let initialized = false;

    function init() {
      if (initialized) return;
      if (!table) {
        // si table aún no existe, reintenta en 60ms (evita race conditions)
        setTimeout(init, 60);
        return;
      }
      initialized = true;

      function applyAutoFilter() {
        // Si por alguna razón table desapareció, abortar
        if (!table) return;

        activeFilters.status = document.getElementById("statusFilter")?.value || "";
        activeFilters.stock = document.getElementById("stockFilter")?.value || "";
        activeFilters.priceFrom = document.getElementById("priceFromFilter")?.value || "";
        activeFilters.priceTo = document.getElementById("priceToFilter")?.value || "";

        console.log("applyAutoFilter -> filtros:", activeFilters);
        // volver a página 1 y pedir los datos remotos con los filtros nuevos
        try {
          table.setPage(1);
          // replaceData hace que Tabulator llame a ajaxURL usando ajaxParams
          if (typeof table.replaceData === "function") {
            table.replaceData();
          } else {
            // fallback por compatibilidad
            table.setData();
          }
        } catch (err) {
          console.warn("Error al forzar recarga de datos en table:", err);
        }
      }

      function debounce(fn, delay) {
        let t;
        return function () {
          clearTimeout(t);
          t = setTimeout(fn, delay);
        };
      }

      ["statusFilter", "stockFilter", "priceFromFilter", "priceToFilter"].forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        el.addEventListener("change", applyAutoFilter);
        el.addEventListener("input", debounce(applyAutoFilter, 400));
      });

      const clearBtn = document.getElementById("clearFilters");
      if (clearBtn) {
        clearBtn.addEventListener("click", function () {
          ["statusFilter", "stockFilter", "priceFromFilter", "priceToFilter"].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = "";
          });

          activeFilters = { status: "", stock: "", priceFrom: "", priceTo: "" };
          table.setPage(1);
          if (typeof table.replaceData === "function") table.replaceData();
          else table.setData();
        });
      }

      console.log("autoFilters inicializado correctamente");
    }

    // Ejecutar init dentro de DOMContentLoaded: si ya estamos dentro, init() se invoca; si no, esperará.
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", init);
    } else {
      init();
    }
  })();



  // --- Preview imagen: al hacer click en miniatura de la tabla abre #imagePreviewModal ---
  (function setupImagePreviewFromTable() {
    const tableContainer = document.querySelector("#products-table");
    const previewModalEl = document.getElementById("imagePreviewModal");
    const previewImg = document.getElementById("imagePreviewModalImg");

    if (!tableContainer) return;
    if (!previewModalEl || !previewImg) {
      console.warn("Modal de imagen no encontrado. Agrega #imagePreviewModal y #imagePreviewModalImg en la vista de lista.");
      return;
    }

    tableContainer.addEventListener("click", function (e) {
      const clicked = e.target.closest(".product-thumb");
      if (!clicked) return;
      const src = clicked.dataset.src || clicked.getAttribute("src");
      if (!src) return;

      previewImg.src = src + (src.includes("?") ? "&" : "?") + "v=" + Date.now();
      previewImg.alt = clicked.alt || "Imagen del producto";

      const modal = new bootstrap.Modal(previewModalEl);
      modal.show();
    });
  })();






  // CSS mejorado para scroll horizontal funcional
  // CSS mejorado para scroll horizontal funcional
  const style = document.createElement("style");
  style.textContent = `
  .tabulator {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    width: 100%;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    position: relative;
    margin-bottom: 30px;
  }

  .tabulator-table {
    min-width: 1450px;
    touch-action: pan-x;
    width: 100% !important;
    border-spacing: 0;
  }

  .tabulator-col:empty,
  .tabulator-cell:empty:not([data-field]) {
    display: none !important;
  }

  .tabulator-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 2px solid #dee2e6;
  }

  .tabulator-row:hover {
    background-color: #f8f9fa !important;
  }

  @media (max-width: 767px) {
    .tabulator::-webkit-scrollbar {
      height: 12px;
    }

    .tabulator::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 6px;
    }

    .tabulator::-webkit-scrollbar-thumb {
      background: #007bff;
      border-radius: 6px;
      border: 2px solid #f1f1f1;
    }

    .tabulator::-webkit-scrollbar-thumb:hover {
      background: #0056b3;
    }

    .tabulator::after {
      content: "← Desliza para ver más columnas →";
      position: absolute;
      bottom: -25px;
      left: 50%;
      transform: translateX(-50%);
      font-size: 12px;
      color: #007bff;
      font-weight: 500;
      pointer-events: none;
    }

    .tabulator-cell {
      padding: 8px 6px !important;
      font-size: 13px;
    }

    .tabulator-col {
      padding: 10px 6px !important;
      font-size: 12px;
      font-weight: 600;
    }

    .btn-group-vertical .btn {
      font-size: 11px;
      padding: 4px 8px;
    }
  }

  @media (min-width: 768px) {
    .tabulator::-webkit-scrollbar {
      height: 8px;
    }

    .tabulator::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 4px;
    }

    .tabulator::-webkit-scrollbar-thumb {
      background: #888;
      border-radius: 4px;
    }

    .tabulator::-webkit-scrollbar-thumb:hover {
      background: #555;
    }
  }
`;

  document.head.appendChild(style);

  // Indicador visual de scroll para móviles
  if (window.innerWidth < 768) {
    setTimeout(() => {
      const scrollHint = document.createElement("div");
      scrollHint.innerHTML =
        "📱 Desliza horizontalmente para ver todas las columnas";
      scrollHint.style.cssText = `
      position: fixed;
      top: 20px;
      left: 50%;
      transform: translateX(-50%);
      background: #007bff;
      color: white;
      padding: 12px 24px;
      border-radius: 20px;
      font-size: 14px;
      font-weight: 500;
      z-index: 1000;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      animation: slideInOut 5s ease-in-out;
    `;

      const hintStyle = document.createElement("style");
      hintStyle.textContent = `
      @keyframes slideInOut {
        0% { opacity: 0; transform: translateX(-50%) translateY(-20px); }
        15% { opacity: 1; transform: translateX(-50%) translateY(0); }
        85% { opacity: 1; transform: translateX(-50%) translateY(0); }
        100% { opacity: 0; transform: translateX(-50%) translateY(-20px); }
      }
    `;
      document.head.appendChild(hintStyle);
      document.body.appendChild(scrollHint);

      setTimeout(() => {
        scrollHint.remove();
        hintStyle.remove();
      }, 5000);
    }, 1000);
  }

  // Búsqueda local en página actual
  var searchInput = document.getElementById("table-search");
  if (searchInput) {
    searchInput.addEventListener("input", function () {
      var q = searchInput.value.toLowerCase();
      table.setFilter(function (data) {
        return (
          (data.product_code || "").toString().toLowerCase().includes(q) ||
          (data.product_name || "").toString().toLowerCase().includes(q)
        );
      });
    });
  }

  // === CRUD: crear nuevo producto ===
  var saveNewProductBtn = document.getElementById("saveNewProductBtn");
  if (saveNewProductBtn) {
    saveNewProductBtn.addEventListener("click", function () {
      var formData = new FormData();
      // Recoger valores de inputs del modal
      var newCodeEl = document.getElementById("new-product-code");
      if (newCodeEl) formData.append("product_code", newCodeEl.value.trim());
      var newBarcodeEl = document.getElementById("new-barcode");
      if (newBarcodeEl) formData.append("barcode", newBarcodeEl.value.trim());
      var newNameEl = document.getElementById("new-product-name");
      if (newNameEl) formData.append("product_name", newNameEl.value.trim());
      var newDescriptionEl = document.getElementById("product_description");
      if (newDescriptionEl)
        formData.append("product_description", newDescriptionEl.value.trim());
      var newLocationEl = document.getElementById("new-location");
      if (newLocationEl)
        formData.append("location", newLocationEl.value.trim());
      var newPriceEl = document.getElementById("new-price");
      if (newPriceEl) formData.append("price", newPriceEl.value);
      var newStockEl = document.getElementById("new-stock");
      if (newStockEl) formData.append("stock", newStockEl.value);
      var categoryEl = document.getElementById("new-category");
      if (categoryEl) formData.append("category_id", categoryEl.value);
      var supplierEl = document.getElementById("new-supplier");
      if (supplierEl) formData.append("supplier_id", supplierEl.value);
      var unitEl = document.getElementById("new-unit");
      if (unitEl) formData.append("unit_id", unitEl.value);
      var currencyEl = document.getElementById("new-currency");
      if (currencyEl) formData.append("currency_id", currencyEl.value);
      var subcategoryEl = document.getElementById("new-subcategory");
      if (subcategoryEl) formData.append("subcategory_id", subcategoryEl.value);
      var desiredStockEl = document.getElementById("new-desired-stock");
      if (desiredStockEl)
        formData.append("desired_stock", desiredStockEl.value);
      var statusEl = document.getElementById("new-status");
      if (statusEl) formData.append("status", statusEl.value);
      var imageEl = document.getElementById("new-image");
      if (imageEl && imageEl.files && imageEl.files.length > 0) {
        formData.append("image_file", imageEl.files[0]);
      }
      // Validación mínima
      if (!formData.get("product_code") || !formData.get("product_name")) {
        Swal.fire({
          icon: "warning",
          title: "Código y nombre obligatorios",
          toast: true,
          position: "top-end",
          timer: 3000,
          showConfirmButton: false,
        });
        return;
      }

      // NUEVOS campos (add)
      var salePriceEl = document.getElementById("new-sale-price");
      if (salePriceEl) formData.append("sale_price", salePriceEl.value);
      var weightEl = document.getElementById("new-weight");
      if (weightEl) formData.append("weight", weightEl.value);
      var heightEl = document.getElementById("new-height");
      if (heightEl) formData.append("height", heightEl.value);
      var lengthEl = document.getElementById("new-length");
      if (lengthEl) formData.append("length", lengthEl.value);
      var widthEl = document.getElementById("new-width");
      if (widthEl) formData.append("width", widthEl.value);
      var diameterEl = document.getElementById("new-diameter");
      if (diameterEl) formData.append("diameter", diameterEl.value);

      fetch(BASE_URL + "api/products.php?action=create", {
        method: "POST",
        body: formData,
      })
        .then((res) => {
          if (!res.ok) {
            return res.text().then((text) => {
              console.error(
                "Error al crear producto. Status:",
                res.status,
                "Body:",
                text
              );
              throw new Error("Error al crear producto");
            });
          }
          return res.json();
        })
        .then((data) => {
          if (data.success) {
            Swal.fire({
              icon: "success",
              title: "Producto registrado con éxito",
              toast: true,
              position: "top-end",
              timer: 2000,
              showConfirmButton: false,
            });
            closeModalAndFocusTarget("addProductModal", "addProductBtn");
            // Recargar página 1 para ver el nuevo registro
            //table.setData("api/products.php?action=list"); // Recargar la tabla aquí
            table.setPage(1);
            table.replaceData();
          } else {
            Swal.fire({
              icon: "error",
              title: "Error al crear producto",
              text: data.message || "",
            });
          }
        })
        .catch((err) => {
          console.error(err);
          Swal.fire({ icon: "error", title: "Error en creación" });
        });
    });
  }

  // === CRUD: editar producto ===
  var saveEditProductBtn = document.getElementById("saveEditProductBtn");
  if (saveEditProductBtn) {
    saveEditProductBtn.addEventListener("click", function () {
      var formData = new FormData();
      // Recoger valores de inputs del modal
      var idEl = document.getElementById("edit-product-id");
      if (idEl) formData.append("product_id", idEl.value);
      var codeEl = document.getElementById("edit-product-code");
      if (codeEl) formData.append("product_code", codeEl.value.trim());
      var barcodeEl2 = document.getElementById("edit-barcode");
      if (barcodeEl2) formData.append("barcode", barcodeEl2.value.trim());
      var nameEl = document.getElementById("edit-product-name");
      if (nameEl) formData.append("product_name", nameEl.value.trim());
      var descEl = document.getElementById("edit-product-description");
      if (descEl) formData.append("product_description", descEl.value.trim());
      var locationEl = document.getElementById("edit-location");
      if (locationEl) formData.append("location", locationEl.value.trim());
      var priceEl = document.getElementById("edit-price");
      if (priceEl) formData.append("price", priceEl.value);
      var stockEl = document.getElementById("edit-stock");
      if (stockEl) formData.append("stock", stockEl.value);
      var categoryEl2 = document.getElementById("edit-category");
      if (categoryEl2) formData.append("category_id", categoryEl2.value);
      var supplierEl2 = document.getElementById("edit-supplier");
      if (supplierEl2) formData.append("supplier_id", supplierEl2.value);
      var unitEl2 = document.getElementById("edit-unit");
      if (unitEl2) formData.append("unit_id", unitEl2.value);
      var currencyEl2 = document.getElementById("edit-currency");
      if (currencyEl2) formData.append("currency_id", currencyEl2.value);
      var subcategoryEl2 = document.getElementById("edit-subcategory");
      if (subcategoryEl2)
        formData.append("subcategory_id", subcategoryEl2.value);
      var desiredStockEl2 = document.getElementById("edit-desired-stock");
      if (desiredStockEl2)
        formData.append("desired_stock", desiredStockEl2.value);
      var statusEl2 = document.getElementById("edit-status");
      if (statusEl2) formData.append("status", statusEl2.value);
      var imageEl2 = document.getElementById("edit-image");
      if (imageEl2 && imageEl2.files && imageEl2.files.length > 0) {
        formData.append("image_file", imageEl2.files[0]);
      }

      // NUEVOS campos (edit)
      var salePriceEl2 = document.getElementById("edit-sale-price");
      if (salePriceEl2) formData.append("sale_price", salePriceEl2.value);
      var weightEl2 = document.getElementById("edit-weight");
      if (weightEl2) formData.append("weight", weightEl2.value);
      var heightEl2 = document.getElementById("edit-height");
      if (heightEl2) formData.append("height", heightEl2.value);
      var lengthEl2 = document.getElementById("edit-length");
      if (lengthEl2) formData.append("length", lengthEl2.value);
      var widthEl2 = document.getElementById("edit-width");
      if (widthEl2) formData.append("width", widthEl2.value);
      var diameterEl2 = document.getElementById("edit-diameter");
      if (diameterEl2) formData.append("diameter", diameterEl2.value);

      // Validación mínima
      if (
        !formData.get("product_id") ||
        !formData.get("product_code") ||
        !formData.get("product_name")
      ) {
        Swal.fire({
          icon: "warning",
          title: "Código y nombre obligatorios",
          toast: true,
          position: "top-end",
          timer: 3000,
          showConfirmButton: false,
        });
        return;
      }
      fetch(BASE_URL + "api/products.php?action=update", {
        method: "POST",
        body: formData,
      })
        .then((res) => {
          if (!res.ok) {
            return res.text().then((text) => {
              console.error(
                "Error al actualizar producto. Status:",
                res.status,
                "Body:",
                text
              );
              throw new Error("Error al actualizar producto");
            });
          }
          return res.json();
        })
        .then((data) => {
          if (data.success) {
            Swal.fire({
              icon: "success",
              title: "Producto actualizado con éxito",
              toast: true,
              position: "top-end",
              timer: 2000,
              showConfirmButton: false,
            });
            closeModalAndFocusTarget("editProductModal", "table-search");
            // Recarga la misma página para reflejar cambios
            //table.setData("api/products.php?action=list"); // Recargar aquí también
            table.setPage(1);
            table.replaceData();
          } else {
            Swal.fire({
              icon: "error",
              title: "Error al actualizar",
              text: data.message || "",
            });
          }
        })
        .catch((err) => {
          console.error(err);
          Swal.fire({ icon: "error", title: "Error en edición" });
        });
    });
  }



  // EXPORTAR CSV
  var exportCSVBtn = document.getElementById("exportCSVBtn");
  if (exportCSVBtn) {
    exportCSVBtn.addEventListener("click", function () {
      // Obtiene los datos de la página actualmente cargada en memoria
      var datos = table.getData();
      let csvContent = "";
      csvContent += `"REPORTE DE LISTA DE PRODUCTOS"\n`;
      csvContent += `"Formato: L001"\n\n`;
      // Encabezados
      const headers = [
        "ID",
        "Código",
        "Barcode",
        "Nombre",
        "Ubicación",
        "Precio",
        "Stock",
        "Registrado",
      ];
      csvContent += headers.join(",") + "\n";
      datos.forEach((row) => {
        let fecha = "";
        if (row.registration_date) {
          const d = new Date(row.registration_date);
          if (!isNaN(d.getTime())) {
            const day = String(d.getDate()).padStart(2, "0");
            const month = String(d.getMonth() + 1).padStart(2, "0");
            const year = d.getFullYear();
            fecha = `${day}/${month}/${year}`;
          }
        }
        csvContent +=
          [
            row.product_id,
            `"${row.product_code}"`,
            `"${row.barcode || ""}"`,
            `"${row.product_name}"`,
            `"${row.location}"`,
            row.price,
            row.stock,
            `"${fecha}"`,
          ].join(",") + "\n";
      });
      const blob = new Blob([csvContent], {
        type: "text/csv;charset=utf-8;",
      });
      const url = URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url;
      a.download = "productos.csv";
      a.click();
      URL.revokeObjectURL(url);
    });
  }

  // EXPORTAR EXCEL
  var exportExcelBtn = document.getElementById("exportExcelBtn");
  if (exportExcelBtn) {
    exportExcelBtn.addEventListener("click", function () {
      const dataToExport = table.getData().map((row) => {
        const { /*image_url,*/ ...filtered } = row;
        return filtered;
      });
      table.download("xlsx", "productos.xlsx", {
        sheetName: "Reporte Productos",
        documentProcessing: function (workbook) {
          const sheet = workbook.Sheets["Reporte Productos"];
          sheet["A1"].s = { font: { bold: true } };
          return workbook;
        },
        rows: dataToExport,
      });
    });
  }

  // EXPORTAR JSON
  var exportJSONBtn = document.getElementById("exportJSONBtn");
  if (exportJSONBtn) {
    exportJSONBtn.addEventListener("click", function () {
      table.download("json", "productos.json");
    });
  }

  // EXPORTAR PDF
  var exportPDFBtn = document.getElementById("exportPDFBtn");
  if (exportPDFBtn) {
    exportPDFBtn.addEventListener("click", function () {
      console.log("Botón de exportación PDF presionado.");
      try {
        if (!table) {
          console.error("El objeto 'table' no está definido.");
          return;
        }
        table.download("pdf", "productos.pdf", {
          orientation: "landscape",
          autoTable: {
            styles: {
              fontSize: 8,
              cellPadding: 2,
              halign: "center",
            },
            margin: { top: 70, left: 10, right: 10 },
            headStyles: {
              fillColor: [22, 160, 133],
              textColor: 255,
              fontStyle: "bold",
              halign: "center",
            },
            bodyStyles: {
              halign: "center",
            },
            theme: "striped",
            columns: [
              { header: "ID", dataKey: "product_id" },
              { header: "Código", dataKey: "product_code" },
              { header: "Código Barras", dataKey: "barcode" },
              { header: "Nombre", dataKey: "product_name" },
              { header: "Ubicación", dataKey: "location" },
              { header: "Precio", dataKey: "price" },
              { header: "Stock", dataKey: "stock" },
              { header: "Registrado", dataKey: "registration_date" },
            ],
            didDrawPage: function (data) {
              const doc = data.doc;
              const pageWidth = doc.internal.pageSize.getWidth();
              let y = 25;
              // TÍTULO CENTRADO
              doc.setFontSize(16);
              doc.setFont(undefined, "bold");
              doc.text("REPORTE DE LISTA DE PRODUCTOS", pageWidth / 2, y, {
                align: "center",
              });
              y += 10;
              // FORMATO
              doc.setFontSize(10);
              doc.setFont(undefined, "normal");
              doc.text("Formato: L001", pageWidth / 2, y, { align: "center" });
              // Fecha generación
              y += 10;
              doc.setFontSize(9);
              doc.text(
                "Generado: " + new Date().toLocaleDateString(),
                data.settings.margin.left,
                y
              );
            },
          },
        });
      } catch (e) {
        console.error("Error en el handler de exportación PDF:", e);
      }
    });
  }



});




