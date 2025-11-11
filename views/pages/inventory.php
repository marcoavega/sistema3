<?php
// Archivo: views/pages/list_product.php

// Verificación de sesión
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
if (!isset($_SESSION['user'])) {
  header("Location: " . BASE_URL . "auth/login/");
  exit();
}

// Obtener segmento de URL para destacar menú activo
$uri = $_GET['url'] ?? 'list_product';
$segment = explode('/', trim($uri, '/'))[0];

// Iniciar buffer de salida
ob_start();

// Conexión a la base de datos
require_once __DIR__ . '/../../models/Database.php';
$pdo = (new Database())->getConnection();

// Nombre de usuario para mostrar
$username = htmlspecialchars($_SESSION['user']['username']);

// Incluir menú lateral de productos/inventario
require_once __DIR__ . '/../partials/layouts/lateral_menu_products.php';
?>

<!-- Solo se carga cuando este modal se incluye -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/page-inventory.css">

<div class="container-fluid m-0 p-0 min-vh-100" data-bs-theme="auto">
  <div class="row g-0">

    <!-- Barra lateral con gradiente moderno -->
    <nav class="col-md-2 d-none d-md-block sidebar min-vh-100">
      <div class="pt-4 px-3">
        <div class="text-center mb-4">
          <div class=" rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
            <i class="bi bi-box-seam text-primary fs-3"></i>
          </div>
          <h6 class=" mt-2 mb-0">Inventario</h6>
        </div>

        <ul class="nav flex-column">
          <?php foreach ($menuItems as $route => $item): ?>
            <li class="nav-item mb-2">
              <a class="nav-link d-flex align-items-center px-3 py-2 rounded-3 <?= $segment === $route ? 'bg-primary text-white fw-bold' : 'text-body' ?>"
                href="<?= BASE_URL . $route ?>" style="transition: all 0.3s ease;">
                <i class="bi bi-<?= $item['icon'] ?> me-3 fs-5"></i>
                <span class="fw-medium"><?= $item['label'] ?></span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </nav>

    <!-- Contenido principal -->
    <main class="col-12 col-md-10">

      <!-- Header con breadcrumb moderno -->
      <div class="bg-body shadow-sm border-bottom">
        <div class="container-fluid px-4 py-3">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                  <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-decoration-none">Dashboard</a></li>
                  <li class="breadcrumb-item active">Inventario de Productos</li>
                </ol>
              </nav>
              <h4 class="mb-0 fw-bold">Gestión de Inventario</h4>
              <small class="text-muted">Bienvenido, <?= $username ?></small>
            </div>

            <!-- Menú móvil mejorado -->
            <div class="d-md-none">
              <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                <i class="bi bi-list"></i>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Menú móvil offcanvas -->
      <div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="mobileMenu">
        <div class="offcanvas-header bg-primary-subtle">
          <h5 class="offcanvas-title">
            <i class="bi bi-box-seam me-2"></i>Inventario
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body bg-body">
          <ul class="nav flex-column">
            <?php foreach ($menuItems as $route => $item): ?>
              <li class="nav-item mb-2">
                <a class="nav-link text-body d-flex align-items-center px-3 py-2 rounded-3 <?= $segment === $route ? 'active bg-primary text-white' : '' ?>"
                  href="<?= BASE_URL . $route ?>">
                  <i class="bi bi-<?= $item['icon'] ?> me-3"></i> <?= $item['label'] ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>

      <!-- Verificación de permisos -->
      <?php if ($_SESSION['user']['level_user'] != 1): ?>
        <div class="container-fluid px-4 py-5">
          <div class="row justify-content-center">
            <div class="col-md-6">
              <div class="card border-0 shadow-lg">
                <div class="card-body text-center py-5">
                  <div class="mb-4">
                    <i class="bi bi-shield-exclamation text-warning" style="font-size: 4rem;"></i>
                  </div>
                  <h3 class="fw-bold text-warning mb-3">Acceso Denegado</h3>
                  <p class="text-muted mb-4">No tienes los permisos necesarios para acceder a esta sección del sistema.</p>
                  <a href="<?= BASE_URL ?>dashboard" class="btn btn-primary px-4">
                    <i class="bi bi-house me-2"></i>Volver al Dashboard
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php else: ?>

        <div class="container-fluid px-4 py-4">

          <!-- Panel de estadísticas rápidas -->
          <!-- Panel de estadísticas rápidas -->
          <div class="row g-3 mb-4">
            <div class="col-md-3">
              <div class="card bg-primary bg-opacity-10 border-0 h-100">
                <div class="card-body text-center">
                  <i class="bi bi-boxes text-primary fs-2 mb-3"></i>
                  <h6 class="text-primary mb-1">Total Productos</h6>
                  <h4 class="fw-bold mb-0" id="totalProducts">-</h4>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card bg-success bg-opacity-10 border-0 h-100">
                <div class="card-body text-center">
                  <i class="bi bi-check-circle text-success fs-2 mb-3"></i>
                  <h6 class="text-success mb-1">En Stock</h6>
                  <h4 class="fw-bold mb-0" id="inStock">-</h4>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card bg-warning bg-opacity-10 border-0 h-100">
                <div class="card-body text-center">
                  <i class="bi bi-exclamation-triangle text-warning fs-2 mb-3"></i>
                  <h6 class="text-warning mb-1">Stock Bajo</h6>
                  <h4 class="fw-bold mb-0" id="lowStock">-</h4>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card bg-info bg-opacity-10 border-0 h-100">
                <div class="card-body text-center">
                  <i class="bi bi-currency-dollar text-info fs-2 mb-3"></i>
                  <h6 class="text-info mb-1">Valor Total</h6>
                  <h4 class="fw-bold mb-0" id="totalValue">-</h4>
                </div>
              </div>
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

<!-- Script JS -->
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
        } else {
          console.error("Error al obtener estadísticas");
        }
      })
      .catch(err => {
        console.error("Error de red o servidor:", err);
      });
  });
</script>