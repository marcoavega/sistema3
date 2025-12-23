<?php
// Archivo: views/pages/list_product.php
require_once __DIR__ . '/../inc/auth_check.php';

$uri = $_GET['url'] ?? 'list_product';
$segment = explode('/', trim($uri, '/'))[0];

ob_start();

require_once __DIR__ . '/../../models/Database.php';
$pdo = (new Database())->getConnection();

$username = htmlspecialchars($_SESSION['user']['username']);
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/page-list-product.css">

<div class="container-fluid m-0 p-0 min-vh-100 bg-body-tertiary" data-bs-theme="auto">
    <div class="row g-0">

        <?php require_once __DIR__ . '/../partials/layouts/laterals_menus/lateral_menu_products.php'; ?>

        <main class="col-12 col-md-10">

            <div class="bg-body shadow-sm border-bottom">
                <div class="container-fluid px-4 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-2 small">
                                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-decoration-none">Dashboard</a></li>
                                    <li class="breadcrumb-item active text-muted border-0">Inventario de Productos</li>
                                </ol>
                            </nav>
                            <h4 class="mb-0 fw-bold">
                                <i class="bi bi-box-seam me-2 text-primary"></i>Gestión de Inventario
                            </h4>
                            <small class="text-muted">Bienvenido, <?= $username ?></small>
                        </div>

                        <div class="d-md-none">
                            <button class="btn btn-outline-primary shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                                <i class="bi bi-list fs-5"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
                <div class="offcanvas-header border-bottom">
                    <h5 class="offcanvas-title fw-bold text-body" id="mobileMenuLabel">
                        <i class="bi bi-box-seam text-primary me-2"></i>Inventario
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                
                <div class="offcanvas-body p-0 d-flex flex-column h-100">
                    <div class="list-group list-group-flush mt-2">
                        <?php 
                        if (isset($menuItems) && is_array($menuItems)): 
                            foreach ($menuItems as $route => $item): 
                                $isActiveParent = ($segment === $route);
                                $isSubActive = isset($item['submenu']) && array_key_exists($segment, $item['submenu']);
                        ?>
                                <a href="<?= BASE_URL . $route ?>" 
                                   class="list-group-item list-group-item-action border-0 py-3 px-4 d-flex align-items-center <?= ($isActiveParent || $isSubActive) ? 'bg-primary-subtle text-primary border-start border-4 border-primary fw-bold' : 'text-body' ?>">
                                    <i class="bi bi-<?= $item['icon'] ?> me-3 fs-5"></i> 
                                    <?= $item['label'] ?>
                                </a>

                                <?php if (isset($item['submenu'])): ?>
                                    <div class="bg-body-tertiary">
                                        <?php foreach ($item['submenu'] as $subRoute => $subItem): 
                                            $isSubItemActive = ($segment === $subRoute);
                                        ?>
                                            <a href="<?= BASE_URL . $subRoute ?>" 
                                               class="list-group-item list-group-item-action border-0 py-2 ps-5 d-flex align-items-center <?= $isSubItemActive ? 'text-primary fw-bold' : 'text-muted' ?>" style="font-size: 0.85rem;">
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
                    <div class="mt-auto border-top p-4 text-center">
                        <span class="badge border px-3 py-2 rounded-pill shadow-sm text-body">
                            <i class="bi bi-person-circle text-primary me-2"></i><?= $username ?>
                        </span>
                    </div>
                </div>
            </div>

            <?php if ($_SESSION['user']['level_user'] != 1): ?>
                <div class="container-fluid px-4 py-5 text-center">
                    <div class="card border-0 shadow-sm rounded-4 py-5 bg-body">
                        <div class="card-body">
                            <i class="bi bi-shield-exclamation text-warning display-1 mb-4"></i>
                            <h3 class="fw-bold">Acceso Denegado</h3>
                            <p class="text-muted mb-4">No tienes los permisos necesarios para acceder a esta sección.</p>
                            <a href="<?= BASE_URL ?>dashboard" class="btn btn-primary px-4 rounded-pill">
                                <i class="bi bi-house me-2"></i>Volver al Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>

                <div class="container-fluid px-4 py-4">

                    <div class="row g-4 mb-4 text-body">
                        <div class="col-sm-6 col-xl-3">
                            <div class="card border-0 shadow-sm rounded-4 bg-body h-100">
                                <div class="card-body p-4 text-center">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                                        <i class="bi bi-boxes fs-4"></i>
                                    </div>
                                    <h6 class="text-muted small text-uppercase fw-bold mb-1">Total Productos</h6>
                                    <h4 class="fw-bold mb-0" id="totalProducts">-</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="card border-0 shadow-sm rounded-4 bg-body h-100">
                                <div class="card-body p-4 text-center">
                                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                                        <i class="bi bi-check-circle fs-4"></i>
                                    </div>
                                    <h6 class="text-muted small text-uppercase fw-bold mb-1">En Stock</h6>
                                    <h4 class="fw-bold mb-0" id="inStock">-</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="card border-0 shadow-sm rounded-4 bg-body h-100">
                                <div class="card-body p-4 text-center">
                                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                                        <i class="bi bi-exclamation-triangle fs-4"></i>
                                    </div>
                                    <h6 class="text-muted small text-uppercase fw-bold mb-1">Stock Bajo</h6>
                                    <h4 class="fw-bold mb-0" id="lowStock">-</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="card border-0 shadow-sm rounded-4 bg-body h-100">
                                <div class="card-body p-4 text-center">
                                    <div class="bg-info bg-opacity-10 text-info rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                                        <i class="bi bi-currency-dollar fs-4"></i>
                                    </div>
                                    <h6 class="text-muted small text-uppercase fw-bold mb-1">Valor Total</h6>
                                    <h4 class="fw-bold mb-0" id="totalValue">-</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-header bg-body p-4 border-bottom-0">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                                <div>
                                    <h3 class="mb-1 fw-bold">Listado de Productos</h3>
                                    <p class="mb-0 text-muted">Gestiona tu inventario completo desde aquí</p>
                                </div>
                                <button id="addProductBtn" class="btn btn-primary btn-lg px-4 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#addProductModal">
                                    <i class="bi bi-plus-circle me-2"></i>Nuevo Producto
                                </button>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="position-relative">
                                        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                        <input type="text" id="table-search" class="form-control ps-5 rounded-pill border-2" placeholder="Buscar productos...">
                                    </div>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-outline-secondary dropdown-toggle rounded-pill px-4" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-download me-2"></i>Exportar
                                        </button>
                                        <ul class="dropdown-menu shadow-lg border-0">
                                            <li><button id="exportCSVBtn" class="dropdown-item"><i class="bi bi-filetype-csv me-2 text-success"></i>CSV</button></li>
                                            <li><button id="exportExcelBtn" class="dropdown-item"><i class="bi bi-file-earmark-excel me-2 text-success"></i>Excel</button></li>
                                            <li><button id="exportPDFBtn" class="dropdown-item"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>PDF</button></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <button class="btn btn-link text-decoration-none p-0 fw-semibold text-body" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilters">
                                    <i class="bi bi-funnel me-2"></i>Filtros Avanzados <i class="bi bi-chevron-down ms-1 small"></i>
                                </button>
                                <div class="collapse mt-3" id="advancedFilters">
                                    <div class="card bg-body-tertiary border-0 rounded-3">
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-bold">Estado</label>
                                                    <select class="form-select" id="statusFilter">
                                                        <option value="">Todos</option>
                                                        <option value="1">Activos</option>
                                                        <option value="0">Inactivos</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-bold">Stock</label>
                                                    <select class="form-select" id="stockFilter">
                                                        <option value="">Todos</option>
                                                        <option value="low">Bajo</option>
                                                        <option value="normal">Normal</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 d-flex align-items-end gap-2">
                                                    <button class="btn btn-primary px-4" id="applyFilters">Aplicar</button>
                                                    <button class="btn btn-light border px-4" id="clearFilters">Limpiar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <div id="products-table" class="border-0"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                include __DIR__ . '/../partials/modals/modal_add_product.php';
                include __DIR__ . '/../partials/modals/modal_edit_product.php';
                ?>
            <?php endif; ?>
        </main>
    </div>
</div>

<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 bg-transparent">
            <div class="modal-body p-0 text-center">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
                <img id="imagePreviewModalImg" src="" alt="Vista previa" class="img-fluid rounded shadow-lg">
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layouts/navbar.php';
?>

<script src="<?php echo BASE_URL; ?>assets/js/ajax/products-table.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Carga de estadísticas iniciales
    fetch("<?php echo BASE_URL; ?>api/products.php?action=stats")
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          document.getElementById('totalProducts').textContent = data.total || 0;
          document.getElementById('inStock').textContent = data.inStock || 0;
          document.getElementById('lowStock').textContent = data.lowStock || 0;
          document.getElementById('totalValue').textContent = `$${data.totalValue || '0.00'}`;
        }
      })
      .catch(err => console.error("Error al cargar estadísticas:", err));
  });
</script>