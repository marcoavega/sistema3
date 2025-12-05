<?php
//Archivo: views/pages/settings.php

// Verifica si el usuario está logueado, si no, redirige
require_once __DIR__ . '/../inc/auth_check.php';


// Obtener segmento para marcar menú activo
$uri = $_GET['url'] ?? 'settings';
$segment = explode('/', trim($uri, '/'))[0];

ob_start();

// Nombre de usuario (escapado)
$username = htmlspecialchars($_SESSION['user']['username']);

// Conexión (por si necesitas datos)
require_once __DIR__ . '/../../models/Database.php';
$pdo = (new Database())->getConnection();

// Mantener la misma inclusión del lateral que usa dashboard

?>

<!-- Contenedor principal — idéntico a dashboard -->
<div class="container-fluid m-0 p-0 min-vh-100" data-bs-theme="auto">
    <div class="row g-0">

        <!-- Menú lateral para pantallas medianas y grandes (sin cambiar, muestra "Sistema") -->
         <!-- Menú lateral para pantallas medianas y grandes -->
       <?php require_once __DIR__ . '/../partials/layouts/laterals_menus/lateral_menu_dashboard.php'; ?>

        <!-- Contenido principal (mismo diseño que dashboard) -->
        <main class="col-12 col-md-10">

            <!-- Header / breadcrumb (igual que Dashboard: "Panel de Control") -->
            <div class="bg-body shadow-sm border-bottom">
                <div class="container-fluid px-4 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-2">
                                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-decoration-none">Panel de Control</a></li>
                                    <li class="breadcrumb-item active">Configuración</li>
                                </ol>
                            </nav>
                            <h4 class="mb-0 fw-bold">Panel de Control</h4>
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

            <!-- Offcanvas móvil (idéntico) -->
            <div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="mobileMenu">
                <div class="offcanvas-header bg-primary-subtle">
                    <h5 class="offcanvas-title"><i class="bi bi-speedometer2 me-2"></i>Sistema</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body bg-body">
                    <ul class="nav flex-column">
                        <?php foreach ($menuItems as $route => $item): ?>
                            <li class="nav-item mb-2">
                                <a class="nav-link text-body d-flex align-items-center px-3 py-2 rounded-3 <?= $segment === $route ? 'active bg-primary text-white' : '' ?>"
                                   href="<?= BASE_URL . $route ?>">
                                    <i class="bi bi-<?= $item['icon'] ?> me-3 fs-5"></i>
                                    <?= $item['label'] ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Contenido de configuración: sólo esta sección cambia respecto al dashboard -->
            <div class="container-fluid px-4 py-4">
                <div class="row g-4">

                    <!-- Columna izquierda: accesos rápidos (estilo similar al dashboard) -->
                    <div class="col-md-4">
                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Configuración</h6>
                                <p class="small text-muted mb-3">Accesos rápidos a las opciones de ajuste del sistema.</p>

                                <div class="list-group">
                                    <a href="<?= BASE_URL ?>settings" class="list-group-item list-group-item-action active">
                                        <i class="bi bi-gear me-2"></i> General
                                    </a>

                                    <a href="<?= BASE_URL ?>currencies" class="list-group-item list-group-item-action">
                                        <i class="bi bi-currency-exchange me-2"></i> Monedas
                                        <small class="text-muted d-block">Gestionar monedas del sistema</small>
                                    </a>

                                    <a href="<?= BASE_URL ?>exchange_rates" class="list-group-item list-group-item-action">
                                        <i class="bi bi-calendar2-check me-2"></i> Tipos de Cambio
                                        <small class="text-muted d-block">Registrar paridad diaria</small>
                                    </a>

                                    <a href="<?= BASE_URL ?>warehouses" class="list-group-item list-group-item-action">
                                        <i class="bi bi-boxes me-2"></i> Almacenes
                                        <small class="text-muted d-block">Administrar almacenes estáticos</small>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Estado breve -->
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="fw-semibold mb-2">Nota</h6>
                                <p class="small text-muted mb-0">No se modifica la estructura del navbar ni del sidebar para mantener la integridad del sistema.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Columna derecha: contenido principal de Configuración -->
                    <div class="col-md-8">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3">Ajustes del Sistema</h5>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded-3 h-100 d-flex flex-column justify-content-between">
                                            <div>
                                                <h6 class="mb-1">Monedas</h6>
                                                <p class="small text-muted mb-2">Crear / editar / eliminar monedas (ej. USD, MXN).</p>
                                            </div>
                                            <div class="text-end">
                                                <a href="<?= BASE_URL ?>currencies" class="btn btn-outline-primary btn-sm">Ir a Monedas</a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="p-3 border rounded-3 h-100 d-flex flex-column justify-content-between">
                                            <div>
                                                <h6 class="mb-1">Tipos de Cambio</h6>
                                                <p class="small text-muted mb-2">Registrar la paridad diaria (ej. 1 USD = 18.50 MXN). Guarda histórico.</p>
                                            </div>
                                            <div class="text-end">
                                                <a href="<?= BASE_URL ?>exchange_rates" class="btn btn-primary btn-sm">Administrar</a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="p-3 border rounded-3 d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">Almacenes</h6>
                                                <p class="small text-muted mb-0">Lista estática de almacenes del sistema. Agregar / editar / borrar registros desde aquí.</p>
                                            </div>
                                            <div>
                                                <a href="<?= BASE_URL ?>warehouses" class="btn btn-outline-secondary btn-sm">Ir a Almacenes</a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mt-3 p-3 border rounded-3 bg-body-tertiary">
                                            <h6 class="mb-1">Otras opciones</h6>
                                            <p class="small text-muted mb-0">En el futuro puedes añadir: impuestos, templates, integraciones, etc.</p>
                                        </div>
                                    </div>
                                </div> <!-- /.row -->
                            </div> <!-- /.card-body -->
                        </div> <!-- /.card -->
                    </div> <!-- /.col-md-8 -->

                </div> <!-- /.row -->
            </div> <!-- /.container-fluid -->

        </main>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layouts/navbar.php';
