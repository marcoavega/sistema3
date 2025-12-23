<?php
// Archivo: views/pages/admin_users.php
require_once __DIR__ . '/../inc/auth_check.php';

$isAdmin = (isset($_SESSION['user']['level_user']) && $_SESSION['user']['level_user'] == 1);

ob_start();

require_once __DIR__ . '/../../models/Database.php';
$pdo = (new Database())->getConnection();

$stmt = $pdo->query("SELECT id_level_user, description_level FROM levels_users ORDER BY level");
$levels = $stmt->fetchAll(PDO::FETCH_ASSOC);

$username = htmlspecialchars($_SESSION['user']['username']);
?>

<div class="container-fluid m-0 p-0 min-vh-100 bg-body-tertiary" data-bs-theme="auto">
    <div class="row g-0">

        <?php require_once __DIR__ . '/../partials/layouts/laterals_menus/lateral_menu_users.php'; ?>

        <main class="col-12 col-md-10">

            <div class="bg-body shadow-sm border-bottom">
                <div class="container-fluid px-4 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-2 small">
                                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-decoration-none">Dashboard</a></li>
                                <li class="breadcrumb-item active text-body">Administración</li>
                            </ol>
                        </nav>
                        <h4 class="mb-0 fw-bold text-body"><i class="bi bi-people-fill me-2 text-primary"></i>Gestión de Usuarios</h4>
                    </div>

                    <div class="d-md-none">
                        <button class="btn btn-outline-primary shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-controls="mobileMenu">
                            <i class="bi bi-list fs-5"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="container-fluid px-4 py-4">
                <?php if (!$isAdmin): ?>
                    <div class="row justify-content-center mt-5">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-lg rounded-4 text-center py-5 bg-body">
                                <div class="card-body">
                                    <div class="mb-4">
                                        <i class="fas fa-shield-alt text-warning" style="font-size: 4rem; opacity: 0.5;"></i>
                                    </div>
                                    <h3 class="fw-bold text-body">Acceso Restringido</h3>
                                    <p class="text-muted">Esta sección es exclusiva para administradores del sistema.</p>
                                    <a href="<?= BASE_URL ?>dashboard" class="btn btn-primary px-4 rounded-pill">
                                        <i class="fas fa-home me-2"></i>Volver al Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card shadow-sm border-0 rounded-4 bg-body">
                        <div class="card-header p-4 bg-transparent border-bottom-0">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <div>
                                    <h5 class="mb-1 fw-bold text-primary">Listado de Usuarios</h5>
                                    <p class="mb-0 text-muted small">Control de credenciales y niveles de acceso</p>
                                </div>
                                <button id="addUserBtn" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                    <i class="fas fa-user-plus me-2"></i>Nuevo Usuario
                                </button>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text bg-body-tertiary border-end-0">
                                            <i class="fas fa-search text-muted"></i>
                                        </span>
                                        <input type="text" id="table-search" class="form-control border-start-0 bg-body-tertiary shadow-none" placeholder="Buscar usuario...">
                                    </div>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary dropdown-toggle rounded-pill px-4" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-file-export me-2"></i>Exportar
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                            <li><button id="exportCSVBtn" class="dropdown-item py-2 small"><i class="fas fa-file-csv text-success me-2"></i> CSV</button></li>
                                            <li><button id="exportExcelBtn" class="dropdown-item py-2 small"><i class="fas fa-file-excel text-success me-2"></i> Excel</button></li>
                                            <li><button id="exportPDFBtn" class="dropdown-item py-2 small"><i class="fas fa-file-pdf text-danger me-2"></i> PDF</button></li>
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
                    include __DIR__ . '/../partials/modals/modal_add_user.php';
                    include __DIR__ . '/../partials/modals/modal_edit_user.php';
                    ?>
                <?php endif; ?>
            </div>

            <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
                <div class="offcanvas-header border-bottom">
                    <h5 class="offcanvas-title fw-bold" id="mobileMenuLabel">
                        <i class="bi bi-people-fill text-primary me-2"></i>Usuarios
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body p-0">
                    <div class="list-group list-group-flush mt-2">
                        <a href="<?= BASE_URL ?>admin_users" class="list-group-item list-group-item-action border-0 py-3 px-4 d-flex align-items-center bg-primary-subtle text-primary border-start border-4 border-primary">
                            <i class="bi bi-person-lines-fill me-3 fs-5"></i> 
                            <span class="fw-bold">Gestión de Usuarios</span>
                        </a>
                        </div>
                    <div class="mt-auto border-top p-3 text-center">
                        <small class="text-muted">Conectado como:</small><br>
                        <strong><?= $username ?></strong>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layouts/navbar.php'; 
?>

<?php if (isset($isAdmin) && $isAdmin): ?>
    <script src="<?= BASE_URL ?>assets/js/ajax/admin-users.js"></script>
<?php endif; ?>