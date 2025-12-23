<?php
// Archivo: views/pages/admin_users.php
require_once __DIR__ . '/../inc/auth_check.php';

$isAdmin = (isset($_SESSION['user']['level_user']) && $_SESSION['user']['level_user'] == 1);
$username = htmlspecialchars($_SESSION['user']['username']);

ob_start();

require_once __DIR__ . '/../../models/Database.php';
$pdo = (new Database())->getConnection();

$stmt = $pdo->query("SELECT id_level_user, description_level FROM levels_users ORDER BY level");
$levels = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Segmento para menú activo
$uri = $_GET['url'] ?? 'admin_users';
$segment = explode('/', trim($uri, '/'))[0];
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
                        <button class="btn btn-outline-primary shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
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
                                    <div class="mb-4"><i class="fas fa-shield-alt text-warning" style="font-size: 4rem; opacity: 0.5;"></i></div>
                                    <h3 class="fw-bold text-body">Acceso Restringido</h3>
                                    <p class="text-muted">Esta sección es exclusiva para administradores.</p>
                                    <a href="<?= BASE_URL ?>dashboard" class="btn btn-primary px-4 rounded-pill">Volver</a>
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
                                    <p class="mb-0 text-muted small">Control de credenciales</p>
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
                                        <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-search text-muted"></i></span>
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
                
                <div class="offcanvas-body p-0 d-flex flex-column h-100">
                    <div class="list-group list-group-flush mt-2">
                        <?php 
                        $menuToRender = $menuItems ?? [];
                        if (!empty($menuToRender)): 
                            foreach ($menuToRender as $route => $item): 
                                $isActiveParent = ($segment === $route);
                                $isSubActive = isset($item['submenu']) && array_key_exists($segment, $item['submenu']);
                                $itemIcon = htmlspecialchars($item['icon'] ?? 'circle');
                                $itemLabel = htmlspecialchars($item['label'] ?? $route);
                        ?>
                                <a href="<?= BASE_URL . $route ?>" 
                                   class="list-group-item list-group-item-action border-0 py-3 px-4 d-flex align-items-center <?= ($isActiveParent || $isSubActive) ? 'bg-primary-subtle text-primary border-start border-4 border-primary fw-bold' : 'text-body' ?>">
                                    <i class="bi bi-<?= $itemIcon ?> me-3 fs-5"></i> 
                                    <?= $itemLabel ?>
                                </a>

                                <?php if (isset($item['submenu'])): ?>
                                    <div class="bg-body-tertiary shadow-inner">
                                        <?php foreach ($item['submenu'] as $subRoute => $subItem): 
                                            $isSubItemActive = ($segment === $subRoute);
                                            $subIcon = htmlspecialchars($subItem['icon'] ?? 'circle');
                                            $subLabel = htmlspecialchars($subItem['label'] ?? $subRoute);
                                        ?>
                                            <a href="<?= BASE_URL . $subRoute ?>" 
                                               class="list-group-item list-group-item-action border-0 py-2 ps-5 d-flex align-items-center <?= $isSubItemActive ? 'text-primary fw-bold' : 'text-muted' ?>" style="font-size: 0.85rem;">
                                                <i class="bi bi-<?= $subIcon ?> me-3 fs-6"></i> 
                                                <?= $subLabel ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                        <?php 
                            endforeach; 
                        else: 
                        ?>
                            <div class="p-4 text-center text-muted">
                                <small>No se pudo cargar el menú dinámico.</small>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mt-auto border-top p-4 text-center">
                        <small class="text-muted d-block mb-1">Usuario conectado:</small>
                        <span class="badge border px-3 py-2 rounded-pill shadow-sm text-body">
                            <i class="bi bi-person-circle text-primary me-2"></i><?= $username ?>
                        </span>
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

<?php if ($isAdmin): ?>
    <script src="<?= BASE_URL ?>assets/js/ajax/admin-users.js"></script>
<?php endif; ?>