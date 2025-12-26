<?php
/**
 * Archivo: views/pages/exchange_rates.php
 * --------------------------------------------------------------------------
 * Vista encargada de mostrar y administrar:
 * - Monedas disponibles en el sistema
 * - Historial de tasas de cambio
 * - Permisos de acciones según el nivel de usuario
 * --------------------------------------------------------------------------
 */

/* ==========================================================================
   1. CONTROL DE ACCESO Y CARGA DE DEPENDENCIAS
   ========================================================================== */

// Verifica que el usuario tenga una sesión activa.
// Si no está autenticado, normalmente redirige al login.
require_once __DIR__ . '/../inc/auth_check.php';

// Carga el controlador que contiene la lógica de negocio
// relacionada con monedas y tipos de cambio.
require_once __DIR__ . '/../../controllers/ExchangeRatesController.php';

/* ==========================================================================
   2. GESTIÓN DE PERMISOS SEGÚN EL NIVEL DE USUARIO
   ========================================================================== */

// Se obtiene el nivel del usuario desde la sesión.
// Si no existe, por defecto se asigna nivel 3 (solo consulta).
// Nivel 1 = Administrador
// Nivel 2 = Editor
// Nivel 3 = Consulta
$userLevel = $_SESSION['user']['level_user'] ?? 3;

// Permiso para agregar monedas y tasas:
// - Admin (1)
// - Editor (2)
$canAdd = ($userLevel == 1 || $userLevel == 2);

// Permiso para eliminar registros:
// - Solo el Administrador (1)
$canDel = ($userLevel == 1);

/* ==========================================================================
   3. OBTENCIÓN DE DATOS DESDE EL CONTROLADOR
   ========================================================================== */

// Se instancia el controlador de tipos de cambio
$controller = new ExchangeRatesController();

// Se obtiene toda la información necesaria para la vista
$data = $controller->getViewData();

// Listado de monedas (ejemplo: USD, EUR, MXN)
$currencies = $data['currencies'] ?? [];

// Historial de tasas de cambio registradas
$rates = $data['rates'] ?? [];

/* ==========================================================================
   4. VARIABLES AUXILIARES PARA EL LAYOUT
   ========================================================================== */

// Segmento utilizado para resaltar el menú activo
$segment = 'exchange_rates';

// Nombre del usuario autenticado (para mostrar en la UI)
$username = $_SESSION['user']['username'] ?? 'Usuario';

// Se inicia el buffer de salida para capturar todo el HTML
// antes de enviarlo al layout principal
ob_start();
?>

<div class="container-fluid m-0 p-0 min-vh-100 bg-body-tertiary" data-bs-theme="auto">
    <div class="row g-0">
        
        <!-- Menú lateral del dashboard -->
        <?php require_once __DIR__ . '/../partials/layouts/laterals_menus/lateral_menu_dashboard.php'; ?>

        <main class="col-12 col-md-10">
            
            <!-- Encabezado superior con breadcrumb y título -->
            <div class="bg-body shadow-sm border-bottom">
                <div class="container-fluid px-4 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <!-- Ruta de navegación -->
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-2 small">
                                <li class="breadcrumb-item">
                                    <a href="<?= BASE_URL ?>dashboard" class="text-decoration-none">
                                        Dashboard
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-body">
                                    Tipo de Cambio
                                </li>
                            </ol>
                        </nav>

                        <!-- Título principal de la vista -->
                        <h4 class="mb-0 fw-bold text-body">
                            <i class="fas fa-coins me-2 text-primary"></i>
                            Gestión de Divisas
                        </h4>
                    </div>

                    <!-- Botón para mostrar menú lateral en móviles -->
                    <div class="d-md-none">
                        <button class="btn btn-outline-primary shadow-sm"
                                type="button"
                                data-bs-toggle="offcanvas"
                                data-bs-target="#mobileMenu">
                            <i class="bi bi-list fs-5"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Contenido principal -->
            <div class="container-fluid px-4 py-4">
                
                <!-- Mensaje flash de éxito -->
                <?php if (!empty($_SESSION['flash_success'])): ?>
                    <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
                        <i class="bi bi-check-circle me-2"></i>
                        <?= htmlspecialchars($_SESSION['flash_success']) ?>
                    </div>
                    <?php unset($_SESSION['flash_success']); ?>
                <?php endif; ?>

                <div class="row g-4">
                    
                    <!-- =======================
                         COLUMNA DE MONEDAS
                         ======================= -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 bg-body h-100">
                            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold text-body">Monedas</h6>

                                <!-- Botón para agregar moneda (solo si tiene permiso) -->
                                <?php if ($canAdd): ?>
                                    <button class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalAddCurrency">
                                        <i class="fas fa-plus me-1"></i> Nueva
                                    </button>
                                <?php endif; ?>
                            </div>

                            <!-- Listado de monedas -->
                            <div class="card-body p-3">
                                <ul id="currencies-list" class="list-group list-group-flush">
                                    <?php foreach ($currencies as $curr): ?>
                                        <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center border-0 px-0 py-2 text-body">
                                            <div>
                                                <!-- Código de la moneda -->
                                                <span class="fw-bold">
                                                    <?= htmlspecialchars($curr['code']) ?>
                                                </span>

                                                <!-- Nombre completo de la moneda -->
                                                <small class="text-muted d-block">
                                                    <?= htmlspecialchars($curr['name']) ?>
                                                </small>
                                            </div>

                                            <!-- Símbolo de la moneda -->
                                            <span class="badge bg-primary-subtle text-primary rounded-pill small">
                                                <?= $curr['symbol'] ?>
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- =======================
                         COLUMNA DE TASAS
                         ======================= -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 bg-body">

                            <!-- Encabezado de la tabla -->
                            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold text-body">Histórico de Tasas</h6>

                                <!-- Acciones -->
                                <div class="d-flex gap-2">
                                    <!-- Exportación de datos -->
                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm dropdown-toggle"
                                                data-bs-toggle="dropdown">
                                            <i class="fas fa-file-export me-1"></i> Exportar
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                            <li>
                                                <button id="exportCSVBtn" class="dropdown-item py-2 small">
                                                    <i class="fas fa-file-csv text-success me-2"></i> CSV
                                                </button>
                                            </li>
                                            <li>
                                                <button id="exportPDFBtn" class="dropdown-item py-2 small">
                                                    <i class="fas fa-file-pdf text-danger me-2"></i> PDF
                                                </button>
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- Botón para agregar nueva tasa -->
                                    <?php if ($canAdd): ?>
                                        <button class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalAddRate">
                                            <i class="fas fa-plus me-1"></i> Nuevo Valor
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Filtros por rango de fechas -->
                            <div class="card-body border-bottom bg-body-tertiary">
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-4">
                                        <label class="small text-muted mb-1">Desde:</label>
                                        <input type="date" id="filter-date-start"
                                               class="form-control form-control-sm border-0 shadow-sm">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="small text-muted mb-1">Hasta:</label>
                                        <input type="date" id="filter-date-end"
                                               class="form-control form-control-sm border-0 shadow-sm">
                                    </div>
                                    <div class="col-md-4">
                                        <button id="btn-clear-filter"
                                                class="btn btn-sm btn-outline-secondary w-100 shadow-sm border-0 bg-body">
                                            <i class="bi bi-x-circle me-1"></i> Limpiar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabla de tasas -->
                            <div class="card-body px-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-body-secondary text-muted small">
                                            <tr>
                                                <th class="ps-4">Fecha</th>
                                                <th>Moneda</th>
                                                <th>Valor</th>
                                                <th class="text-end pe-4">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="rates-tbody">
                                            <?php foreach ($rates as $rate): ?>
                                                <tr>
                                                    <!-- Fecha de la tasa -->
                                                    <td class="ps-4">
                                                        <span class="small">
                                                            <?= date('d/m/Y', strtotime($rate['date_rate'])) ?>
                                                        </span>
                                                    </td>

                                                    <!-- Código de moneda -->
                                                    <td>
                                                        <span class="badge bg-light text-dark border">
                                                            <?= htmlspecialchars($rate['currency_code']) ?>
                                                        </span>
                                                    </td>

                                                    <!-- Valor de la tasa -->
                                                    <td>
                                                        <span class="fw-bold text-primary">
                                                            <?= number_format($rate['rate_value'], 4) ?>
                                                        </span>
                                                    </td>

                                                    <!-- Acciones disponibles -->
                                                    <td class="text-end pe-4">
                                                        <div class="btn-group">
                                                            <button class="btn btn-sm btn-light border shadow-sm">
                                                                <i class="bi bi-pencil"></i>
                                                            </button>

                                                            <!-- Eliminar solo para admin -->
                                                            <?php if ($canDel): ?>
                                                                <button class="btn btn-sm btn-light border text-danger shadow-sm">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>
</div>

<?php
/* ==========================================================================
   5. CARGA DE MODALES
   ========================================================================== */

// Modal para agregar una nueva moneda
include __DIR__ . '/../partials/modals/add_currency_modal.php';

// Modal para agregar una nueva tasa de cambio
include __DIR__ . '/../partials/modals/add_rate_modal.php';

/* ==========================================================================
   6. FINALIZACIÓN DEL BUFFER Y CARGA DEL LAYOUT PRINCIPAL
   ========================================================================== */

// Se obtiene todo el HTML generado
$content = ob_get_clean();

// Se carga el layout principal que renderiza la vista
include __DIR__ . '/../partials/layouts/navbar.php';
?>

<!-- Script JavaScript para manejo AJAX de tasas de cambio -->
<script src="<?= BASE_URL ?>assets/js/ajax/exchange_rates.js"></script>

<?php
/**
 * --------------------------------------------------------------------------
 * GUÍA DE CLASES BOOTSTRAP 5 UTILIZADAS EN ESTA VISTA
 * --------------------------------------------------------------------------
 * container-fluid / row / col     → Sistema de grillas responsivo
 * bg-body-tertiary                → Fondo adaptable a modo claro/oscuro
 * card / shadow-sm / rounded-4    → Tarjetas con diseño moderno
 * d-flex / justify-content-between→ Alineación flexible
 * table-hover / align-middle      → Mejor lectura en tablas
 * badge / rounded-pill            → Etiquetas tipo cápsula
 * d-md-none                       → Ocultar elementos en desktop
 * --------------------------------------------------------------------------
 */
?>
