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

<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/page-inventory.css">

<div class="container-fluid m-0 p-0 min-vh-100 bg-body-tertiary" data-bs-theme="auto">
    <div class="row g-0">

        <?php require_once __DIR__ . '/../partials/layouts/laterals_menus/lateral_menu_products.php'; ?>

        <main class="col-12 col-md-10">

            <div class="bg-body shadow-sm border-bottom">
                <div class="container-fluid px-4 py-3">
                    <div class="d-flex justify-content-between align-items-center text-body">
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

                        <div class="d-md-none text-end">
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
                                    <h6 class="text-muted small text-uppercase fw-bold mb-1 text-truncate">Total Productos</h6>
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
                                    <h6 class="text-muted small text-uppercase fw-bold mb-1 text-truncate">En Stock</h6>
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
                                    <h6 class="text-muted small text-uppercase fw-bold mb-1 text-truncate">Stock Bajo</h6>
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
                                    <h6 class="text-muted small text-uppercase fw-bold mb-1 text-truncate">Valor Total</h6>
                                    <h4 class="fw-bold mb-0" id="totalValue">-</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layouts/navbar.php';
?>

<script src="<?php echo BASE_URL; ?>assets/js/ajax/products-table.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    fetch("<?php echo BASE_URL; ?>api/products.php?action=stats")
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          document.getElementById('totalProducts').textContent = data.total;
          document.getElementById('inStock').textContent = data.inStock;
          document.getElementById('lowStock').textContent = data.lowStock;
          document.getElementById('totalValue').textContent = `$${data.totalValue}`;
        }
      })
      .catch(err => console.error("Error stats:", err));
  });
</script>