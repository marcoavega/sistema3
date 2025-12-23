<?php
// Archivo: views/pages/settings.php

// Verifica si el usuario está logueado
require_once __DIR__ . '/../inc/auth_check.php';

// Obtener segmento para marcar menú activo
$uri = $_GET['url'] ?? 'settings';
$segment = explode('/', trim($uri, '/'))[0];

ob_start();

// Datos del usuario
$username = $_SESSION['user']['username'] ?? '';

// Conexión
require_once __DIR__ . '/../../models/Database.php';
$pdo = (new Database())->getConnection();

// Menú activo para el sidebar
$activeMenu = 'settings';
?>

<div class="container-fluid m-0 p-0 min-vh-100 bg-body-tertiary" data-bs-theme="auto">
    <div class="row g-0">
        
        <?php require_once __DIR__ . '/../partials/layouts/laterals_menus/lateral_menu_dashboard.php'; ?>

        <main class="col-12 col-md-10">

            <div class="bg-body shadow-sm border-bottom">
                <div class="container-fluid px-4 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-2 small">
                                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-decoration-none">Dashboard</a></li>
                                <li class="breadcrumb-item active text-body">Configuración</li>
                            </ol>
                        </nav>
                        <h4 class="mb-0 fw-bold text-body"><i class="bi bi-gear me-2 text-primary"></i>Configuración</h4>
                    </div>

                    <div class="d-md-none">
                        <button class="btn btn-outline-primary shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-controls="mobileMenu">
                            <i class="bi bi-list fs-5"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="container-fluid px-4 py-4">
                <div class="row g-4">
                    
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 bg-body mb-4">
                            <div class="card-header bg-transparent border-0 pt-4 px-4">
                                <h6 class="mb-0 fw-bold text-body">Secciones</h6>
                            </div>
                            <div class="card-body p-2">
                                <div class="list-group list-group-flush">
                                    <a href="<?= BASE_URL ?>settings" class="list-group-item list-group-item-action border-0 py-3 px-4 d-flex align-items-center bg-primary-subtle text-primary border-start border-4 border-primary rounded-2">
                                        <i class="bi bi-gear-fill me-3 fs-5"></i> 
                                        <span class="fw-bold">General</span>
                                    </a>
                                    <a href="<?= BASE_URL ?>currencies" class="list-group-item list-group-item-action border-0 py-3 px-4 d-flex align-items-center bg-transparent">
                                        <i class="bi bi-currency-exchange me-3 fs-5 text-secondary"></i> 
                                        <span class="fw-medium">Monedas</span>
                                    </a>
                                    <a href="<?= BASE_URL ?>exchange_rates" class="list-group-item list-group-item-action border-0 py-3 px-4 d-flex align-items-center bg-transparent">
                                        <i class="bi bi-graph-up me-3 fs-5 text-secondary"></i> 
                                        <span class="fw-medium">Tipos de Cambio</span>
                                    </a>
                                    <a href="<?= BASE_URL ?>warehouses" class="list-group-item list-group-item-action border-0 py-3 px-4 d-flex align-items-center bg-transparent">
                                        <i class="bi bi-buildings me-3 fs-5 text-secondary"></i> 
                                        <span class="fw-medium">Almacenes</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 bg-body p-4">
                            <h5 class="fw-bold mb-4 text-body">Ajustes del Sistema</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="p-4 border rounded-4 bg-body-tertiary h-100 d-flex flex-column justify-content-between">
                                        <div>
                                            <h6 class="fw-bold mb-2">Monedas</h6>
                                            <p class="small text-muted mb-3">Gestión de códigos ISO y símbolos de moneda.</p>
                                        </div>
                                        <a href="<?= BASE_URL ?>currencies" class="btn btn-sm btn-outline-primary rounded-pill w-100 mt-2">Configurar</a>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-4 border rounded-4 bg-body-tertiary h-100 d-flex flex-column justify-content-between">
                                        <div>
                                            <h6 class="fw-bold mb-2">Tipos de Cambio</h6>
                                            <p class="small text-muted mb-3">Registro de paridad diaria y valor de conversión.</p>
                                        </div>
                                        <a href="<?= BASE_URL ?>exchange_rates" class="btn btn-sm btn-primary rounded-pill w-100 mt-2">Administrar</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
                <div class="offcanvas-header border-bottom">
                    <h5 class="offcanvas-title fw-bold" id="mobileMenuLabel">
                        <i class="bi bi-grid-fill text-primary me-2"></i>Sistema
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body p-0">
                    <div class="list-group list-group-flush mt-2">
                        
                        <a href="<?= BASE_URL ?>dashboard" class="list-group-item list-group-item-action border-0 py-3 px-4 d-flex align-items-center bg-transparent">
                            <i class="bi bi-speedometer2 me-3 fs-5 text-secondary"></i> 
                            <span class="fw-medium">Panel de Control</span>
                        </a>

                        <a href="<?= BASE_URL ?>settings" class="list-group-item list-group-item-action border-0 py-3 px-4 d-flex align-items-center bg-primary-subtle text-primary border-start border-4 border-primary">
                            <i class="bi bi-gear me-3 fs-5"></i> 
                            <span class="fw-bold">Configuración</span>
                        </a>

                        <a href="<?= BASE_URL ?>exchange_rates" class="list-group-item list-group-item-action border-0 py-3 px-4 d-flex align-items-center bg-transparent">
                            <i class="fas fa-coins me-3 fs-5 text-secondary"></i> 
                            <span class="fw-medium">Tipo de Cambio</span>
                        </a>

                    </div>
                    
                    <div class="mt-auto border-top p-3 text-center">
                        <small class="text-muted">Conectado como:</small><br>
                        <strong><?= htmlspecialchars($username) ?></strong>
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