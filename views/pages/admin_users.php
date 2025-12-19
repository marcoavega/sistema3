<?php
// Archivo: views/pages/admin_users.php
require_once __DIR__ . '/../inc/auth_check.php';

// 1. CONTROL DE ACCESO (Posicionamiento Ideal)
// Lo ponemos al principio. Si no es admin, mostramos error y cortamos ejecución.
$isAdmin = (isset($_SESSION['user']['level_user']) && $_SESSION['user']['level_user'] == 1);

ob_start();

require_once __DIR__ . '/../../models/Database.php';
$pdo = (new Database())->getConnection();

// Consulta para niveles (necesaria para los modales)
$stmt = $pdo->query("SELECT id_level_user, description_level FROM levels_users ORDER BY level");
$levels = $stmt->fetchAll(PDO::FETCH_ASSOC);

$username = htmlspecialchars($_SESSION['user']['username']);
?>

<div class="container-fluid m-0 p-0 min-vh-100" data-bs-theme="auto">
  <div class="row g-0">

    <?php require_once __DIR__ . '/../partials/layouts/laterals_menus/lateral_menu_users.php'; ?>

    <main class="col-12 col-md-10">

      <div class="bg-body shadow-sm border-bottom">
        <div class="container-fluid px-4 py-3">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                  <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-decoration-none">Dashboard</a></li>
                  <li class="breadcrumb-item active">Administración</li>
                </ol>
              </nav>
              <h4 class="mb-0 fw-bold">Gestión de Usuarios</h4>
              <small class="text-muted">Bienvenido, <?= $username ?></small>
            </div>
          </div>
        </div>
      </div>

      <div class="container-fluid px-4 py-4">
        <?php if (!$isAdmin): ?>
          <div class="row justify-content-center mt-5">
            <div class="col-md-6">
              <div class="card border-0 shadow-lg rounded-4 text-center py-5">
                <div class="card-body">
                  <div class="mb-4">
                    <i class="fas fa-shield-alt text-warning" style="font-size: 4rem; opacity: 0.5;"></i>
                  </div>
                  <h3 class="fw-bold">Acceso Restringido</h3>
                  <p class="text-muted">Esta sección es exclusiva para administradores del sistema.</p>
                  <a href="<?= BASE_URL ?>dashboard" class="btn btn-primary px-4 rounded-pill">
                    <i class="fas fa-home me-2"></i>Volver al Dashboard
                  </a>
                </div>
              </div>
            </div>
          </div>
        <?php else: ?>
          <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header p-4 bg-transparent border-bottom-0">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h3 class="mb-1 fw-bold text-primary">Listado de Usuarios</h3>
                  <p class="mb-0 text-muted">Control de credenciales y niveles de acceso</p>
                </div>
                <button id="addUserBtn" class="btn btn-primary btn-lg px-4 shadow-sm">
                  <i class="fas fa-user-plus me-2"></i>Nuevo Usuario
                </button>
              </div>
            </div>

            <div class="card-body p-4">
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0">
                      <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" id="table-search" class="form-control border-start-0 ps-0 form-control-lg shadow-sm"
                      placeholder="Buscar por nombre, usuario o correo...">
                  </div>
                </div>
                <div class="col-md-6 text-md-end">
                  <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle rounded-pill px-4 shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="fas fa-file-export me-2"></i>Exportar Datos
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3">
                      <li>
                        <h6 class="dropdown-header fw-bold text-uppercase small opacity-50">Formatos Disponibles</h6>
                      </li>

                      <li><button id="exportCSVBtn" class="dropdown-item d-flex align-items-center py-2">
                          <i class="fas fa-file-csv text-success me-3 fs-5"></i> CSV (Texto delimitado)
                        </button></li>

                      <li><button id="exportExcelBtn" class="dropdown-item d-flex align-items-center py-2">
                          <i class="fas fa-file-excel text-success me-3 fs-5"></i> Excel (Libro de trabajo)
                        </button></li>

                      <li><button id="exportPDFBtn" class="dropdown-item d-flex align-items-center py-2">
                          <i class="fas fa-file-pdf text-danger me-3 fs-5"></i> PDF (Documento portable)
                        </button></li>

                      <li><button id="exportJSONBtn" class="dropdown-item d-flex align-items-center py-2">
                          <i class="fas fa-file-code text-info me-3 fs-5"></i> JSON (Estructura de datos)
                        </button></li>
                    </ul>
                  </div>
                </div>
              </div>

              <div class="table-responsive border rounded-3">
                <div id="users-table"></div>
              </div>
            </div>
          </div>

          <?php
          // Incluimos modales SOLO si es admin para no cargar código innecesario
          include __DIR__ . '/../partials/modals/modal_add_user.php';
          include __DIR__ . '/../partials/modals/modal_edit_user.php';
          ?>
        <?php endif; ?>
      </div>
    </main>
  </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layouts/navbar.php';
include __DIR__ . '/../partials/layouts/footer.php';
?>

<?php if ($isAdmin): ?>
  <script src="<?php echo BASE_URL; ?>assets/js/ajax/admin-users.js"></script>
<?php endif; ?>