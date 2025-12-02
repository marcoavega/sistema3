// assets/js/modals_stock.js  (REEMPLAZAR COMPLETO)
document.addEventListener("DOMContentLoaded", () => {
    const DEBUG = true;
    const BASE = (typeof BASE_URL !== "undefined") ? BASE_URL : "/";
  
    function log(...args){ if(DEBUG) console.debug("[modals_stock]", ...args); }
    function toast(msg, err=false){
      const t = document.createElement("div");
      t.textContent = msg;
      t.style.position = "fixed";
      t.style.right = "20px";
      t.style.bottom = "20px";
      t.style.padding = "8px 12px";
      t.style.borderRadius = "8px";
      t.style.zIndex = 12000;
      t.style.background = err ? "#dc3545" : "#198754";
      t.style.color = "#fff";
      document.body.appendChild(t);
      setTimeout(()=> t.remove(), 3500);
    }
  
    function findSpinner(btn){
      if(!btn) return null;
      return btn.querySelector(".spinner-border");
    }
    function setButtonLoading(btn, loading){
      if(!btn) return;
      btn.disabled = loading;
      const s = findSpinner(btn);
      if(s) s.classList.toggle("d-none", !loading);
    }
  
    function updateStockUI(productId, stocks){
      try{
        if(!Array.isArray(stocks)) return;
        // total
        const totalEl = document.querySelector(".product-stock");
        if(totalEl){
          const total = stocks.reduce((acc,it)=> acc + (Number(it.stock)||0), 0);
          totalEl.textContent = total;
        }
        // por almacén
        stocks.forEach(it=>{
          const tr = document.querySelector(`tr[data-warehouse-id="${it.warehouse_id}"]`);
          if(tr){
            const td = tr.querySelector('[data-stock-cell]') || tr.children[1];
            if(td) td.textContent = Number(it.stock) || 0;
          }
        });
      }catch(e){ console.error("updateStockUI", e); }
    }
  
    async function postAction(action, formData){
      const url = `${BASE}api/stock.php?action=${encodeURIComponent(action)}`;
      log("POST", url, Array.from(formData.entries()));
      const resp = await fetch(url, { method: "POST", body: formData });
      const text = await resp.text();
      log("server raw:", text);
      let json;
      try { json = JSON.parse(text); } catch(e){ throw new Error("Respuesta inválida del servidor"); }
      if(!resp.ok) throw new Error(json.message || "Error servidor");
      if(!json.success) throw new Error(json.message || "Operación no completada");
      return json;
    }
  
    // Maneja la lógica general de envío (action = entry|exit|transfer)
    async function handleSend({ action, form, modalEl, button }) {
      const bsModal = modalEl ? bootstrap.Modal.getOrCreateInstance(modalEl) : null;
      setButtonLoading(button, true);
      try {
        const fd = new FormData(form);
  
        // Validaciones comunes
        const product_id = fd.get("product_id");
        if(!product_id) throw new Error("Falta product_id");
  
        if(action === "entry" || action === "exit") {
          const warehouse_id = fd.get("warehouse_id");
          if(!warehouse_id) throw new Error("Selecciona un almacén");
          const qty = Number(fd.get("quantity") || 0);
          if(!qty || qty <= 0) throw new Error("Cantidad inválida (>0)");
        } else if(action === "transfer") {
          const from = fd.get("from_warehouse_id");
          const to = fd.get("to_warehouse_id");
          const qty = Number(fd.get("quantity") || 0);
          if(!from || !to) throw new Error("Selecciona origen y destino");
          if(from === to) throw new Error("Origen y destino deben ser diferentes");
          if(!qty || qty <= 0) throw new Error("Cantidad inválida (>0)");
        }
  
        const res = await postAction(action, fd);
  
        // Actualizar UI en tiempo real
        if(res.updated_stocks) updateStockUI(product_id, res.updated_stocks);
  
        // Mensaje y cerrar modal
        toast(res.message || "Operación registrada");
        form.reset();
        if(bsModal) bsModal.hide();
      } catch(err) {
        console.error("stock send error", err);
        toast(err.message || "Error", true);
      } finally {
        setButtonLoading(button, false);
      }
    }
  
    // Engancha botón (type=button) o submit (type=submit) y fallback submit
    function bindFormWithButton({ formId, modalId, action, btnSelector }) {
      const form = document.getElementById(formId);
      const modalEl = document.getElementById(modalId);
      if(!form) { log("Form no existe:", formId); return; }
  
      // 1) Si hay un botón con data-action o clase, usar click
      let btn = null;
      if(btnSelector) btn = document.querySelector(btnSelector);
      if(!btn) {
        // intenta buscar dentro del form
        btn = form.querySelector(btnSelector) || form.querySelector('button[type="submit"]') || form.querySelector('button');
      }
  
      // si el form tiene onsubmit="return false;" no confíes en submit; engancha click al botón
      if(btn){
        btn.addEventListener("click", (ev) => {
          ev.preventDefault();
          ev.stopPropagation();
          log("click boton", formId, action);
          handleSend({ action, form, modalEl, button: btn });
        });
      }
  
      // fallback: si el formulario se envía (por ejemplo por Enter) capturarlo
      form.addEventListener("submit", (ev) => {
        ev.preventDefault();
        ev.stopPropagation();
        log("submit form", formId, action);
        // usar el primer botón disponible como referencia para spinner/disable
        const btnRef = btn || form.querySelector('button[type="submit"]') || form.querySelector('button');
        handleSend({ action, form, modalEl, button: btnRef });
      });
    }
  
    // Bind de los tres formularios (usa selectores de tus botones)
    bindFormWithButton({ formId: "formStockEntry", modalId: "modalStockEntry", action: "entry", btnSelector: ".js-submit-entry" });
    bindFormWithButton({ formId: "formStockExit", modalId: "modalStockExit", action: "exit", btnSelector: ".js-submit-exit" });
    bindFormWithButton({ formId: "formStockTransfer", modalId: "modalStockTransfer", action: "transfer", btnSelector: ".js-submit-transfer" });
  
    log("modals_stock.js inicializado");
  });
  