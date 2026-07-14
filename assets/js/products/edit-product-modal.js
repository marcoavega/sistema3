document.addEventListener("DOMContentLoaded", () => {

    const modalEl = document.getElementById("editProductModal");
    const form = document.getElementById("editProductForm");
    const saveBtn = document.getElementById("saveEditProductBtn");
  
    if (!modalEl || !form || !saveBtn) return;
  
    const p = window.EDIT_PRODUCT_DATA || {};
  
    const productMap = {
      "edit-product-id": p.product_id ?? "",
      "edit-product-code": p.product_code ?? "",
      "edit-barcode": p.barcode ?? "",
      "edit-product-name": p.product_name ?? "",
      "edit-product-description": p.product_description ?? "",
      "edit-price": p.price ?? "",
      "edit-stock": p.stock ?? "",
      "edit-desired-stock": p.desired_stock ?? "",
      "edit-location": p.location ?? "",
      "edit-category": p.category_id ?? "",
      "edit-subcategory": p.subcategory_id ?? "",
      "edit-unit": p.unit_id ?? "",
      "edit-currency": p.currency_id ?? "",
      "edit-supplier": p.supplier_id ?? "",
      "edit-status": p.status ?? "",
  
      "edit-sale-price": p.sale_price ?? "",
      "edit-weight": p.weight ?? "",
      "edit-height": p.height ?? "",
      "edit-length": p.length ?? "",
      "edit-width": p.width ?? "",
      "edit-diameter": p.diameter ?? ""
    };
  
    const fillForm = () => {
      for (const id in productMap) {
        const el = document.getElementById(id);
        if (el) el.value = productMap[id];
      }
    };
  
    modalEl.addEventListener("show.bs.modal", fillForm);

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
  
      saveBtn.addEventListener("click", async (e) => {
        e.preventDefault();
        saveBtn.disabled = true;
        const spinner = saveBtn.querySelector(".spinner-border");
        if (spinner) spinner.classList.remove("d-none");
  
        try {
          const fd = new FormData(form);
          if (!fd.get("product_id")) {
            toast("ID de producto faltante", true);
            return;
          }
  
          const resp = await fetch(`${BASE_URL}api/products.php?action=update`, {
            method: "POST",
            body: fd
          });
  
          const txt = await resp.text();
  
          if (!resp.ok) {
            // mostrar error servidor (body)
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
  
          // --- Actualizaciones inmediatas en la UI principal ---
          // Nombre y código (ya los tenías)
          document.querySelectorAll(".product-name").forEach(el => el.textContent = u.product_name || form["product_name"].value);
          const codigoEl = document.querySelector(".product-code");
          if (codigoEl && (u.product_code || form["product_code"].value)) {
            codigoEl.innerHTML = `<i class="bi bi-upc-scan me-2"></i>Código: ${u.product_code || form["product_code"].value}`;
          }
  
          // Precio y stock (elementos existentes)
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
  
          // Imagen (si actualizó)
          if (u.image_url) {
            const img = document.getElementById("productImage");
            if (img) img.src = `${BASE_URL}${u.image_url}?v=${Date.now()}`;
          }
  
          // --- Actualizar Código de Barras ---
          const detailBarcode = document.getElementById('detail-barcode');
          if (detailBarcode) {
            const newBarcode = (typeof u.barcode !== 'undefined' && u.barcode !== null && u.barcode !== '') ?
              u.barcode :
              (form["barcode"]?.value ?? '');
            detailBarcode.textContent = newBarcode;
          }
  
          // --- Actualizar Ubicación ---
          const detailLocation = document.getElementById('detail-location');
          if (detailLocation) {
            const newLocation = (typeof u.location !== 'undefined' && u.location !== null && u.location !== '') ?
              u.location :
              (form["location"]?.value ?? '');
            detailLocation.textContent = newLocation;
          }
  
          // --- Actualizar Descripción ---
          const detailDescription = document.getElementById('detail-description');
          if (detailDescription) {
            const newDesc = (typeof u.product_description !== 'undefined' && u.product_description !== null) ?
              u.product_description :
              (form["product_description"]?.value ?? '');
  
            // mantener saltos de línea en HTML
            detailDescription.innerHTML = newDesc.replace(/\n/g, "<br>");
          }
  
          // --- ACTUALIZAR INFORMACIÓN DETALLADA (precio venta, margen, dimensiones, peso) ---
          // Precio base
          const detailPriceBase = document.getElementById("detail-price-base");
          if (detailPriceBase) detailPriceBase.textContent = `$${(newPrice).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
  
          // Precio venta
          const salePriceVal = (typeof u.sale_price !== 'undefined' && u.sale_price !== null && u.sale_price !== '') ? parseFloat(u.sale_price) : (form["sale_price"] ? parseFloat(form["sale_price"].value || 0) : null);
          const detailSalePrice = document.getElementById("detail-sale-price");
          if (detailSalePrice) {
            if (salePriceVal !== null && !isNaN(salePriceVal)) {
              detailSalePrice.textContent = `$${salePriceVal.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            } else {
              detailSalePrice.textContent = 'N/A';
            }
          }
  
          // Margen
          const detailMargin = document.getElementById("detail-margin");
          if (detailMargin) {
            if (salePriceVal !== null && !isNaN(salePriceVal) && newPrice > 0) {
              const margin = ((salePriceVal - newPrice) / newPrice) * 100;
              detailMargin.textContent = `${margin.toFixed(1)}%`;
            } else {
              detailMargin.textContent = 'N/A';
            }
          }
  
          // Dimensiones y peso
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
  
          // Peso (mostrar con 'kg')
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
    });