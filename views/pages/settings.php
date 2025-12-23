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
                        <button class="btn btn-outline-primary shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
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
                        <div class="card border-0 shadow-sm rounded-4 bg-body p-4 text-body">
                            <h5 class="fw-bold mb-4">Ajustes del Sistema</h5>
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
                    <h5 class="offcanvas-title fw-bold text-body" id="mobileMenuLabel">
                        <i class="bi bi-list text-primary me-2"></i>Menú Principal
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
                        <span class="badge border px-3 py-2 rounded-pill shadow-sm">
                            <i class="bi bi-person-circle text-primary me-2"></i><?= htmlspecialchars($username) ?>
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