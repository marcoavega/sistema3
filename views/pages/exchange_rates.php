<?php
// Archivo: views/pages/exchange_rates.php
require_once __DIR__ . '/../inc/auth_check.php';
require_once __DIR__ . '/../../controllers/ExchangeRatesController.php';

// Permisos
$userLevel = $_SESSION['user']['level_user'] ?? 3;
$canAdd    = ($userLevel == 1 || $userLevel == 2);
$canDel    = ($userLevel == 1);

$controller = new ExchangeRatesController();
$data = $controller->getViewData();

$currencies = $data['currencies'];
$lastRates  = $data['lastRates'];
$rates      = $data['rates'];

$activeMenu = 'settings';

$username = $_SESSION['user']['username'] ?? '';
ob_start();
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
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>settings" class="text-decoration-none">Ajustes</a></li>
                <li class="breadcrumb-item active text-body">Tipo de Cambio</li>
              </ol>
            </nav>
            <h4 class="mb-0 fw-bold text-body"><i class="fas fa-coins me-2 text-primary"></i>Tipo de Cambio</h4>
          </div>
          
          <div class="d-md-none">
            <button class="btn btn-outline-primary shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-controls="mobileMenu">
              <i class="bi bi-list fs-5"></i>
            </button>
          </div>
        </div>
      </div>

      <div class="container-fluid px-4 py-4">
        <?php if (!empty($_SESSION['flash_success'])): ?>
          <div class="alert alert-success border-0 shadow-sm"><?= htmlspecialchars($_SESSION['flash_success']) ?></div>
          <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>

        <div class="row g-4">
          <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 bg-body">
              <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-body">Monedas</h6>
                <?php if ($canAdd): ?>
                  <button class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalAddCurrency">
                    <i class="fas fa-plus me-1"></i> Nueva
                  </button>
                <?php endif; ?>
              </div>
              <div class="card-body p-2">
                <ul id="currencies-list" class="list-group list-group-flush">
                   </ul>
              </div>
            </div>
          </div>

          <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-body">
              <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="mb-0 fw-bold text-body">Histórico de Tasas</h6>
                <div class="d-flex gap-2">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle rounded-pill px-3" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-file-export me-1"></i> Exportar
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li><button id="exportCSVBtn" class="dropdown-item py-2 small"><i class="fas fa-file-csv text-success me-2"></i> CSV</button></li>
                            <li><button id="exportExcelBtn" class="dropdown-item py-2 small"><i class="fas fa-file-excel text-success me-2"></i> Excel</button></li>
                            <li><button id="exportPDFBtn" class="dropdown-item py-2 small"><i class="fas fa-file-pdf text-danger me-2"></i> PDF</button></li>
                        </ul>
                    </div>
                    <?php if ($canAdd): ?>
                      <button class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalAddRate">
                        <i class="fas fa-plus me-1"></i> Nuevo
                      </button>
                    <?php endif; ?>
                </div>
              </div>

              <div class="card-body border-bottom bg-body-tertiary">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="small text-muted">Desde:</label>
                        <input type="date" id="filter-date-start" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="small text-muted">Hasta:</label>
                        <input type="date" id="filter-date-end" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <button id="btn-clear-filter" class="btn btn-sm btn-outline-secondary w-100">
                            <i class="bi bi-x-circle me-1"></i> Limpiar
                        </button>
                    </div>
                </div>
              </div>

              <div class="card-body px-0">
                <div class="table-responsive">
                  <table class="table table-hover align-middle mb-0">
                    <thead class="bg-body-secondary text-muted small">
                      <tr>
                        <th class="ps-4">Fecha</th>
                        <th>Moneda</th>
                        <th>Tipo</th>
                        <th>Notas</th>
                        <th class="text-end pe-4">Acciones</th>
                      </tr>
                    </thead>
                    <tbody id="rates-tbody" class="text-body">
                      </tbody>
                  </table>
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
                
                <a href="<?= BASE_URL ?>dashboard" class="list-group-item list-group-item-action border-0 py-3 px-4 d-flex align-items-center">
                    <i class="bi bi-speedometer2 me-3 fs-5 text-secondary"></i> 
                    <span class="fw-medium">Panel de Control</span>
                </a>

                <a href="<?= BASE_URL ?>settings" class="list-group-item list-group-item-action border-0 py-3 px-4 d-flex align-items-center">
                    <i class="bi bi-gear me-3 fs-5 text-secondary"></i> 
                    <span class="fw-medium">Configuración</span>
                </a>

                <a href="<?= BASE_URL ?>exchange_rates" class="list-group-item list-group-item-action border-0 py-3 px-4 d-flex align-items-center bg-primary-subtle text-primary border-start border-4 border-primary">
                    <i class="fas fa-coins me-3 fs-5"></i> 
                    <span class="fw-bold">Tipo de Cambio</span>
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
include __DIR__ . '/../partials/modals/add_currency_modal.php';
include __DIR__ . '/../partials/modals/add_rate_modal.php';

$content = ob_get_clean();
include __DIR__ . '/../partials/layouts/navbar.php';
?>
<script src="<?= BASE_URL ?>assets/js/ajax/exchange_rates.js"></script>