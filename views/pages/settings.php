<?php
// views/pages/settings.php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user'])) {
  header("Location: " . BASE_URL . "auth/login/");
  exit();
}

ob_start(); // mantenemos el mismo patrón de tus vistas

require_once __DIR__ . '/../../models/Database.php';
$pdo = (new Database())->getConnection();

$uri = $_GET['url'] ?? 'settings';
$segment = explode('/', trim($uri, '/'))[0];

$username = htmlspecialchars($_SESSION['user']['username']);
$activeMenu = 'settings';

// incluir menú lateral (misma integración que en dashboard)
require_once __DIR__ . '/../partials/layouts/lateral_menu_dashboard.php';
?>

<div class="container-fluid m-0 p-0 min-vh-100" data-bs-theme="auto">
  <div class="row g-0">
    <main class="col-12 col-md-10">
      <div class="bg-body shadow-sm border-bottom">
        <div class="container-fluid px-4 py-3 d-flex justify-content-between align-items-center">
          <div>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard">Dashboard</a></li>
                <li class="breadcrumb-item active">Ajustes</li>
              </ol>
            </nav>
            <h4 class="mb-0 fw-bold">Ajustes del Sistema</h4>
            <small class="text-muted">Aquí agrupamos las opciones de configuración del sistema.</small>
          </div>
          <div class="d-md-none">
            <button class="btn btn-outline-primary" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
              <i class="bi bi-list"></i>
            </button>
          </div>
        </div>
      </div>

      <div class="container-fluid px-4 py-4">
        <div class="row g-4">
          <!-- Accesos rápidos -->
          <div class="col-lg-4">
            <div class="card shadow-sm">
              <div class="card-body">
                <h6 class="fw-bold">Accesos rápidos</h6>
                <p class="text-muted small mb-3">Enlaces a páginas de configuración.</p>

                <div class="list-group">
                  <a href="<?= BASE_URL ?>exchange_rates" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div>
                      <i class="bi bi-currency-exchange me-2"></i>
                      Tipo de Cambio
                      <div class="small text-muted">Registrar y ver histórico de tasas</div>
                    </div>
                    <i class="bi bi-chevron-right"></i>
                  </a>

                  <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center disabled">
                    <div>
                      <i class="bi bi-gear me-2"></i>
                      Preferencias
                      <div class="small text-muted">Próximamente</div>
                    </div>
                    <i class="bi bi-lock"></i>
                  </a>

                  <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center disabled">
                    <div>
                      <i class="bi bi-shield-lock me-2"></i>
                      Seguridad
                      <div class="small text-muted">Próximamente</div>
                    </div>
                    <i class="bi bi-lock"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Descripción / ayuda -->
          <div class="col-lg-8">
            <div class="card shadow-sm">
              <div class="card-body">
                <h6 class="fw-bold">Descripción</h6>
                <p class="text-muted small">Esta sección centralizará las configuraciones del sistema. Por ahora la opción disponible es <strong>Tipo de Cambio</strong>. Puedes agregar más opciones de ajustes aquí (monedas, impuestos, preferencias, etc.).</p>

                <div class="row g-3 mt-3">
                  <div class="col-md-6">
                    <div class="p-3 border rounded-3">
                      <h6 class="mb-1">Monedas</h6>
                      <p class="small text-muted mb-0">Gestiona monedas (USD, MXN, etc.).</p>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="p-3 border rounded-3">
                      <h6 class="mb-1">Tipos de Cambio</h6>
                      <p class="small text-muted mb-0">Registra la paridad diaria. Useful para conversiones históricas y reportes.</p>
                    </div>
                  </div>
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
$content = ob_get_clean();
include __DIR__ . '/../partials/layouts/navbar.php';
?>
