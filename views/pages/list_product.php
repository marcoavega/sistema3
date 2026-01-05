<?php
/************************************************************
 * Archivo: views/pages/list_product.php
 *
 * Esta vista se encarga de mostrar:
 * - El inventario de productos
 * - Estadísticas generales
 * - Tabla con filtros, búsqueda y exportación
 * 
 * Usa PHP para la lógica del servidor
 * y HTML + Bootstrap para la interfaz visual.
 ************************************************************/

/*
 |----------------------------------------------------------
 | Verificación de sesión activa
 |----------------------------------------------------------
 | Este archivo revisa si el usuario está autenticado.
 | Si no lo está, se bloquea el acceso o redirige.
 */
require_once __DIR__ . '/../inc/auth_check.php';

/*
 |----------------------------------------------------------
 | Obtención de la URL actual
 |----------------------------------------------------------
 | Se obtiene el parámetro "url" desde la petición GET.
 | Si no existe, se asigna "list_product" por defecto.
 */
$uri = $_GET['url'] ?? 'list_product';

/*
 |----------------------------------------------------------
 | Separación de la URL en segmentos
 |----------------------------------------------------------
 | Se limpia la URL y se divide usando "/".
 | Solo se toma el primer segmento para saber
 | qué sección del menú está activa.
 */
$segment = explode('/', trim($uri, '/'))[0];

/*
 |----------------------------------------------------------
 | Activación del buffer de salida
 |----------------------------------------------------------
 | Todo el HTML que se genere se guarda en memoria
 | para luego insertarlo dentro del layout principal.
 */
ob_start();

/*
 |----------------------------------------------------------
 | Conexión a la base de datos
 |----------------------------------------------------------
 | Se carga la clase Database y se crea la conexión PDO.
 */
require_once __DIR__ . '/../../models/Database.php';
$pdo = (new Database())->getConnection();

/*
 |----------------------------------------------------------
 | Obtención del nombre de usuario
 |----------------------------------------------------------
 | Se obtiene el username desde la sesión.
 | htmlspecialchars protege contra inyección de HTML.
 */
$username = htmlspecialchars($_SESSION['user']['username']);
?>

<!-- ======================================================
     HOJA DE ESTILOS ESPECÍFICA PARA LISTADO DE PRODUCTOS
====================================================== -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/page-list-product.css">

<!-- ======================================================
     CONTENEDOR PRINCIPAL
     container-fluid ocupa todo el ancho de la pantalla
====================================================== -->
<div class="container-fluid m-0 p-0 min-vh-100 bg-body-tertiary" data-bs-theme="auto">
    <div class="row g-0">

        <!-- =================================================
             MENÚ LATERAL (SE INCLUYE DESDE OTRO ARCHIVO)
        ================================================== -->
        <?php require_once __DIR__ . '/../partials/layouts/laterals_menus/lateral_menu_products.php'; ?>

        <!-- =================================================
             CONTENIDO PRINCIPAL
        ================================================== -->
        <main class="col-12 col-md-10">

            <!-- =================================================
                 BARRA SUPERIOR
            ================================================== -->
            <div class="bg-body shadow-sm border-bottom">
                <div class="container-fluid px-4 py-3">
                    <div class="d-flex justify-content-between align-items-center">

                        <!-- =====TÍTULO ===== -->
                        <div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-2 small">
                                    <li class="breadcrumb-item">
                                        <a href="<?= BASE_URL ?>dashboard" class="text-decoration-none">
                                            Dashboard
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item active text-muted border-0">
                                        Inventario de Productos
                                    </li>
                                </ol>
                            </nav>

                            <h4 class="mb-0 fw-bold">
                                <i class="bi bi-box-seam me-2 text-primary"></i>
                                Gestión de Inventario
                            </h4>

                            <small class="text-muted">
                                Bienvenido, <?= $username ?>
                            </small>
                        </div>

                        <!-- ===== BOTÓN MENÚ MÓVIL ===== -->
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
            </div>

            <!-- =================================================
                 MENÚ OFFCANVAS (VERSIÓN MÓVIL)
            ================================================== -->
            <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu">
                <div class="offcanvas-header border-bottom">
                    <h5 class="offcanvas-title fw-bold text-body">
                        <i class="bi bi-box-seam text-primary me-2"></i>
                        Inventario
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                </div>

                <div class="offcanvas-body p-0 d-flex flex-column h-100">
                    <div class="list-group list-group-flush mt-2">

                        <?php
                        /*
                         |--------------------------------------------------
                         | Renderizado dinámico del menú
                         |--------------------------------------------------
                         | $menuItems contiene las opciones del menú.
                         | Se recorren con foreach.
                         */
                        if (isset($menuItems) && is_array($menuItems)):
                            foreach ($menuItems as $route => $item):

                                // Verifica si el menú está activo
                                $isActiveParent = ($segment === $route);
                                $isSubActive = isset($item['submenu']) &&
                                               array_key_exists($segment, $item['submenu']);
                        ?>
                                <a href="<?= BASE_URL . $route ?>"
                                   class="list-group-item list-group-item-action border-0 py-3 px-4 d-flex align-items-center
                                   <?= ($isActiveParent || $isSubActive)
                                        ? 'bg-primary-subtle text-primary border-start border-4 border-primary fw-bold'
                                        : 'text-body' ?>">
                                    <i class="bi bi-<?= $item['icon'] ?> me-3 fs-5"></i>
                                    <?= $item['label'] ?>
                                </a>

                                <!-- SUBMENÚ -->
                                <?php if (isset($item['submenu'])): ?>
                                    <div class="bg-body-tertiary">
                                        <?php foreach ($item['submenu'] as $subRoute => $subItem):
                                            $isSubItemActive = ($segment === $subRoute);
                                        ?>
                                            <a href="<?= BASE_URL . $subRoute ?>"
                                               class="list-group-item list-group-item-action border-0 py-2 ps-5 d-flex align-items-center
                                               <?= $isSubItemActive ? 'text-primary fw-bold' : 'text-muted' ?>"
                                               style="font-size: 0.85rem;">
                                                <i class="bi bi-<?= $subItem['icon'] ?> me-3 fs-6"></i>
                                                <?= $subItem['label'] ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </div>

                    <!-- USUARIO LOGUEADO -->
                    <div class="mt-auto border-top p-4 text-center">
                        <span class="badge border px-3 py-2 rounded-pill shadow-sm text-body">
                            <i class="bi bi-person-circle text-primary me-2"></i>
                            <?= $username ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- =================================================
                 CONTROL DE PERMISOS
            ================================================== -->
            <?php if ($_SESSION['user']['level_user'] != 1): ?>

                <!-- MENSAJE DE ACCESO DENEGADO -->
                <div class="container-fluid px-4 py-5 text-center">
                    <div class="card border-0 shadow-sm rounded-4 py-5 bg-body">
                        <div class="card-body">
                            <i class="bi bi-shield-exclamation text-warning display-1 mb-4"></i>
                            <h3 class="fw-bold">Acceso Denegado</h3>
                            <p class="text-muted mb-4">
                                No tienes los permisos necesarios para acceder a esta sección.
                            </p>
                            <a href="<?= BASE_URL ?>dashboard" class="btn btn-primary px-4 rounded-pill">
                                <i class="bi bi-house me-2"></i>
                                Volver al Dashboard
                            </a>
                        </div>
                    </div>
                </div>

            <?php else: ?>

                <!-- =================================================
                     CONTENIDO PRINCIPAL DEL INVENTARIO
                ================================================== -->
                <div class="container-fluid px-4 py-4">

                                       <!-- ===== TARJETAS DE ESTADÍSTICAS ===== -->
                    <!-- Cada tarjeta muestra un resumen del inventario -->
                    <!-- Los valores se cargan dinámicamente con JavaScript -->
                    <div class="row g-4 mb-4 text-body">

                        <!-- TOTAL DE PRODUCTOS -->
                        <div class="col-sm-6 col-xl-3">
                            <div class="card border-0 shadow-sm rounded-4 bg-body h-100">
                                <div class="card-body p-4 text-center">
                                    <!-- Ícono decorativo -->
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                         style="width: 50px; height: 50px;">
                                        <i class="bi bi-boxes fs-4"></i>
                                    </div>

                                    <!-- Título -->
                                    <h6 class="text-muted small text-uppercase fw-bold mb-1">
                                        Total Productos
                                    </h6>

                                    <!-- Valor que se llena con JS -->
                                    <h4 class="fw-bold mb-0" id="totalProducts">-</h4>
                                </div>
                            </div>
                        </div>

                        <!-- PRODUCTOS EN STOCK -->
                        <div class="col-sm-6 col-xl-3">
                            <div class="card border-0 shadow-sm rounded-4 bg-body h-100">
                                <div class="card-body p-4 text-center">
                                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                         style="width: 50px; height: 50px;">
                                        <i class="bi bi-check-circle fs-4"></i>
                                    </div>
                                    <h6 class="text-muted small text-uppercase fw-bold mb-1">
                                        En Stock
                                    </h6>
                                    <h4 class="fw-bold mb-0" id="inStock">-</h4>
                                </div>
                            </div>
                        </div>

                        <!-- PRODUCTOS CON STOCK BAJO -->
                        <div class="col-sm-6 col-xl-3">
                            <div class="card border-0 shadow-sm rounded-4 bg-body h-100">
                                <div class="card-body p-4 text-center">
                                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                         style="width: 50px; height: 50px;">
                                        <i class="bi bi-exclamation-triangle fs-4"></i>
                                    </div>
                                    <h6 class="text-muted small text-uppercase fw-bold mb-1">
                                        Stock Bajo
                                    </h6>
                                    <h4 class="fw-bold mb-0" id="lowStock">-</h4>
                                </div>
                            </div>
                        </div>

                        <!-- VALOR TOTAL DEL INVENTARIO -->
                        <div class="col-sm-6 col-xl-3">
                            <div class="card border-0 shadow-sm rounded-4 bg-body h-100">
                                <div class="card-body p-4 text-center">
                                    <div class="bg-info bg-opacity-10 text-info rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                         style="width: 50px; height: 50px;">
                                        <i class="bi bi-currency-dollar fs-4"></i>
                                    </div>
                                    <h6 class="text-muted small text-uppercase fw-bold mb-1">
                                        Valor Total
                                    </h6>
                                    <h4 class="fw-bold mb-0" id="totalValue">-</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- =================================================
                         TARJETA PRINCIPAL: LISTADO DE PRODUCTOS
                    ================================================== -->
                    <div class="card shadow-sm border-0 rounded-4">

                        <!-- CABECERA -->
                        <div class="card-header bg-body p-4 border-bottom-0">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                                <div>
                                    <h3 class="mb-1 fw-bold">Listado de Productos</h3>
                                    <p class="mb-0 text-muted">
                                        Gestiona tu inventario completo desde aquí
                                    </p>
                                </div>

                                <!-- BOTÓN PARA ABRIR MODAL DE NUEVO PRODUCTO -->
                                <button id="addProductBtn"
                                        class="btn btn-primary btn-lg px-4 rounded-pill shadow-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#addProductModal">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    Nuevo Producto
                                </button>
                            </div>
                        </div>

                        <!-- CUERPO DE LA TARJETA -->
                        <div class="card-body p-4">

                            <!-- ===== BÚSQUEDA Y EXPORTACIÓN ===== -->
                            <div class="row g-3 mb-4">

                                <!-- BUSCADOR -->
                                <div class="col-md-6">
                                    <div class="position-relative">
                                        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                        <input type="text"
                                               id="table-search"
                                               class="form-control ps-5 rounded-pill border-2"
                                               placeholder="Buscar productos...">
                                    </div>
                                </div>

                                <!-- EXPORTACIÓN -->
                                <div class="col-md-6 text-md-end">
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-outline-secondary dropdown-toggle rounded-pill px-4"
                                                type="button"
                                                data-bs-toggle="dropdown">
                                            <i class="bi bi-download me-2"></i>
                                            Exportar
                                        </button>

                                        <!-- OPCIONES DE EXPORTACIÓN -->
                                        <ul class="dropdown-menu shadow-lg border-0">
                                            <li>
                                                <button id="exportCSVBtn" class="dropdown-item">
                                                    <i class="bi bi-filetype-csv me-2 text-success"></i>
                                                    CSV
                                                </button>
                                            </li>
                                            <li>
                                                <button id="exportExcelBtn" class="dropdown-item">
                                                    <i class="bi bi-file-earmark-excel me-2 text-success"></i>
                                                    Excel
                                                </button>
                                            </li>
                                            <li>
                                                <button id="exportPDFBtn" class="dropdown-item">
                                                    <i class="bi bi-file-earmark-pdf me-2 text-danger"></i>
                                                    PDF
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- ===== FILTROS AVANZADOS ===== -->
                            <div class="mb-4">
                                <button class="btn btn-link text-decoration-none p-0 fw-semibold text-body"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#advancedFilters">
                                    <i class="bi bi-funnel me-2"></i>
                                    Filtros Avanzados
                                    <i class="bi bi-chevron-down ms-1 small"></i>
                                </button>

                                <!-- CONTENEDOR DE FILTROS -->
                                <div class="collapse mt-3" id="advancedFilters">
                                    <div class="card bg-body-tertiary border-0 rounded-3">
                                        <div class="card-body">
                                            <div class="row g-3">

                                                <!-- FILTRO ESTADO -->
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-bold">Estado</label>
                                                    <select class="form-select" id="statusFilter">
                                                        <option value="">Todos</option>
                                                        <option value="1">Activos</option>
                                                        <option value="0">Inactivos</option>
                                                    </select>
                                                </div>

                                                <!-- FILTRO STOCK -->
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-bold">Stock</label>
                                                    <select class="form-select" id="stockFilter">
                                                        <option value="">Todos</option>
                                                        <option value="low">Bajo</option>
                                                        <option value="normal">Normal</option>
                                                    </select>
                                                </div>

                                                <!-- BOTONES DE FILTRO -->
                                                <div class="col-md-6 d-flex align-items-end gap-2">
                                                    <button class="btn btn-primary px-4" id="applyFilters">
                                                        Aplicar
                                                    </button>
                                                    <button class="btn btn-light border px-4" id="clearFilters">
                                                        Limpiar
                                                    </button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ===== TABLA DE PRODUCTOS ===== -->
                            <!-- La tabla se genera completamente con JavaScript -->
                            <div class="table-responsive">
                                <div id="products-table" class="border-0"></div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- =================================================
                     MODALES (VENTANAS EMERGENTES)
                ================================================== -->
                <?php
                include __DIR__ . '/../partials/modals/modal_add_product.php';
                include __DIR__ . '/../partials/modals/modal_edit_product.php';
                include __DIR__ . '/../partials/modals/products/imagePreviewModal.php';
                ?>

            <?php endif; ?>
        </main>
    </div>
</div>


<?php
/*
 |----------------------------------------------------------
 | Se obtiene todo el contenido del buffer
 | y se inserta dentro del layout principal
 | (navbar, scripts globales, etc.)
 */
$content = ob_get_clean();
include __DIR__ . '/../partials/layouts/navbar.php';
?>

<!-- =====================================================
     SCRIPT PRINCIPAL DE LA TABLA DE PRODUCTOS (AJAX)
===================================================== -->
<script src="<?php echo BASE_URL; ?>assets/js/ajax/products-table.js"></script>

<!-- =====================================================
     ARCHIVO JS PARA ESTADÍSTICAS DE INVENTARIO
===================================================== --> 
<script src="<?php echo BASE_URL; ?>assets/js/ajax/inventory.js"></script>
