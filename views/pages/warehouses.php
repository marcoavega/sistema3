<?php
// Archivo: views/pages/warehouses.php

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "auth/login/");
    exit();
}

$uri = $_GET['url'] ?? 'warehouses';
$segment = explode('/', trim($uri, '/'))[0];

ob_start();

require_once __DIR__ . '/../../models/Database.php';
$pdo = (new Database())->getConnection();

$username = htmlspecialchars($_SESSION['user']['username']);

// incluir lateral para mantener consistencia visual
require_once __DIR__ . '/../partials/layouts/lateral_menu_products.php';
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/page-list-product.css">

<div class="container-fluid m-0 p-0 min-vh-100" data-bs-theme="auto">
  <div class="row g-0">

    <!-- Barra lateral (igual que otras páginas) -->
    <nav class="col-md-2 d-none d-md-block sidebar min-vh-100">
      <div class="pt-4 px-3">
        <div class="text-center mb-4">
          <div class=" rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
            <i class="bi bi-building text-primary fs-3"></i>
          </div>
          <h6 class=" mt-2 mb-0">Inventario</h6>
        </div>

        <ul class="nav flex-column">
          <?php foreach ($menuItems as $route => $item): ?>
            <li class="nav-item mb-2">
              <a class="nav-link d-flex align-items-center px-3 py-2 rounded-3 <?= $segment === $route ? 'bg-primary text-white fw-bold' : 'text-body' ?>"
              href="<?= BASE_URL . $route ?>" style="transition: all 0.3s ease;">
                <i class="bi bi-<?= $item['icon'] ?> me-3 fs-5"></i>
                <span class="fw-medium"><?= $item['label'] ?></span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </nav>

    <!-- Contenido principal -->
    <main class="col-12 col-md-10">

      <!-- Header -->
      <div class="bg-body shadow-sm border-bottom">
        <div class="container-fluid px-4 py-3">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                  <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-decoration-none">Dashboard</a></li>
                  <li class="breadcrumb-item active">Almacenes</li>
                </ol>
              </nav>
              <h4 class="mb-0 fw-bold">Gestión de Almacenes</h4>
              <small class="text-muted">Bienvenido, <?= $username ?></small>
            </div>

            <div>
              <button id="addWarehouseBtn" class="btn btn-info btn-lg px-4">
                <i class="bi bi-plus-circle me-2"></i>Nuevo Almacén
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Contenido -->
      <div class="container-fluid px-4 py-4">
        <div class="card shadow-lg border-0 rounded-4">
          <div class="card-header p-4">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h3 class="mb-1 fw-bold">Listado de Almacenes</h3>
                <p class="mb-0 opacity-75">Administra los almacenes del sistema</p>
              </div>
            </div>
          </div>

          <div class="card-body p-4">
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead>
                  <tr>
                    <th style="width:80px;">#</th>
                    <th>Nombre del Almacén</th>
                    <th style="width:200px;" class="text-center">Opciones</th>
                  </tr>
                </thead>
                <tbody id="warehouses-tbody">
                  <!-- filas cargadas desde JS -->
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>

    </main>
  </div>
</div>

<!-- Modales: agregar/editar y confirmar borrar -->
<div class="modal fade" id="editWarehouseModal" tabindex="-1" aria-labelledby="editWarehouseModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="editWarehouseForm">
        <div class="modal-header">
          <h5 class="modal-title" id="editWarehouseModalLabel">Nuevo Almacén</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="edit-warehouse-id" name="id" value="">
          <div class="mb-3">
            <label for="edit-warehouse-name" class="form-label">Nombre del Almacén</label>
            <input type="text" id="edit-warehouse-name" name="name" class="form-control" required maxlength="255">
          </div>
          <div class="form-text text-muted">Los cambios se guardarán en la base de datos.</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary" id="saveWarehouseBtn">
            <i class="bi bi-save me-2"></i> Guardar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="deleteWarehouseModal" tabindex="-1" aria-labelledby="deleteWarehouseModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteWarehouseModalLabel">Confirmar eliminación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        ¿Deseas eliminar este almacén? Esta acción no puede deshacerse.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" id="confirmDeleteWarehouseBtn" class="btn btn-danger">Eliminar</button>
      </div>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layouts/navbar.php';
?>

<!-- JS: lógica CRUD usando la API (fetch) -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const tbody = document.getElementById('warehouses-tbody');
  const editModalEl = document.getElementById('editWarehouseModal');
  const editModal = new bootstrap.Modal(editModalEl);
  const deleteModalEl = document.getElementById('deleteWarehouseModal');
  const deleteModal = new bootstrap.Modal(deleteModalEl);
  let deleteTargetId = null;

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

  function onDeleteClick(e) {
    deleteTargetId = this.dataset.id;
    deleteModal.show();
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
</script>
