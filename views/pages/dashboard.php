<?php
// Archivo: views/pages/dashboard.php
require_once __DIR__ . '/../inc/auth_check.php';

// Determinamos nivel de acceso
$isAdmin = (isset($_SESSION['user']['level_user']) && $_SESSION['user']['level_user'] == 1);
$username = htmlspecialchars($_SESSION['user']['username']);

// Inicia el buffer
ob_start();

// Conexión y Consultas Rápidas
require_once __DIR__ . '/../../models/Database.php';
$pdo = (new Database())->getConnection();

// Estadísticas básicas
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn() ?: 0;
$totalUsers = $isAdmin ? ($pdo->query("SELECT COUNT(*) FROM users")->fetchColumn() ?: 0) : null;

// Segmento para menú activo
$uri = $_GET['url'] ?? 'dashboard';
$segment = explode('/', trim($uri, '/'))[0];
?>

<div class="container-fluid m-0 p-0 min-vh-100" data-bs-theme="auto">
    <div class="row g-0">

        <?php require_once __DIR__ . '/../partials/layouts/laterals_menus/lateral_menu_dashboard.php'; ?>

        <main class="col-12 col-md-10 bg-body-tertiary">

            <div class="bg-body shadow-sm border-bottom">
                <div class="container-fluid px-4 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-1">
                                    <li class="breadcrumb-item active text-primary fw-bold">Dashboard</li>
                                </ol>
                            </nav>
                            <h4 class="mb-0 fw-bold">Panel de Control</h4>
                            <p class="text-muted small mb-0">Gestión general del inventario y sistema</p>
                        </div>
                        <div class="d-md-none">
                            <button class="btn btn-outline-primary border-2 rounded-circle" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                                <i class="fas fa-bars"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid px-4 py-4">

                <div class="row g-4 mb-4">
                    <div class="col-sm-6 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="bg-success bg-opacity-10 p-3 rounded-4">
                                        <i class="fas fa-box text-success fs-3"></i>
                                    </div>
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3">Activos</span>
                                </div>
                                <h6 class="text-muted fw-bold text-uppercase small">Total Productos</h6>
                                <h2 class="mb-0 fw-bold"><?= $totalProducts ?></h2>
                            </div>
                        </div>
                    </div>

                    <?php if ($isAdmin): ?>
                    <div class="col-sm-6 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="bg-primary bg-opacity-10 p-3 rounded-4">
                                        <i class="fas fa-users text-primary fs-3"></i>
                                    </div>
                                </div>
                                <h6 class="text-muted fw-bold text-uppercase small">Usuarios Registrados</h6>
                                <h2 class="mb-0 fw-bold"><?= $totalUsers ?></h2>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="col-sm-6 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="bg-warning bg-opacity-10 p-3 rounded-4">
                                        <i class="fas fa-shopping-cart text-warning fs-3"></i>
                                    </div>
                                </div>
                                <h6 class="text-muted fw-bold text-uppercase small">Órdenes Pendientes</h6>
                                <h2 class="mb-0 fw-bold">--</h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="bg-info bg-opacity-10 p-3 rounded-4">
                                        <i class="fas fa-exchange-alt text-info fs-3"></i>
                                    </div>
                                </div>
                                <h6 class="text-muted fw-bold text-uppercase small">Movimientos Hoy</h6>
                                <h2 class="mb-0 fw-bold">--</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header p-4 bg-transparent border-bottom-0">
    <div class="row align-items-center g-3">
        <div class="col-md-6">
            <h5 class="mb-1 fw-bold"><i class="fas fa-history me-2 text-primary"></i>Actividad Reciente</h5>
            <p class="text-muted small mb-0">Últimos movimientos realizados en el sistema</p>
        </div>
        
        <div class="col-md-6 text-md-end">
            <div class="d-flex gap-2 justify-content-md-end">
                <div class="input-group input-group-sm w-auto shadow-sm">
                    <span class="input-group-text bg-body border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="table-search" class="form-control border-start-0" placeholder="Filtrar actividad...">
                </div>

                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle rounded-3 px-3 shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-file-export me-1"></i> Exportar
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3">
                        <li><h6 class="dropdown-header fw-bold text-uppercase small opacity-50">Formatos Disponibles</h6></li>
                        
                        <li><button id="exportCSVBtn" class="dropdown-item d-flex align-items-center py-2">
                            <i class="fas fa-file-csv text-success me-3 fs-5"></i> CSV
                        </button></li>
                        
                        <li><button id="exportExcelBtn" class="dropdown-item d-flex align-items-center py-2">
                            <i class="fas fa-file-excel text-success me-3 fs-5"></i> Excel
                        </button></li>
                        
                        <li><button id="exportPDFBtn" class="dropdown-item d-flex align-items-center py-2">
                            <i class="fas fa-file-pdf text-danger me-3 fs-5"></i> PDF
                        </button></li>
                        
                        <li><button id="exportJSONBtn" class="dropdown-item d-flex align-items-center py-2">
                            <i class="fas fa-file-code text-info me-3 fs-5"></i> JSON
                        </button></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

                    <div class="card-body p-0">
                        <div id="recent-activity-table" class="border-top"></div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<div class="offcanvas offcanvas-start d-md-none border-0 shadow" tabindex="-1" id="mobileMenu">
    <div class="offcanvas-header bg-primary text-white py-4">
        <h5 class="offcanvas-title fw-bold"><i class="fas fa-boxes me-2"></i>Inventario Pro</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0 bg-body">
        <div class="list-group list-group-flush pt-3">
            <a href="<?= BASE_URL ?>dashboard" class="list-group-item list-group-item-action border-0 px-4 py-3 <?= $segment === 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt me-3"></i>Dashboard
            </a>
            </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layouts/navbar.php';
?>

<script src="<?= BASE_URL ?>assets/js/ajax/dashboard-activity.js"></script>