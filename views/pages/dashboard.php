<?php
// Archivo: views/pages/dashboard.php
// -----------------------------------------------------------------------------
// Vista principal del sistema (Dashboard).
// Muestra información general del sistema como:
// - Totales de productos
// - Totales de usuarios (solo admin)
// - Actividad reciente
// - Accesos rápidos y navegación principal
// -----------------------------------------------------------------------------
/* =============================================================================
   1. SEGURIDAD Y SESIÓN
   ============================================================================= */
/**
 * require_once:
 * Verifica que el usuario tenga una sesión activa.
 * 
 * Si la sesión no existe o no es válida:
 * - Normalmente redirige al login
 * - Evita el acceso directo a la vista
 */
require_once __DIR__ . '/../inc/auth_check.php';

/**
 * Determina si el usuario es Administrador.
 * 
 * Convención del sistema:
 * - level_user = 1 → Administrador
 */
$isAdmin = (
    isset($_SESSION['user']['level_user']) &&
    $_SESSION['user']['level_user'] == 1
);

/**
 * Obtiene el nombre del usuario autenticado.
 * 
 * htmlspecialchars():
 * Evita ataques XSS al imprimir el nombre en la interfaz.
 */
$username = htmlspecialchars($_SESSION['user']['username'] ?? '');

/* =============================================================================
   2. INICIO DEL BUFFER DE SALIDA
   ============================================================================= */

/**
 * ob_start():
 * Inicia el buffer de salida.
 * 
 * Todo el HTML generado a partir de aquí:
 * - Se almacena en memoria
 * - No se imprime directamente
 * 
 * Posteriormente se inyecta dentro del layout principal (navbar.php).
 */
ob_start();

/* =============================================================================
   3. CONEXIÓN A BASE DE DATOS Y ESTADÍSTICAS
   ============================================================================= */

/**
 * Se incluye el modelo Database para obtener una conexión PDO.
 */
require_once __DIR__ . '/../../models/Database.php';

/**
 * Se establece la conexión a la base de datos.
 */
$pdo = (new Database())->getConnection();

/**
 * Obtiene el total de productos registrados.
 * 
 * fetchColumn():
 * Devuelve directamente el valor numérico del COUNT(*).
 * 
 * Operador ?: 
 * Si la consulta falla o devuelve null, se asigna 0.
 */
$totalProducts = $pdo->query(
    "SELECT COUNT(*) FROM products"
)->fetchColumn() ?: 0;

/**
 * Obtiene el total de usuarios registrados.
 * 
 * - Solo se ejecuta si el usuario es administrador
 * - Si no es admin, se asigna null para ocultar la tarjeta
 */
$totalUsers = $isAdmin
    ? ($pdo->query("SELECT COUNT(*) FROM users")->fetchColumn() ?: 0)
    : null;

/* =============================================================================
   4. LÓGICA DE NAVEGACIÓN
   ============================================================================= */

/**
 * Se obtiene la URL actual.
 * 
 * Si no existe, se asigna 'dashboard' por defecto.
 */
$uri = $_GET['url'] ?? 'dashboard';

/**
 * Se obtiene el primer segmento de la URL
 * para marcar el menú correspondiente como activo.
 */
$segment = explode('/', trim($uri, '/'))[0];
?>

<div class="container-fluid m-0 p-0 min-vh-100 bg-body-tertiary" data-bs-theme="auto">
    <div class="row g-0">

        <!-- Menú lateral del dashboard -->
        <?php require_once __DIR__ . '/../partials/layouts/laterals_menus/lateral_menu_dashboard.php'; ?>

        <main class="col-12 col-md-10">

            <!-- Encabezado superior -->
            <div class="bg-body shadow-sm border-bottom">
                <div class="container-fluid px-4 py-3">
                    <div class="d-flex justify-content-between align-items-center">

                        <!-- Título y breadcrumb -->
                        <div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-1">
                                    <li class="breadcrumb-item active text-primary fw-bold">
                                        Dashboard
                                    </li>
                                </ol>
                            </nav>

                            <h4 class="mb-0 fw-bold">Panel de Control</h4>
                            <p class="text-muted small mb-0">
                                Gestión general del inventario y sistema
                            </p>
                        </div>

                        <!-- Botón menú móvil -->
                        <div class="d-md-none">
                            <button class="btn btn-outline-primary border-2 rounded-3 shadow-sm px-3"
                                    type="button"
                                    data-bs-toggle="offcanvas"
                                    data-bs-target="#mobileMenu">
                                <i class="bi bi-list fs-4"></i>
                            </button>
                        </div>

                    </div>
                </div>
            </div>

            <!-- ==========================
                 TARJETAS DE RESUMEN
                 ========================== -->
            <div class="container-fluid px-4 py-4">

                <div class="row g-4 mb-4">

                    <!-- Total de productos -->
                    <div class="col-sm-6 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="bg-success bg-opacity-10 p-3 rounded-4">
                                        <i class="fas fa-box text-success fs-3"></i>
                                    </div>
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3">
                                        Activos
                                    </span>
                                </div>
                                <h6 class="text-muted fw-bold text-uppercase small">
                                    Total Productos
                                </h6>
                                <h2 class="mb-0 fw-bold"><?= $totalProducts ?></h2>
                            </div>
                        </div>
                    </div>

                    <!-- Total de usuarios (solo admin) -->
                    <?php if ($isAdmin): ?>
                    <div class="col-sm-6 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="bg-primary bg-opacity-10 p-3 rounded-4">
                                        <i class="fas fa-users text-primary fs-3"></i>
                                    </div>
                                </div>
                                <h6 class="text-muted fw-bold text-uppercase small">
                                    Usuarios Registrados
                                </h6>
                                <h2 class="mb-0 fw-bold"><?= $totalUsers ?></h2>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Tarjetas informativas (placeholder) -->
                    <div class="col-sm-6 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <div class="bg-warning bg-opacity-10 p-3 rounded-4 mb-3">
                                    <i class="fas fa-shopping-cart text-warning fs-3"></i>
                                </div>
                                <h6 class="text-muted fw-bold text-uppercase small">
                                    Órdenes Pendientes
                                </h6>
                                <h2 class="mb-0 fw-bold">--</h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <div class="bg-info bg-opacity-10 p-3 rounded-4 mb-3">
                                    <i class="fas fa-exchange-alt text-info fs-3"></i>
                                </div>
                                <h6 class="text-muted fw-bold text-uppercase small">
                                    Movimientos Hoy
                                </h6>
                                <h2 class="mb-0 fw-bold">--</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==========================
                     ACTIVIDAD RECIENTE
                     ========================== -->
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-header p-4 bg-transparent border-bottom-0">
                        <div class="row align-items-center g-3">

                            <div class="col-md-6">
                                <h5 class="mb-1 fw-bold">
                                    <i class="fas fa-history me-2 text-primary"></i>
                                    Actividad Reciente
                                </h5>
                                <p class="text-muted small mb-0">
                                    Últimos movimientos realizados en el sistema
                                </p>
                            </div>

                            <!-- Búsqueda y exportación -->
                            <div class="col-md-6 text-md-end">
                                <div class="d-flex gap-2 justify-content-md-end">

                                    <div class="input-group input-group-sm w-auto shadow-sm">
                                        <span class="input-group-text bg-body border-end-0">
                                            <i class="fas fa-search text-muted"></i>
                                        </span>
                                        <input type="text"
                                               id="table-search"
                                               class="form-control border-start-0"
                                               placeholder="Filtrar actividad...">
                                    </div>

                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle rounded-3 px-3 shadow-sm"
                                                type="button"
                                                data-bs-toggle="dropdown">
                                            <i class="fas fa-file-export me-1"></i> Exportar
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3">
                                            <li class="dropdown-header fw-bold text-uppercase small opacity-50">
                                                Formatos Disponibles
                                            </li>
                                            <li><button id="exportCSVBtn" class="dropdown-item">CSV</button></li>
                                            <li><button id="exportExcelBtn" class="dropdown-item">Excel</button></li>
                                            <li><button id="exportPDFBtn" class="dropdown-item">PDF</button></li>
                                            <li><button id="exportJSONBtn" class="dropdown-item">JSON</button></li>
                                        </ul>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contenedor donde se carga la actividad vía AJAX -->
                    <div class="card-body p-0">
                        <div id="recent-activity-table"
                             class="border-top"
                             style="min-height: 300px;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==========================
                 MENÚ OFFCANVAS (MÓVIL)
                 ========================== -->
            <!-- (misma lógica del menú lateral, adaptada a móvil) -->

        </main>
    </div>
</div>

<?php
/* =============================================================================
   5. CIERRE DEL BUFFER Y CARGA DEL LAYOUT
   ============================================================================= */

/**
 * Se obtiene todo el HTML generado
 * y se envía al layout principal.
 */
$content = ob_get_clean();
include __DIR__ . '/../partials/layouts/navbar.php';
?>

<!-- Script AJAX para la actividad reciente del dashboard -->
<script src="<?= BASE_URL ?>assets/js/ajax/dashboard-activity.js"></script>

<?php
/**
 * =============================================================================
 * REFERENCIA DE CLASES BOOTSTRAP UTILIZADAS
 * =============================================================================
 * bg-body-tertiary   → Fondo claro
 * g-4               → Espaciado entre columnas
 * rounded-4         → Bordes redondeados
 * bg-opacity-10     → Fondos suaves para iconos
 * shadow-sm / shadow-lg → Sombras
 * offcanvas         → Menú móvil
 * breadcrumb        → Navegación jerárquica
 * d-md-none         → Visible solo en móvil
 * =============================================================================
 */
?>
