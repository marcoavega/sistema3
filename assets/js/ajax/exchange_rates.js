// assets/js/ajax/exchange_rates.js
document.addEventListener('DOMContentLoaded', function () {
    const API = `${BASE_URL}api/exchange_rates.php`;
    const currenciesListEl = document.querySelector('#currencies-list'); // asumiremos id en el HTML
    const ratesTbody = document.querySelector('#rates-tbody'); // asumiremos id en la tabla
    const modalAddCurrency = document.getElementById('modalAddCurrency');
    const modalAddRate     = document.getElementById('modalAddRate');
    const bsModalAddCurrency = modalAddCurrency ? new bootstrap.Modal(modalAddCurrency) : null;
    const bsModalAddRate     = modalAddRate ? new bootstrap.Modal(modalAddRate) : null;
  
    function showToast(msg, err=false) {
      const t = document.createElement('div');
      t.textContent = msg;
      t.style.position = 'fixed';
      t.style.bottom = '25px';
      t.style.right = '25px';
      t.style.padding = '10px 15px';
      t.style.borderRadius = '8px';
      t.style.background = err ? '#dc3545' : '#198754';
      t.style.color = 'white';
      t.style.zIndex = 9999;
      document.body.appendChild(t);
      setTimeout(() => t.remove(), 2500);
    }
  
    async function fetchJSON(url, opts = {}) {
      const res = await fetch(url, opts);
      if (!res.ok) {
        const text = await res.text().catch(()=>null);
        throw new Error(text || `HTTP ${res.status}`);
      }
      return await res.json();
    }
  
    async function loadCurrencies() {
      try {
        const j = await fetchJSON(`${API}?action=list_currencies`);
        if (!j.success) throw new Error(j.message || 'Error al cargar monedas');
        renderCurrencies(j.data || []);
      } catch (err) {
        console.error(err);
        showToast('Error al cargar monedas', true);
      }
    }
  
    async function loadRates() {
      try {
        const j = await fetchJSON(`${API}?action=list_rates`);
        if (!j.success) throw new Error(j.message || 'Error al cargar tipos');
        renderRates(j.data || []);
      } catch (err) {
        console.error(err);
        showToast('Error al cargar tipos de cambio', true);
      }
    }
  
    function renderCurrencies(items) {
      // si en el HTML usaste <ul id="currencies-list"> y <select name="currency_id">
      const ul = document.querySelector('#currencies-list');
      const sel = document.querySelector('select[name="currency_id"]');
      if (ul) {
        ul.innerHTML = '';
        if (items.length === 0) {
          ul.innerHTML = '<li class="list-group-item text-muted">No hay monedas registradas.</li>';
        } else {
          items.forEach(c => {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            li.innerHTML = `
              <div>
                <strong>${escapeHtml(c.currency_code)}</strong>
                <div class="small text-muted">${escapeHtml(c.currency_name)} — ${escapeHtml(c.country)}</div>
              </div>
              <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-danger btn-delete-currency" data-id="${c.currency_id}"><i class="bi bi-trash"></i></button>
              </div>
            `;
            ul.appendChild(li);
          });
        }
      }
      if (sel) {
        sel.innerHTML = `<option value="">Selecciona...</option>`;
        items.forEach(c => {
          const o = document.createElement('option');
          o.value = c.currency_id;
          o.textContent = `${c.currency_code} — ${c.currency_name}`;
          sel.appendChild(o);
        });
      }
      // bind delete buttons
      document.querySelectorAll('.btn-delete-currency').forEach(btn=>{
        btn.removeEventListener('click', onDeleteCurrencyClick);
        btn.addEventListener('click', onDeleteCurrencyClick);
      });
    }
  
    function renderRates(items) {
      const tbody = document.querySelector('#rates-tbody');
      if (!tbody) return;
      tbody.innerHTML = '';
      if (!Array.isArray(items) || items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No hay registros de tipo de cambio.</td></tr>';
        return;
      }
      items.forEach(r=>{
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${escapeHtml(r.rate_date)}</td>
          <td>${escapeHtml(r.currency_code)} — ${escapeHtml(r.currency_name)}</td>
          <td class="fw-bold">$${Number(r.rate).toFixed(6)}</td>
          <td>${escapeHtml(r.notes || '')}</td>
          <td class="small text-muted">${escapeHtml(r.created_at)}</td>
          <td class="text-end">
            <button class="btn btn-sm btn-outline-danger btn-delete-rate" data-id="${r.rate_id}"><i class="bi bi-trash"></i></button>
          </td>
        `;
        tbody.appendChild(tr);
      });
  
      document.querySelectorAll('.btn-delete-rate').forEach(btn=>{
        btn.removeEventListener('click', onDeleteRateClick);
        btn.addEventListener('click', onDeleteRateClick);
      });
    }
  
    // handlers
    async function onDeleteCurrencyClick(e) {
      const id = this.dataset.id;
      if (!confirm('Eliminar moneda y sus tipos de cambio?')) return;
    
      try {
        const j = await fetchJSON(API + '?action=delete_currency', {
          method: 'POST',
          body: JSON.stringify({ currency_id: id }),
          headers: { 'Content-Type': 'application/json' },
        });
    
        if (!j.success) throw new Error(j.message || 'No se pudo borrar moneda');
    
        showToast('Moneda eliminada');
        await loadCurrencies();
        await loadRates();
      } catch (err) {
        console.error(err);
        showToast('Error al eliminar moneda', true);
      }
    }
    
  
    async function onDeleteRateClick(e) {
      const id = this.dataset.id;
      if (!confirm('Eliminar este registro?')) return;
      try {
        const j = await fetchJSON(API + '?action=delete_rate', { method: 'POST', body: JSON.stringify({rate_id: id}), headers:{'Content-Type':'application/json'} });
        if (!j.success) throw new Error(j.message || 'No se pudo borrar');
        showToast('Registro eliminado');
        await loadRates();
      } catch (err) {
        console.error(err);
        showToast('Error al borrar registro', true);
      }
    }
  
    // submit forms
    const formAddCurrency = document.getElementById('formAddCurrency');
    if (formAddCurrency) {
      formAddCurrency.addEventListener('submit', async function (e) {
        e.preventDefault();
        const fd = new FormData(formAddCurrency);
        const payload = Object.fromEntries(fd.entries());
        try {
          const j = await fetchJSON(API + '?action=create_currency', { method: 'POST', body: JSON.stringify(payload), headers:{'Content-Type':'application/json'} });
          if (!j.success) throw new Error(j.message || 'Error al crear moneda');
          showToast('Moneda creada');
          formAddCurrency.reset();
          if (bsModalAddCurrency) bsModalAddCurrency.hide();
          await loadCurrencies();
        } catch (err) {
          console.error(err);
          showToast(err.message || 'Error', true);
        }
      });
    }
  
    const formAddRate = document.getElementById('formAddRate');
    if (formAddRate) {
      formAddRate.addEventListener('submit', async function (e) {
        e.preventDefault();
        const fd = new FormData(formAddRate);
        const payload = Object.fromEntries(fd.entries());
        try {
          const j = await fetchJSON(API + '?action=create_rate', { method: 'POST', body: JSON.stringify(payload), headers:{'Content-Type':'application/json'} });
          if (!j.success) throw new Error(j.message || 'Error al crear tipo de cambio');
          showToast('Tipo de cambio registrado');
          formAddRate.reset();
          if (bsModalAddRate) bsModalAddRate.hide();
          await loadRates();
          await loadCurrencies(); // por si quieres ver lastRates
        } catch (err) {
          console.error(err);
          showToast(err.message || 'Error', true);
        }
      });
    }
  
    function escapeHtml(str='') {
      return String(str).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }
  
    // inicializar: cargar desde API
    loadCurrencies();
    loadRates();
  });
  