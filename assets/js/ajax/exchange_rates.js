document.addEventListener('DOMContentLoaded', function () {
    const API = `${BASE_URL}api/exchange_rates.php`;
    const ratesTbody = document.querySelector('#rates-tbody');
    const currenciesListEl = document.querySelector('#currencies-list');

    // --- VARIABLES DE ESTADO ---
    let allRates = [];
    let currentPage = 1;
    const recordsPerPage = 10;
    
    // Filtros activos
    let currentStart = '';
    let currentEnd = '';

    // ==========================================
    // 1. RENDERIZADO
    // ==========================================

    const renderCurrencies = (items) => {
        if (!currenciesListEl) return;
        currenciesListEl.innerHTML = '';
        
        const selectCurrency = document.querySelector('select[name="currency_id"]');
        if (selectCurrency) selectCurrency.innerHTML = '<option value="">Selecciona moneda...</option>';

        items.forEach(c => {
            const li = document.createElement('li');
            li.className = 'list-group-item bg-transparent border-0 d-flex justify-content-between align-items-center p-3 mb-2 rounded-3 bg-body-secondary bg-opacity-25';
            li.innerHTML = `
                <div>
                  <strong class="text-body">${c.currency_code}</strong>
                  <div class="small text-muted">${c.currency_name}</div>
                </div>
                <div class="d-flex gap-1">
                  <button class="btn btn-sm btn-outline-info border-0 btn-edit-currency" 
                          data-id="${c.currency_id}" data-code="${c.currency_code}" data-name="${c.currency_name}">
                    <i class="bi bi-pencil-square"></i>
                  </button>
                  <button class="btn btn-sm btn-outline-danger border-0 btn-delete-currency" data-id="${c.currency_id}">
                    <i class="bi bi-trash-fill"></i>
                  </button>
                </div>
            `;
            currenciesListEl.appendChild(li);

            if (selectCurrency) {
                const opt = document.createElement('option');
                opt.value = c.currency_id;
                opt.textContent = `${c.currency_code} - ${c.currency_name}`;
                selectCurrency.appendChild(opt);
            }
        });

        document.querySelectorAll('.btn-delete-currency').forEach(btn => btn.onclick = onDeleteCurrencyClick);
        document.querySelectorAll('.btn-edit-currency').forEach(btn => btn.onclick = onEditCurrencyClick);
    };

    const renderRates = (items) => {
        if (!ratesTbody) return;
        ratesTbody.innerHTML = '';

        if (items.length === 0) {
            ratesTbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">No se encontraron registros</td></tr>';
            renderPaginationControls(0);
            return;
        }

        const start = (currentPage - 1) * recordsPerPage;
        const end = start + recordsPerPage;
        const paginatedItems = items.slice(start, end);

        paginatedItems.forEach(r => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="ps-4 text-body">${r.rate_date}</td>
                <td class="text-body"><strong>${r.currency_code}</strong></td>
                <td><span class="badge bg-success-subtle text-success border border-success-subtle fs-6">$${Number(r.rate).toFixed(6)}</span></td>
                <td class="small text-muted italic">${r.notes || ''}</td>
                <td class="text-end pe-4">
                  <button class="btn btn-sm btn-outline-info border me-1 btn-edit-rate" 
                          data-id="${r.rate_id}" data-rate="${r.rate}" data-date="${r.rate_date}" data-notes="${r.notes || ''}">
                    <i class="bi bi-pencil-square"></i>
                  </button>
                  <button class="btn btn-sm btn-outline-danger border btn-delete-rate" data-id="${r.rate_id}">
                    <i class="bi bi-trash-fill"></i>
                  </button>
                </td>
            `;
            ratesTbody.appendChild(tr);
        });

        document.querySelectorAll('.btn-delete-rate').forEach(btn => btn.onclick = onDeleteRateClick);
        document.querySelectorAll('.btn-edit-rate').forEach(btn => btn.onclick = onEditRateClick);
        
        renderPaginationControls(items.length);
    };

    const renderPaginationControls = (totalRecords) => {
        let nav = document.getElementById('pagination-container');
        if (!nav) {
            nav = document.createElement('div');
            nav.id = 'pagination-container';
            nav.className = 'd-flex justify-content-between align-items-center px-4 py-3 border-top';
            ratesTbody.closest('.card').appendChild(nav);
        }
        
        if (totalRecords === 0) {
            nav.innerHTML = '';
            return;
        }

        const totalPages = Math.ceil(totalRecords / recordsPerPage) || 1;
        nav.innerHTML = `
            <div class="small text-muted">Página ${currentPage} de ${totalPages}</div>
            <div class="btn-group">
                <button class="btn btn-sm btn-outline-secondary" ${currentPage === 1 ? 'disabled' : ''} id="prevPage">Anterior</button>
                <button class="btn btn-sm btn-outline-secondary" ${currentPage === totalPages ? 'disabled' : ''} id="nextPage">Siguiente</button>
            </div>
        `;

        const prevBtn = document.getElementById('prevPage');
        const nextBtn = document.getElementById('nextPage');

        if(prevBtn) prevBtn.onclick = () => { currentPage--; renderRates(allRates); };
        if(nextBtn) nextBtn.onclick = () => { currentPage++; renderRates(allRates); };
    };

    // ==========================================
    // 2. EDICIÓN
    // ==========================================

    async function onEditCurrencyClick() {
        const { id, code, name } = this.dataset;
        const { value: formValues } = await Swal.fire({
            title: 'Editar Moneda',
            background: '#1e1e1e', color: '#fff',
            html: `
                <div class="text-start p-2">
                    <label class="small mb-1">Código</label>
                    <input id="swal-code" class="form-control bg-dark text-white border-secondary mb-3" value="${code}">
                    <label class="small mb-1">Nombre</label>
                    <input id="swal-name" class="form-control bg-dark text-white border-secondary" value="${name}">
                </div>`,
            showCancelButton: true,
            confirmButtonText: 'Actualizar',
            preConfirm: () => [document.getElementById('swal-code').value, document.getElementById('swal-name').value]
        });

        if (formValues) {
            const res = await fetch(`${API}?action=update_currency`, {
                method: 'POST',
                body: JSON.stringify({ currency_id: id, currency_code: formValues[0], currency_name: formValues[1] }),
                headers: { 'Content-Type': 'application/json' }
            });
            
            const response = await res.json();

            if (response.success) { 
                loadCurrencies(); // Actualiza la lista de la izquierda
                loadRates();      // <--- ESTA LÍNEA FALTABA: Actualiza la tabla de tasas
                Swal.fire('Éxito', 'Moneda actualizada', 'success'); 
            }
        }
    }

    async function onEditRateClick() {
        const { id, rate, date, notes } = this.dataset;
        const { value: f } = await Swal.fire({
            title: 'Editar Registro',
            background: '#1e1e1e', color: '#fff',
            html: `
                <div class="text-start p-2">
                    <label class="small mb-1 text-muted">Tasa</label>
                    <input id="sw-rate" type="number" step="0.000001" class="form-control bg-dark text-white border-secondary mb-3" value="${rate}">
                    <label class="small mb-1 text-muted">Fecha</label>
                    <input id="sw-date" type="date" class="form-control bg-dark text-white border-secondary mb-3" value="${date}">
                    <label class="small mb-1 text-muted">Notas</label>
                    <textarea id="sw-notes" class="form-control bg-dark text-white border-secondary">${notes}</textarea>
                </div>`,
            showCancelButton: true,
            confirmButtonText: 'Guardar',
            preConfirm: () => ({ rate: document.getElementById('sw-rate').value, date: document.getElementById('sw-date').value, notes: document.getElementById('sw-notes').value })
        });

        if (f) {
            const res = await fetch(`${API}?action=update_rate`, {
                method: 'POST',
                body: JSON.stringify({ rate_id: id, rate: f.rate, rate_date: f.date, notes: f.notes }),
                headers: { 'Content-Type': 'application/json' }
            });
            if ((await res.json()).success) { loadRates(); Swal.fire('Éxito', 'Registro actualizado', 'success'); }
        }
    }

    // ==========================================
    // 3. ELIMINACIÓN (CON PROTECCIÓN)
    // ==========================================

    async function onDeleteCurrencyClick() {
        const id = this.dataset.id;
        const nombreMoneda = this.closest('li').querySelector('strong').innerText;

        const confirm = await Swal.fire({
            title: '¿Eliminar Moneda?',
            text: `Se verificará si ${nombreMoneda} tiene historial.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Eliminar',
            customClass: { confirmButton: 'btn btn-danger', cancelButton: 'btn btn-secondary ms-2' },
            buttonsStyling: false
        });
        if (!confirm.isConfirmed) return;

        const res = await fetch(`${API}?action=delete_currency`, {
            method: 'POST',
            body: JSON.stringify({ currency_id: id }),
            headers: { 'Content-Type': 'application/json' }
        });
        
        const j = await res.json();
        
        if (j.success) {
            loadCurrencies(); 
            // Si la moneda borrada afectaba la lista de tasas, recargamos
            loadRates();
            Swal.fire('Eliminado', 'La moneda ha sido eliminada.', 'success');
        } else {
            // AQUÍ SALTA EL ERROR SI TIENE HISTORIAL
            Swal.fire('Error', 'No se puede eliminar porque tiene historial de tasas registrado.', 'error');
        }
    }

    async function onDeleteRateClick() {
        const id = this.dataset.id;
        const confirm = await Swal.fire({
            title: '¿Borrar este registro?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Eliminar',
            customClass: { confirmButton: 'btn btn-danger', cancelButton: 'btn btn-secondary ms-2' },
            buttonsStyling: false
        });
        if (!confirm.isConfirmed) return;

        const res = await fetch(`${API}?action=delete_rate`, {
            method: 'POST',
            body: JSON.stringify({ rate_id: id }),
            headers: { 'Content-Type': 'application/json' }
        });
        if ((await res.json()).success) loadRates();
    }

    // ==========================================
    // 4. EXPORTACIÓN e INICIALIZACIÓN
    // ==========================================

    const exportData = async (type) => {
        // Usamos allRates que ya contiene los datos filtrados del servidor
        if (!allRates || !allRates.length) return Swal.fire('Info', 'No hay datos visibles para exportar', 'info');

        const formatted = allRates.map(r => ({ Fecha: r.rate_date, Moneda: r.currency_code, Tasa: r.rate, Notas: r.notes || '' }));

        if (type === 'pdf') {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            doc.text("Historial de Tasas", 14, 15);
            doc.autoTable({ head: [['Fecha', 'Moneda', 'Tasa', 'Notas']], body: formatted.map(d => [d.Fecha, d.Moneda, d.Tasa, d.Notas]), startY: 20 });
            doc.save('Historial_Tasas.pdf');
        } else {
            const ws = XLSX.utils.json_to_sheet(formatted);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Tasas");
            XLSX.writeFile(wb, `Historial_Tasas.${type === 'excel' ? 'xlsx' : 'csv'}`);
        }
    };

    document.getElementById('exportPDFBtn')?.addEventListener('click', () => exportData('pdf'));
    document.getElementById('exportExcelBtn')?.addEventListener('click', () => exportData('excel'));
    document.getElementById('exportCSVBtn')?.addEventListener('click', () => exportData('csv'));

    // CARGA PRINCIPAL (MODIFICADA PARA FILTROS)
    async function loadRates() {
        let url = `${API}?action=list_rates`;
        if (currentStart) url += `&start=${currentStart}`;
        if (currentEnd) url += `&end=${currentEnd}`;

        const res = await fetch(url);
        const j = await res.json();
        if (j.success) { 
            allRates = j.data; 
            // Si filtramos, queremos ver la página 1
            renderRates(allRates); 
        }
    }

    async function loadCurrencies() {
        const res = await fetch(`${API}?action=list_currencies`);
        const j = await res.json();
        if (j.success) renderCurrencies(j.data);
    }

    // Listeners Formularios
    document.getElementById('formAddCurrency')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const res = await fetch(`${API}?action=create_currency`, {
            method: 'POST',
            body: JSON.stringify(Object.fromEntries(new FormData(this))),
            headers: { 'Content-Type': 'application/json' }
        });
        if ((await res.json()).success) { this.reset(); bootstrap.Modal.getInstance(document.getElementById('modalAddCurrency')).hide(); loadCurrencies(); }
    });

    document.getElementById('formAddRate')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const res = await fetch(`${API}?action=create_rate`, {
            method: 'POST',
            body: JSON.stringify(Object.fromEntries(new FormData(this))),
            headers: { 'Content-Type': 'application/json' }
        });
        if ((await res.json()).success) { this.reset(); bootstrap.Modal.getInstance(document.getElementById('modalAddRate')).hide(); loadRates(); }
    });

    // ==========================================
    // 5. NUEVA LÓGICA DE FILTRADO (SERVER-SIDE)
    // ==========================================
    
    // Función central para actualizar variables y recargar
    const applyFilters = () => {
        currentStart = document.getElementById('filter-date-start').value;
        currentEnd = document.getElementById('filter-date-end').value;
        currentPage = 1; // Reseteamos paginación al filtrar
        loadRates();     // Pedimos datos nuevos al servidor
    };

    // Eventos 'change' para que busque al momento de seleccionar fecha
    document.getElementById('filter-date-start')?.addEventListener('change', applyFilters);
    document.getElementById('filter-date-end')?.addEventListener('change', applyFilters);

    // Botón Limpiar
    document.getElementById('btn-clear-filter')?.addEventListener('click', () => {
        document.getElementById('filter-date-start').value = '';
        document.getElementById('filter-date-end').value = '';
        currentStart = '';
        currentEnd = '';
        currentPage = 1;
        loadRates();
    });

    // Carga inicial
    loadCurrencies();
    loadRates();
});