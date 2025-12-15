document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.getElementById('warehouses-tbody');
    const editModalEl = document.getElementById('editWarehouseModal');
    const editModal = new bootstrap.Modal(editModalEl);
    //const deleteModalEl = document.getElementById('deleteWarehouseModal');
    //const deleteModal = new bootstrap.Modal(deleteModalEl);
    //let deleteTargetId = null;
  
    const API_URL = `${BASE_URL}api/warehouses.php`;
  
    // Cargar lista desde API
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
      tbody.innerHTML = '';
      if (!Array.isArray(items) || items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">No hay almacenes</td></tr>';
        return;
      }
      items.forEach(row => {
        const tr = document.createElement('tr');
        tr.dataset.id = row.id;
        tr.innerHTML = `
          <td class="fw-semibold">${escapeHtml(row.id)}</td>
          <td class="warehouse-name">${escapeHtml(row.name)}</td>
          <td class="text-center">
            <div class="btn-group" role="group">
              <button class="btn btn-sm btn-outline-primary btn-edit-warehouse" data-id="${escapeAttr(row.id)}" data-name="${escapeAttr(row.name)}">
                <i class="bi bi-pencil-square"></i> Editar
              </button>
              <button class="btn btn-sm btn-outline-danger btn-delete-warehouse" data-id="${escapeAttr(row.id)}">
                <i class="bi bi-trash3"></i> Borrar
              </button>
            </div>
          </td>
        `;
        tbody.appendChild(tr);
      });
  
      // bind
      document.querySelectorAll('.btn-edit-warehouse').forEach(btn => {
        btn.removeEventListener('click', onEditClick);
        btn.addEventListener('click', onEditClick);
      });
      document.querySelectorAll('.btn-delete-warehouse').forEach(btn => {
        btn.removeEventListener('click', onDeleteClick);
        btn.addEventListener('click', onDeleteClick);
      });
    }
  
    function onEditClick(e) {
      const id = this.dataset.id;
      const name = this.dataset.name || '';
      document.getElementById('edit-warehouse-id').value = id;
      document.getElementById('edit-warehouse-name').value = name;
      editModalEl.querySelector('.modal-title').textContent = id ? 'Editar Almacén' : 'Nuevo Almacén';
      editModal.show();
    }
  
    /*function onDeleteClick(e) {
      deleteTargetId = this.dataset.id;
      deleteModal.show();
    }*/
      function onDeleteClick(e) {
        const id = this.dataset.id;
        const row = this.closest('tr');
        const name = row?.querySelector('.warehouse-name')?.textContent || '';
      
        Swal.fire({
          title: '¿Eliminar almacén?',
          html: `<strong>${name}</strong><br>Esta acción no se puede deshacer.`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Eliminar',
          cancelButtonText: 'Cancelar',
          buttonsStyling: false,
          customClass: {
            confirmButton: 'btn btn-danger',
            cancelButton: 'btn btn-secondary ms-2'
          }
        }).then(async (result) => {
          if (!result.isConfirmed) return;
      
          try {
            const fd = new FormData();
            fd.append('id', id);
      
            const res = await fetch(`${API_URL}?action=delete`, {
              method: 'POST',
              body: fd
            });
      
            const j = await res.json();
            if (!j.success) throw new Error(j.message || 'Error al eliminar');
      
            await loadWarehouses();
      
            Swal.fire({
              icon: 'success',
              title: 'Almacén eliminado',
              toast: true,
              position: 'top-end',
              timer: 2000,
              showConfirmButton: false
            });
          } catch (err) {
            console.error(err);
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: err.message || 'No se pudo eliminar el almacén'
            });
          }
        });
      }
      
  
    // Botón crear
    document.getElementById('addWarehouseBtn').addEventListener('click', () => {
      document.getElementById('edit-warehouse-id').value = '';
      document.getElementById('edit-warehouse-name').value = '';
      editModalEl.querySelector('.modal-title').textContent = 'Nuevo Almacén';
      editModal.show();
    });
  
    // Guardar (create/update)
    document.getElementById('editWarehouseForm').addEventListener('submit', async function (e) {
      e.preventDefault();
      const id = document.getElementById('edit-warehouse-id').value;
      const name = document.getElementById('edit-warehouse-name').value.trim();
      if (!name) return showToast('Nombre obligatorio', true);
  
      try {
        const fd = new FormData();
        fd.append('name', name);
        if (id) fd.append('id', id);
  
        const action = id ? 'update' : 'create';
        const res = await fetch(`${API_URL}?action=${action}`, {
          method: 'POST',
          body: fd
        });
        const j = await res.json();
        if (!j.success) throw new Error(j.message || 'Error al guardar');
  
        // actualizar en la UI: recargar la lista
        await loadWarehouses();
        editModal.hide();
        showToast('Almacén guardado correctamente.');
      } catch (err) {
        console.error(err);
        showToast(err.message || 'Error al guardar', true);
      }
    });
  
    /*
    // Confirmar eliminación
    document.getElementById('confirmDeleteWarehouseBtn').addEventListener('click', async function () {
      if (!deleteTargetId) return;
      try {
        const fd = new FormData();
        fd.append('id', deleteTargetId);
        const res = await fetch(`${API_URL}?action=delete`, {
          method: 'POST',
          body: fd
        });
        const j = await res.json();
        if (!j.success) throw new Error(j.message || 'Error al borrar');
        await loadWarehouses();
        deleteModal.hide();
        showToast('Almacén eliminado.');
        deleteTargetId = null;
      } catch (err) {
        console.error(err);
        showToast(err.message || 'Error al borrar', true);
      }
    });
    */
  
    // Helpers
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
      setTimeout(() => t.remove(), 3000);
    }
    function escapeHtml(str) {
      return String(str === null || typeof str === 'undefined' ? '' : str)
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
    }
    function escapeAttr(str) {
      return String(str === null || typeof str === 'undefined' ? '' : str)
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
    }
  
    // carga inicial
    loadWarehouses();
  });