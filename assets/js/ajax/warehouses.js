document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.getElementById('warehouses-tbody');
    const API_URL = `${BASE_URL}api/warehouses.php`;

    const addModalEl = document.getElementById('addWarehouseModal');
    const editModalEl = document.getElementById('editWarehouseModal');

    let addModal, editModal;
    if (addModalEl) addModal = new bootstrap.Modal(addModalEl);
    if (editModalEl) editModal = new bootstrap.Modal(editModalEl);

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
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted p-5">No hay almacenes registrados</td></tr>';
            return;
        }

        items.forEach(row => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="px-4 fw-bold text-muted small">#${escapeHtml(row.id)}</td>
                <td class="fw-semibold text-body">${escapeHtml(row.name)}</td>
                <td class="text-center">
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-soft-primary btn-sm rounded-3 btn-edit-warehouse" 
                                data-id="${escapeAttr(row.id)}" 
                                data-name="${escapeAttr(row.name)}"
                                title="Editar">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-soft-danger btn-sm rounded-3 btn-delete-warehouse" 
                                data-id="${escapeAttr(row.id)}" 
                                data-name="${escapeAttr(row.name)}"
                                title="Eliminar">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });

        // Re-asignar eventos
        document.querySelectorAll('.btn-edit-warehouse').forEach(btn => btn.addEventListener('click', onEditClick));
        document.querySelectorAll('.btn-delete-warehouse').forEach(btn => btn.addEventListener('click', onDeleteClick));
    }

    // Apertura de modal agregar
    const addBtn = document.getElementById('addWarehouseBtn');
    if (addBtn) {
        addBtn.addEventListener('click', () => {
            const form = document.getElementById('addWarehouseForm');
            if(form) form.reset();
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

    // Guardar Nuevo
    const addForm = document.getElementById('addWarehouseForm');
    if (addForm) {
        addForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const nameInput = document.getElementById('new-warehouse-name');
            const btn = document.getElementById('saveNewWarehouseBtn');
            if (!nameInput.value.trim()) return;

            try {
                toggleLoading(btn, true);
                const fd = new FormData();
                fd.append('name', nameInput.value.trim());

                const res = await fetch(`${API_URL}?action=create`, { method: 'POST', body: fd });
                const j = await res.json();
                if (!j.success) throw new Error(j.message);

                addModal.hide();
                showToast('Almacén creado con éxito');
                loadWarehouses();
            } catch (err) {
                showToast(err.message, true);
            } finally {
                toggleLoading(btn, false);
            }
        });
    }

    // Guardar Cambios
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
                showToast('Actualizado correctamente');
                loadWarehouses();
            } catch (err) {
                showToast(err.message, true);
            } finally {
                toggleLoading(btn, false);
            }
        });
    }

    // Borrar
    function onDeleteClick() {
        const id = this.dataset.id;
        const name = this.dataset.name;

        Swal.fire({
            title: '¿Eliminar almacén?',
            html: `¿Estás seguro de eliminar <b>${name}</b>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            customClass: { confirmButton: 'btn btn-danger me-2', cancelButton: 'btn btn-light' },
            buttonsStyling: false
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const fd = new FormData();
                    fd.append('id', id);
                    const res = await fetch(`${API_URL}?action=delete`, { method: 'POST', body: fd });
                    const j = await res.json();
                    if (!j.success) throw new Error(j.message);
                    showToast('Eliminado correctamente');
                    loadWarehouses();
                } catch (err) {
                    Swal.fire('Error', err.message, 'error');
                }
            }
        });
    }

    // Utilitarios
    function toggleLoading(btn, loading) {
        if (!btn) return;
        const icon = btn.querySelector('i');
        if (loading) {
            btn.disabled = true;
            if (icon) {
                icon.dataset.oldClass = icon.className;
                icon.className = 'fas fa-spinner fa-spin me-2';
            }
        } else {
            btn.disabled = false;
            if (icon) icon.className = icon.dataset.oldClass || 'fas fa-save me-2';
        }
    }

    function showToast(msg, isError = false) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: isError ? 'error' : 'success',
            title: msg,
            showConfirmButton: false,
            timer: 2000
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

    loadWarehouses();
});