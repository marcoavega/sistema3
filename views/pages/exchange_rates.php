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

$uri = $_GET['url'] ?? 'exchange_rates';
$segment = explode('/', trim($uri, '/'))[0];
$username = htmlspecialchars($_SESSION['user']['username']);
$activeMenu = 'settings';

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
            <button class="btn btn-outline-primary" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
              <i class="fas fa-bars"></i>
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
                    <i class="fas fa-plus me-1"></i> Nueva Moneda
                  </button>
                <?php endif; ?>
              </div>
              <div class="card-body p-2">
                <ul id="currencies-list" class="list-group list-group-flush">
                  <?php if (!empty($currencies)): ?>
                    <?php foreach ($currencies as $c): ?>
                      <li class="list-group-item bg-transparent border-0 d-flex justify-content-between align-items-center p-3 mb-2 rounded-3 bg-body-secondary bg-opacity-25">
                        <div>
                          <strong class="text-body"><?= htmlspecialchars($c['currency_code']) ?></strong>
                          <div class="small text-muted"><?= htmlspecialchars($c['currency_name']) ?></div>
                        </div>
                        <div class="d-flex align-items-center">
                          <?php if (!empty($lastRates[$c['currency_id']])): ?>
                            <div class="text-end me-2 small">
                              <div class="fw-bold text-primary">$<?= number_format($lastRates[$c['currency_id']]['rate'], 6) ?></div>
                            </div>
                          <?php endif; ?>
                          
                          <?php if ($canDel): ?>
                            <button class="btn btn-sm btn-outline-danger border-0 btn-delete-currency" data-id="<?= $c['currency_id'] ?>">
                              <i class="fas fa-trash"></i>
                            </button>
                          <?php endif; ?>
                        </div>
                      </li>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <li class="list-group-item bg-transparent text-muted small">No hay monedas registradas.</li>
                  <?php endif; ?>
                </ul>
              </div>
            </div>
          </div>

          <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-body">
              <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
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
                        <i class="fas fa-plus me-1"></i> Nuevo Registro
                      </button>
                    <?php endif; ?>
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
                      <?php if (!empty($rates)): ?>
                        <?php foreach ($rates as $r): ?>
                          <tr>
                            <td class="ps-4"><?= htmlspecialchars($r['rate_date']) ?></td>
                            <td><strong><?= htmlspecialchars($r['currency_code']) ?></strong></td>
                            <td><span class="badge bg-success-subtle text-success border border-success-subtle">$<?= number_format($r['rate'], 6) ?></span></td>
                            <td class="small text-muted"><?= htmlspecialchars($r['notes']) ?></td>
                            <td class="text-end pe-4">
                              <?php if ($canDel): ?>
                                <button class="btn btn-sm btn-light text-danger border btn-delete-rate" data-id="<?= $r['rate_id'] ?>">
                                  <i class="fas fa-trash-alt"></i>
                                </button>
                              <?php else: ?>
                                <i class="fas fa-lock text-muted small"></i>
                              <?php endif; ?>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <tr><td colspan="5" class="text-center text-muted py-5">No hay registros de tipo de cambio.</td></tr>
                      <?php endif; ?>
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
include __DIR__ . '/../partials/modals/add_currency_modal.php';
include __DIR__ . '/../partials/modals/add_rate_modal.php';

$content = ob_get_clean();
include __DIR__ . '/../partials/layouts/navbar.php';
?>
<script src="<?= BASE_URL ?>assets/js/ajax/exchange_rates.js"></script>