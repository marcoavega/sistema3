document.addEventListener('DOMContentLoaded', function () {
  const API = `${BASE_URL}api/exchange_rates.php`;
  const currenciesListEl = document.querySelector('#currencies-list');
  const ratesTbody = document.querySelector('#rates-tbody');
  const modalAddCurrency = document.getElementById('modalAddCurrency');
  const modalAddRate = document.getElementById('modalAddRate');
  const bsModalAddCurrency = modalAddCurrency ? new bootstrap.Modal(modalAddCurrency) : null;
  const bsModalAddRate = modalAddRate ? new bootstrap.Modal(modalAddRate) : null;

  function showToast(msg, err = false) {
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
      // Si el servidor responde 500, no lanzamos error de inmediato, 
      // primero intentamos leer el JSON para ver si es un duplicado.
      const j = await res.json().catch(() => ({ success: false, message: 'Error de servidor' }));
      return j;
  }

  async function loadCurrencies() {
      try {
          const j = await fetchJSON(`${API}?action=list_currencies`);
          if (!j.success) throw new Error(j.message);
          renderCurrencies(j.data || []);
      } catch (err) {
          console.error(err);
      }
  }

  async function loadRates() {
      try {
          const j = await fetchJSON(`${API}?action=list_rates`);
          if (!j.success) throw new Error(j.message);
          renderRates(j.data || []);
      } catch (err) {
          console.error(err);
      }
  }

  function renderCurrencies(items) {
    const ul = document.querySelector('#currencies-list');
    // Buscamos el select específicamente por su nombre dentro del modal de tasas
    const sel = document.querySelector('#formAddRate select[name="currency_id"]');
    
    if (ul) {
        ul.innerHTML = '';
        if (items.length === 0) {
            ul.innerHTML = '<li class="list-group-item text-muted">No hay monedas registradas.</li>';
        } else {
            items.forEach(c => {
                const li = document.createElement('li');
                li.className = 'list-group-item bg-transparent border-0 d-flex justify-content-between align-items-center p-3 mb-2 rounded-3 bg-body-secondary bg-opacity-25';
                li.innerHTML = `
                    <div>
                        <strong class="text-body">${escapeHtml(c.currency_code)}</strong>
                        <div class="small text-muted">${escapeHtml(c.currency_name)}</div>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-danger btn-delete-currency" data-id="${c.currency_id}">
                            <i class="bi bi-trash-fill"></i> Eliminar
                        </button>
                    </div>
                `;
                ul.appendChild(li);
            });
        }
    }
    // --- ACTUALIZACIÓN DEL DESPLEGABLE ---
    if (sel) {
      const valorActual = sel.value; // Guardamos lo que estaba seleccionado (por si acaso)
      sel.innerHTML = '<option value="">Selecciona...</option>';
      
      items.forEach(c => {
          const o = document.createElement('option');
          o.value = c.currency_id;
          o.textContent = `${escapeHtml(c.currency_code)} — ${escapeHtml(c.currency_name)}`;
          sel.appendChild(o);
      });
      
      // Si la moneda que estaba seleccionada sigue existiendo, la mantenemos
      sel.value = valorActual;
  }

  document.querySelectorAll('.btn-delete-currency').forEach(btn => {
      btn.onclick = onDeleteCurrencyClick;
  });
}

function renderRates(items) {
    if (!ratesTbody) return;
    ratesTbody.innerHTML = '';
    items.forEach(r => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
        <td class="ps-4 text-body">${escapeHtml(r.rate_date)}</td>
        <td class="text-body"><strong>${escapeHtml(r.currency_code)}</strong></td>
        <td><span class="badge bg-success-subtle text-success border border-success-subtle fs-6">$${Number(r.rate).toFixed(6)}</span></td>
        <td class="small text-muted italic">${escapeHtml(r.notes || '')}</td>
        <td class="text-end pe-4">
          <button class="btn btn-sm btn-danger btn-delete-rate" data-id="${r.rate_id}">
            <i class="bi bi-trash-fill"></i> Borrar
          </button>
        </td>
      `;
        ratesTbody.appendChild(tr);
    });
    document.querySelectorAll('.btn-delete-rate').forEach(btn => {
        btn.onclick = onDeleteRateClick;
    });
}

  // --- FORMULARIO MONEDA (VALIDACIÓN AMIGABLE) ---
  const formAddCurrency = document.getElementById('formAddCurrency');
  if (formAddCurrency) {
      formAddCurrency.addEventListener('submit', async function (e) {
          e.preventDefault();
          const fd = new FormData(this);
          const payload = Object.fromEntries(fd.entries());
          
          const j = await fetchJSON(API + '?action=create_currency', { 
              method: 'POST', 
              body: JSON.stringify(payload), 
              headers: { 'Content-Type': 'application/json' } 
          });

          if (!j.success) {
              // Interceptamos el error de duplicado aquí
              if (j.error && (j.error.includes("1062") || j.error.includes("Duplicate"))) {
                  Swal.fire({
                      icon: 'warning',
                      title: 'Moneda ya existente',
                      text: `El código "${payload.currency_code}" ya está registrado en el sistema.`,
                      confirmButtonColor: '#3085d6'
                  });
              } else {
                  showToast(j.message || 'Error al guardar', true);
              }
              return;
          }

          showToast('Moneda creada');
          this.reset();
          if (bsModalAddCurrency) bsModalAddCurrency.hide();
          await loadCurrencies();
      });
  }

  const formAddRate = document.getElementById('formAddRate');
  if (formAddRate) {
      formAddRate.addEventListener('submit', async function (e) {
          e.preventDefault();
          const fd = new FormData(this);
          const payload = Object.fromEntries(fd.entries());
          const j = await fetchJSON(API + '?action=create_rate', { 
              method: 'POST', 
              body: JSON.stringify(payload), 
              headers: { 'Content-Type': 'application/json' } 
          });
          if (j.success) {
              showToast('Tipo de cambio registrado');
              this.reset();
              if (bsModalAddRate) bsModalAddRate.hide();
              await loadRates();
          } else {
              showToast(j.message, true);
          }
      });
  }

  // --- ELIMINACIÓN ---
  async function onDeleteCurrencyClick() {
    const id = this.dataset.id;

    // Primero preguntamos si está seguro
    const result = await Swal.fire({
        title: '¿Estás seguro?',
        text: "Si esta moneda tiene historial de tipos de cambio, el sistema podría bloquear la eliminación para proteger tus datos.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, intentar eliminar',
        cancelButtonText: 'Cancelar',
        buttonsStyling: false,
        customClass: {
            confirmButton: 'btn btn-danger',
            cancelButton: 'btn btn-secondary ms-2'
        }
    });

    if (!result.isConfirmed) return;

    try {
        const j = await fetchJSON(API + '?action=delete_currency', {
            method: 'POST',
            body: JSON.stringify({ currency_id: id }),
            headers: { 'Content-Type': 'application/json' },
        });

        if (!j.success) {
            // AQUÍ BLOQUEAMOS SI HAY REGISTROS ASOCIADOS
            // El error suele ser un "1451" en SQL (Cannot delete or update a parent row)
            if (j.error && j.error.includes("1451")) {
                return Swal.fire({
                    icon: 'error',
                    title: 'Acción Bloqueada',
                    text: 'No puedes eliminar esta moneda porque tiene registros de tipos de cambio asociados. Para eliminarla, primero debes borrar su historial en la tabla de la derecha.',
                    confirmButtonColor: '#3085d6'
                });
            }
            throw new Error(j.message || 'Error al eliminar');
        }

        showToast('Moneda eliminada correctamente');
        await loadCurrencies();
        await loadRates();

    } catch (err) {
        Swal.fire('Error', err.message, 'error');
    }
}

  async function onDeleteRateClick() {
      const id = this.dataset.id;
      const res = await Swal.fire({ title: '¿Eliminar registro?', icon: 'warning', showCancelButton: true });
      if (!res.isConfirmed) return;
      const j = await fetchJSON(API + '?action=delete_rate', {
          method: 'POST',
          body: JSON.stringify({ rate_id: id }),
          headers: { 'Content-Type': 'application/json' }
      });
      if (j.success) { showToast('Registro eliminado'); loadRates(); }
  }

  // --- EXPORTACIONES ---
  function getTableData() {
      const rows = Array.from(ratesTbody.querySelectorAll('tr'));
      return rows.map(tr => {
          const tds = tr.querySelectorAll('td');
          if (tds.length < 4) return null;
          return [tds[0].innerText, tds[1].innerText, tds[2].innerText, tds[3].innerText];
      }).filter(x => x !== null);
  }

  document.getElementById('exportCSVBtn')?.addEventListener('click', () => {
      const data = getTableData();
      let csv = 'Fecha,Moneda,Valor,Notas\n';
      data.forEach(r => csv += `"${r[0]}","${r[1]}","${r[2]}","${r[3]}"\n`);
      const blob = new Blob([csv], { type: 'text/csv' });
      const a = document.createElement('a');
      a.href = URL.createObjectURL(blob); a.download = 'tasas.csv'; a.click();
  });

  document.getElementById('exportPDFBtn')?.addEventListener('click', () => {
      const { jsPDF } = window.jspdf;
      const doc = new jsPDF();
      doc.text("Reporte de Tasas", 14, 15);
      doc.autoTable({ head: [['Fecha', 'Moneda', 'Valor', 'Notas']], body: getTableData(), startY: 20 });
      doc.save('reporte.pdf');
  });

  function escapeHtml(str = '') {
      return String(str).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  }

  loadCurrencies();
  loadRates();
});