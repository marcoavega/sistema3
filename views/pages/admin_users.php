<?php
// Archivo: views/pages/admin_users.php

// Verifica si el usuario está logueado, si no, redirige
require_once __DIR__ . '/../inc/auth_check.php';


$uri = $_GET['url'] ?? 'admin_users';
$segment = explode('/', trim($uri, '/'))[0];

ob_start();

require_once __DIR__ . '/../../models/Database.php';
$pdo = (new Database())->getConnection();

$stmt = $pdo->query("SELECT id_level_user, description_level FROM levels_users ORDER BY level");
$levels = $stmt->fetchAll(PDO::FETCH_ASSOC);

$username = htmlspecialchars($_SESSION['user']['username']);


?>

<div class="container-fluid m-0 p-0 min-vh-100" data-bs-theme="auto">
  <div class="row g-0">

    <!-- Barra lateral con gradiente moderno -->
    <?php require_once __DIR__ . '/../partials/layouts/laterals_menus/lateral_menu_users.php'; ?>

    <main class="col-12 col-md-10">

      <div class="bg-body shadow-sm border-bottom">
        <div class="container-fluid px-4 py-3">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                  <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-decoration-none">Dashboard</a></li>
                  <li class="breadcrumb-item active">Administración de Usuarios</li>
                </ol>
              </nav>
              <h4 class="mb-0 fw-bold">Gestión de Usuarios</h4>
              <small class="text-muted">Bienvenido, <?= $username ?></small>
            </div>

            <div class="d-md-none">
              <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                <i class="bi bi-list"></i>
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="mobileMenu">
        <div class="offcanvas-header bg-primary-subtle">
          <h5 class="offcanvas-title">
            <i class="bi bi-people-fill me-2"></i>Usuarios
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body bg-body">
          <ul class="nav flex-column">
            <li class="nav-item mb-2">
              <a class="nav-link text-body d-flex align-items-center px-3 py-2 rounded-3 <?= $segment === 'admin_users' ? 'active bg-primary text-white' : '' ?>" href="<?= BASE_URL ?>admin_users">
                <i class="bi bi-people-fill me-3"></i>Usuarios
              </a>
            </li>
          </ul>
        </div>
      </div>

      <?php if ($_SESSION['user']['level_user'] != 1): ?>
        <div class="container-fluid px-4 py-5">
          <div class="row justify-content-center">
            <div class="col-md-6">
              <div class="card border-0 shadow-lg">
                <div class="card-body text-center py-5">
                  <div class="mb-4">
                    <i class="bi bi-shield-exclamation text-warning" style="font-size: 4rem;"></i>
                  </div>
                  <h3 class="fw-bold text-warning mb-3">Acceso Denegado</h3>
                  <p class="text-muted mb-4">No tienes los permisos necesarios para acceder a esta sección del sistema.</p>
                  <a href="<?= BASE_URL ?>dashboard" class="btn btn-primary px-4">
                    <i class="bi bi-house me-2"></i>Volver al Dashboard
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php else: ?>

        <div class="container-fluid px-4 py-4">
          <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header p-4">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h3 class="mb-1 fw-bold">Listado de Usuarios</h3>
                  <p class="mb-0 opacity-75">Gestiona los usuarios del sistema desde aquí</p>
                </div>
                <div>
                  <button id="addUserBtn" class="btn btn-info btn-lg px-4">
                    <i class="bi bi-person-plus-fill me-2"></i>Nuevo Usuario
                  </button>
                </div>
              </div>
            </div>

            <div class="card-body p-4">
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <div class="position-relative">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input type="text" id="table-search" class="form-control form-control-lg ps-5 rounded-pill border-2" placeholder="Buscar usuarios por nombre o email...">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="d-flex gap-2 justify-content-md-end">
                    <div class="dropdown">
                      <button class="btn btn-outline-primary dropdown-toggle rounded-pill px-4" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-download me-2"></i>Exportar
                      </button>
                      <ul class="dropdown-menu shadow-lg border-0 rounded-3">
                        <li>
                          <h6 class="dropdown-header fw-bold">Formatos disponibles</h6>
                        </li>
                        <li><button id="exportCSVBtn" class="dropdown-item d-flex align-items-center"><i class="bi bi-filetype-csv text-success me-2"></i>Exportar a CSV</button></li>
                        <li><button id="exportExcelBtn" class="dropdown-item d-flex align-items-center"><i class="bi bi-file-earmark-excel text-success me-2"></i>Exportar a Excel</button></li>
                        <li><button id="exportPDFBtn" class="dropdown-item d-flex align-items-center"><i class="bi bi-file-earmark-pdf text-danger me-2"></i>Exportar a PDF</button></li>
                        <li><button id="exportJSONBtn" class="dropdown-item d-flex align-items-center"><i class="bi bi-filetype-json text-info me-2"></i>Exportar a JSON</button></li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>

              <div class="table-responsive">
                <div id="users-table" class="border rounded-3"></div>
              </div>

              <?php
               include __DIR__ . '/../partials/modals/modal_add_user.php';
               include __DIR__ . '/../partials/modals/modal_edit_user.php';
               include __DIR__ . '/../partials/modals/modal_delete_user.php';
              ?>

            </div>
          </div>
        </div>

      <?php endif; ?>
    </main>
  </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layouts/navbar.php';
?>

<script src="<?php echo BASE_URL; ?>assets/js/ajax/admin-users.js"></script>