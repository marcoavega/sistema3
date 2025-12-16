<?php
// Archivo: views/pages/warehouses.php
require_once __DIR__ . '/../inc/auth_check.php';

$uri = $_GET['url'] ?? 'warehouses';
$segment = explode('/', trim($uri, '/'))[0];

ob_start();

require_once __DIR__ . '/../../models/Database.php';
$pdo = (new Database())->getConnection();

$username = htmlspecialchars($_SESSION['user']['username']);
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/page-list-product.css">

<div class="container-fluid m-0 p-0 min-vh-100" data-bs-theme="auto">
  <div class="row g-0">

    <?php require_once __DIR__ . '/../partials/layouts/laterals_menus/lateral_menu_warehouse.php'; ?>

    <main class="col-12 col-md-10">

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
              <button id="addWarehouseBtn" class="btn btn-info btn-lg px-4 shadow-sm">
                <i class="fas fa-plus-circle me-2"></i>Nuevo Almacén
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="container-fluid px-4 py-4">
        <div class="card shadow-lg border-0 rounded-4">
          <div class="card-header p-4">
            <h3 class="mb-1 fw-bold">Listado de Almacenes</h3>
            <p class="mb-0 text-muted">Administra las ubicaciones físicas del inventario</p>
          </div>

          <div class="card-body p-4">
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead class="table">
                  <tr>
                    <th style="width:80px;">#</th>
                    <th>Nombre del Almacén</th>
                    <th style="width:200px;" class="text-center">Opciones</th>
                  </tr>
                </thead>
                <tbody id="warehouses-tbody">
                  </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<?php

include_once __DIR__ . '/../partials/modals/modal_add_warehouse.php';
include_once __DIR__ . '/../partials/modals/modal_edit_warehouse.php';

$content = ob_get_clean();
include __DIR__ . '/../partials/layouts/navbar.php';
?>

<script src="<?php echo BASE_URL; ?>assets/js/ajax/warehouses.js"></script>