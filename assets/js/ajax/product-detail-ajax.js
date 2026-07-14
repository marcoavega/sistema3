// assets/js/ajax/product-detail-ajax.js
(function () {
    if (typeof window === 'undefined') return;
  
    // Espera DOM listo
    document.addEventListener('DOMContentLoaded', function () {
  
      // --- datos iniciales desde la vista ---
      const initial = window.PRODUCT_DATA || {};
      const product = initial.product || {};
  
      // Elementos del modal editar (deben existir en la vista/modals)
      const modalEl = document.getElementById("editProductModal");
      const form = document.getElementById("editProductForm");
      const saveBtn = document.getElementById("saveEditProductBtn");
  
      // Función toast simple
      function toast(msg, err = false) {
        const t = document.createElement("div");
        t.textContent = msg;
        t.style.position = "fixed";
        t.style.bottom = "25px";
        t.style.right = "25px";
        t.style.padding = "10px 15px";
        t.style.borderRadius = "8px";
        t.style.background = err ? "#dc3545" : "#198754";
        t.style.color = "white";
        t.style.zIndex = 9999;
        document.body.appendChild(t);
        setTimeout(() => t.remove(), 3000);
      }
  
      // Map de ids -> valores que la vista usa para rellenar el form
      const productMap = {
        "edit-product-id": product.product_id || '',
        "edit-product-code": product.product_code || '',
        "edit-barcode": product.barcode || '',
        "edit-product-name": product.product_name || '',
        "edit-product-description": product.product_description || '',
        "edit-price": product.price || '',
        "edit-stock": product.stock || '',
        "edit-desired-stock": product.desired_stock || '',
        "edit-location": product.location || '',
        "edit-category": product.category_id || '',
        "edit-subcategory": product.subcategory_id || '',
        "edit-unit": product.unit_id || '',
        "edit-currency": product.currency_id || '',
        "edit-supplier": product.supplier_id || '',
        "edit-status": (typeof product.status !== 'undefined') ? (parseInt(product.status) ? '1' : '0') : '1',
        "edit-sale-price": product.sale_price || '',
        "edit-weight": product.weight || '',
        "edit-height": product.height || '',
        "edit-length": product.length || '',
        "edit-width": product.width || '',
        "edit-diameter": product.diameter || ''
      };
  
      function fillForm() {
        if (!form) return;
        for (const id in productMap) {
          const el = document.getElementById(id);
          if (el) el.value = (productMap[id] !== null && typeof productMap[id] !== "undefined") ? productMap[id] : "";
        }
      }
  
      if (modalEl) {
        modalEl.addEventListener("show.bs.modal", fillForm);
      }
  
      // Envío del formulario (botón guardar)
      if (saveBtn && form) {
        saveBtn.addEventListener("click", async function (e) {
          e.preventDefault();
          saveBtn.disabled = true;
          const spinner = saveBtn.querySelector(".spinner-border");
          if (spinner) spinner.classList.remove("d-none");
  
          try {
            const fd = new FormData(form);
            if (!fd.get("product_id") && !fd.get("product_id")) {
              toast("ID de producto faltante", true);
              return;
            }
  
            const resp = await fetch(`${BASE_URL}api/products.php?action=update`, {
              method: "POST",
              body: fd
            });
  
            const txt = await resp.text();
  
            if (!resp.ok) {
              console.error("Respuesta no OK:", resp.status, txt);
              toast("Error al actualizar el producto.", true);
              return;
            }
  
            let data;
            try {
              data = JSON.parse(txt);
            } catch (err) {
              console.error("JSON parse error:", err, txt);
              toast("Respuesta no válida del servidor.", true);
              return;
            }
  
            if (!data.success) {
              toast(data.message || "No se pudo actualizar.", true);
              return;
            }
  
            const u = data.product || {};
  
            // Cerrar modal
            bootstrap.Modal.getInstance(modalEl)?.hide();
  
            // Actualizar UI principal: nombre, código, precio, stock, badge, imagen...
            document.querySelectorAll(".product-name").forEach(el => el.textContent = u.product_name || form["product_name"].value);
            const codigoEl = document.querySelector(".product-code");
            if (codigoEl && (u.product_code || form["product_code"].value)) {
              codigoEl.innerHTML = `<i class="bi bi-upc-scan me-2"></i>Código: ${u.product_code || form["product_code"].value}`;
            }
  
            const newPrice = (typeof u.price !== 'undefined' && u.price !== null) ? parseFloat(u.price) : parseFloat(form["price"].value || 0);
            const newStock = (typeof u.stock !== 'undefined' && u.stock !== null) ? u.stock : form["stock"].value;
  
            const productPriceEl = document.querySelector(".product-price");
            if (productPriceEl) productPriceEl.textContent = `$${newPrice.toFixed(2)}`;
  
            const productStockEl = document.querySelector(".product-stock");
            if (productStockEl) productStockEl.textContent = newStock;
  
            // Estado badge
            const statusBadge = document.getElementById('productStatusBadge');
            const statusFromServer = (typeof u.status !== 'undefined') ? u.status : null;
            let statusVal = null;
            if (statusFromServer !== null) {
              statusVal = parseInt(statusFromServer) === 1 ? 1 : 0;
            } else {
              const s = form.querySelector('#edit-status')?.value;
              statusVal = (s === '1' || s === 1) ? 1 : 0;
            }
  
            if (statusBadge) {
              statusBadge.classList.remove('bg-success', 'bg-warning', 'text-white');
              if (statusVal === 1) {
                statusBadge.classList.add('bg-success', 'text-white');
                statusBadge.innerHTML = `<i class="bi bi-check-circle me-1"></i> Activo`;
              } else {
                statusBadge.classList.add('bg-warning', 'text-white');
                statusBadge.innerHTML = `<i class="bi bi-exclamation-triangle me-1"></i> Inactivo`;
              }
            }
  
            // Imagen (si viene la url)
            if (u.image_url) {
              const img = document.getElementById("productImage");
              if (img) img.src = `${BASE_URL}${u.image_url}?v=${Date.now()}`;
            }
  
            // Actualizar campos descriptivos
            const detailBarcode = document.getElementById('detail-barcode');
            if (detailBarcode) {
              const newBarcode = (typeof u.barcode !== 'undefined' && u.barcode !== null && u.barcode !== '') ?
                u.barcode :
                (form["barcode"]?.value ?? '');
              detailBarcode.textContent = newBarcode;
            }
  
            const detailLocation = document.getElementById('detail-location');
            if (detailLocation) {
              const newLocation = (typeof u.location !== 'undefined' && u.location !== null && u.location !== '') ?
                u.location :
                (form["location"]?.value ?? '');
              detailLocation.textContent = newLocation;
            }
  
            const detailDescription = document.getElementById('detail-description');
            if (detailDescription) {
              const newDesc = (typeof u.product_description !== 'undefined' && u.product_description !== null) ?
                u.product_description :
                (form["product_description"]?.value ?? '');
              detailDescription.innerHTML = newDesc.replace(/\n/g, "<br>");
            }
  
            // Precio venta, margen, dimensiones y peso
            const detailPriceBase = document.getElementById("detail-price-base");
            if (detailPriceBase) detailPriceBase.textContent = `$${(newPrice).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
  
            const salePriceVal = (typeof u.sale_price !== 'undefined' && u.sale_price !== null && u.sale_price !== '') ? parseFloat(u.sale_price) : (form["sale_price"] ? parseFloat(form["sale_price"].value || 0) : null);
            const detailSalePrice = document.getElementById("detail-sale-price");
            if (detailSalePrice) {
              if (salePriceVal !== null && !isNaN(salePriceVal)) {
                detailSalePrice.textContent = `$${salePriceVal.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
              } else {
                detailSalePrice.textContent = 'N/A';
              }
            }
  
            const detailMargin = document.getElementById("detail-margin");
            if (detailMargin) {
              if (salePriceVal !== null && !isNaN(salePriceVal) && newPrice > 0) {
                const margin = ((salePriceVal - newPrice) / newPrice) * 100;
                detailMargin.textContent = `${margin.toFixed(1)}%`;
              } else {
                detailMargin.textContent = 'N/A';
              }
            }
  
            const setDetail = (id, value, suffix = '') => {
              const el = document.getElementById(id);
              if (!el) return;
              if (value === null || typeof value === 'undefined' || value === '') {
                el.textContent = 'N/A';
              } else {
                const n = parseFloat(value);
                el.textContent = (!isNaN(n)) ? n.toString() + suffix : String(value) + suffix;
              }
            };
  
            setDetail('detail-height', u.height ?? form["height"]?.value ?? '', ' cm');
            setDetail('detail-length', u.length ?? form["length"]?.value ?? '', ' cm');
            setDetail('detail-width', u.width ?? form["width"]?.value ?? '', ' cm');
            setDetail('detail-diameter', u.diameter ?? form["diameter"]?.value ?? '', ' cm');
  
            const weightVal = (typeof u.weight !== 'undefined' && u.weight !== null && u.weight !== '') ? u.weight : form["weight"]?.value ?? null;
            const detailWeight = document.getElementById("detail-weight");
            if (detailWeight) {
              if (weightVal === null || weightVal === '' || typeof weightVal === 'undefined') {
                detailWeight.textContent = 'N/A';
              } else {
                const w = parseFloat(weightVal);
                detailWeight.textContent = (!isNaN(w)) ? `${w} kg` : `${weightVal} kg`;
              }
            }
  
            toast("Producto actualizado correctamente.");
          } catch (err) {
            console.error(err);
            toast("Error de conexión con el servidor.", true);
          } finally {
            saveBtn.disabled = false;
            const spinner2 = saveBtn.querySelector(".spinner-border");
            if (spinner2) spinner2.classList.add("d-none");
          }
        });
      }
  
      // --- Zoom/preview de imágenes ---
      (function attachZoom() {
        const zoomableSelector = ".zoomable, #productImage";
        const previewModalEl = document.getElementById("imagePreviewModal");
        const previewImg = document.getElementById("imagePreviewModalImg");
        if (!previewModalEl || !previewImg) return;
  
        const modalInstance = new bootstrap.Modal(previewModalEl, { keyboard: true, backdrop: true });
  
        function attachZoomListeners() {
          const imgs = document.querySelectorAll(zoomableSelector);
          imgs.forEach(img => {
            if (img.dataset.zoomAttached) return;
            img.dataset.zoomAttached = "1";
            img.addEventListener("click", (e) => {
              const src = img.getAttribute("src") || img.dataset.src;
              if (!src) return;
              const cacheBusted = src + (src.includes('?') ? '&' : '?') + 'v=' + Date.now();
              previewImg.src = cacheBusted;
              previewImg.alt = img.alt || 'Imagen del producto';
              modalInstance.show();
            });
          });
        }
  
        attachZoomListeners();
      })();
  
    }); // DOMContentLoaded
  })();
  