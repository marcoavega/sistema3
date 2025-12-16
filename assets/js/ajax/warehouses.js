document.addEventListener('DOMContentLoaded', function () {
    // 1. SELECTORES DE ELEMENTOS
    const tbody = document.getElementById('warehouses-tbody');
    const API_URL = `${BASE_URL}api/warehouses.php`;

    // Inicialización de Modales (Verifica que los IDs coincidan con los archivos PHP)
    const addModalEl = document.getElementById('addWarehouseModal');
    const editModalEl = document.getElementById('editWarehouseModal');

    // Solo inicializamos si los elementos existen en el DOM para evitar el error "backdrop"
    let addModal, editModal;
    
    if (addModalEl) {
        addModal = new bootstrap.Modal(addModalEl);
    } else {
        console.error("Error: No se encontró el elemento #addWarehouseModal en el HTML.");
    }

    if (editModalEl) {
        editModal = new bootstrap.Modal(editModalEl);
    } else {
        console.error("Error: No se encontró el elemento #editWarehouseModal en el HTML.");
    }

    // 2. FUNCIÓN PARA CARGAR LA TABLA
    async function loadWarehouses() {
        try {
            const res = await fetch(`${API_URL}?action=list`);
            const j = await res.json();
            if (!j.success) throw new Error(j.message || 'Error al listar');
            renderRows(j.data || []);
        } catch (err) {
            console.error(err);
            showToast('Error al cargar almacenes', true);
        }
    }

    function renderRows(items) {
        if (!tbody) return;
        tbody.innerHTML = '';
        
        if (!Array.isArray(items) || items.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted p-4">No hay almacenes registrados</td></tr>';
            return;
        }

        items.forEach(row => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="fw-bold text-primary" style="width: 80px;">#${escapeHtml(row.id)}</td>
                <td class="warehouse-name fw-semibold">${escapeHtml(row.name)}</td>
                <td class="text-center">
                    <div class="btn-group shadow-sm" role="group">
                        <button class="btn btn-sm btn-outline-primary btn-edit-warehouse" 
                                data-id="${escapeAttr(row.id)}" 
                                data-name="${escapeAttr(row.name)}">
                            <i class="fas fa-edit me-1"></i> Editar
                        </button>
                        <button class="btn btn-sm btn-outline-danger btn-delete-warehouse" 
                                data-id="${escapeAttr(row.id)}" 
                                data-name="${escapeAttr(row.name)}">
                            <i class="fas fa-trash-alt me-1"></i> Borrar
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });

        // Asignar eventos a los botones recién creados
        document.querySelectorAll('.btn-edit-warehouse').forEach(btn => {
            btn.addEventListener('click', onEditClick);
        });
        document.querySelectorAll('.btn-delete-warehouse').forEach(btn => {
            btn.addEventListener('click', onDeleteClick);
        });
    }

    // 3. EVENTOS DE APERTURA DE MODALES
    const addBtn = document.getElementById('addWarehouseBtn');
    if (addBtn) {
        addBtn.addEventListener('click', () => {
            document.getElementById('addWarehouseForm').reset();
            addModal.show();
        });
    }

    function onEditClick() {
        const id = this.dataset.id;
        const name = this.dataset.name;
        
        document.getElementById('edit-warehouse-id').value = id;
        document.getElementById('edit-warehouse-name').value = name;
        editModal.show();
    }

    // 4. PROCESAR GUARDADO (NUEVO)
    const addForm = document.getElementById('addWarehouseForm');
    if (addForm) {
        addForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const name = document.getElementById('new-warehouse-name').value.trim();
            const btn = document.getElementById('saveNewWarehouseBtn');

            try {
                toggleLoading(btn, true);
                const fd = new FormData();
                fd.append('name', name);

                const res = await fetch(`${API_URL}?action=create`, { method: 'POST', body: fd });
                const j = await res.json();
                if (!j.success) throw new Error(j.message);

                addModal.hide();
                showToast('Almacén creado con éxito');
                await loadWarehouses();
            } catch (err) {
                showToast(err.message, true);
            } finally {
                toggleLoading(btn, false);
            }
        });
    }

    // 5. PROCESAR ACTUALIZACIÓN (EDITAR)
    const editForm = document.getElementById('editWarehouseForm');
    if (editForm) {
        editForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const id = document.getElementById('edit-warehouse-id').value;
            const name = document.getElementById('edit-warehouse-name').value.trim();
            const btn = document.getElementById('saveChangesWarehouseBtn');

            try {
                toggleLoading(btn, true);
                const fd = new FormData();
                fd.append('id', id);
                fd.append('name', name);

                const res = await fetch(`${API_URL}?action=update`, { method: 'POST', body: fd });
                const j = await res.json();
                if (!j.success) throw new Error(j.message);

                editModal.hide();
                showToast('Almacén actualizado con éxito');
                await loadWarehouses();
            } catch (err) {
                showToast(err.message, true);
            } finally {
                toggleLoading(btn, false);
            }
        });
    }

    // 6. ELIMINAR (CON SWAL)
    function onDeleteClick() {
        const id = this.dataset.id;
        const name = this.dataset.name;

        Swal.fire({
            title: '¿Eliminar almacén?',
            html: `Confirma que desea eliminar: <b>${name}</b>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const fd = new FormData();
                    fd.append('id', id);
                    const res = await fetch(`${API_URL}?action=delete`, { method: 'POST', body: fd });
                    const j = await res.json();
                    if (!j.success) throw new Error(j.message);
                    
                    showToast('Almacén eliminado');
                    await loadWarehouses();
                } catch (err) {
                    Swal.fire('Error', err.message, 'error');
                }
            }
        });
    }

    // HELPERS
    function toggleLoading(btn, loading) {
        if (!btn) return;
        const icon = btn.querySelector('i');
        if (loading) {
            btn.disabled = true;
            if (icon) icon.className = 'fas fa-spinner fa-spin me-2';
        } else {
            btn.disabled = false;
            if (icon) icon.className = icon.dataset.origClass || 'fas fa-save me-2';
        }
    }

    function showToast(msg, isError = false) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: isError ? 'error' : 'success',
            title: msg,
            showConfirmButton: false,
            timer: 3000
        });
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function escapeAttr(str) {
        return String(str).replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    // CARGA INICIAL
    loadWarehouses();
});