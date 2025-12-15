<?php
// Archivo: views/pages/warehouses.php

// Verifica si el usuario está logueado, si no, redirige
require_once __DIR__ . '/../inc/auth_check.php';

$uri = $_GET['url'] ?? 'warehouses';
$segment = explode('/', trim($uri, '/'))[0];

ob_start();

require_once __DIR__ . '/../../models/Database.php';
$pdo = (new Database())->getConnection();

$username = htmlspecialchars($_SESSION['user']['username']);

// incluir lateral para mantener consistencia visual
//require_once __DIR__ . '/../partials/layouts/laterals_menus/lateral_menu_products.php';
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/page-list-product.css">

<div class="container-fluid m-0 p-0 min-vh-100" data-bs-theme="auto">
  <div class="row g-0">

    <!-- Barra lateral (igual que otras páginas) -->
    <?php require_once __DIR__ . '/../partials/layouts/laterals_menus/lateral_menu_warehouse.php'; ?>

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
<script src="<?php echo BASE_URL; ?>assets/js/ajax/warehouses.js"></script>
