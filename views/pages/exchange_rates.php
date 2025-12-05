<?php
//Archivo: views/pages/exchange_rates.php

// Verifica si el usuario está logueado, si no, redirige
require_once __DIR__ . '/../inc/auth_check.php';


require_once __DIR__ . '/../../controllers/ExchangeRatesController.php';

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

<div class="container-fluid m-0 p-0 min-vh-100" data-bs-theme="auto">
  <div class="row g-0">
    <?php require_once __DIR__ . '/../partials/layouts/laterals_menus/lateral_menu_dashboard.php'; ?>

    <main class="col-12 col-md-10">
      <div class="bg-body shadow-sm border-bottom">
        <div class="container-fluid px-4 py-3 d-flex justify-content-between align-items-center">
          <div>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>settings">Ajustes</a></li>
                <li class="breadcrumb-item active">Tipo de Cambio</li>
              </ol>
            </nav>
            <h4 class="mb-0 fw-bold">Tipo de Cambio</h4>
            <small class="text-muted">Registra la paridad diaria y consulta el histórico.</small>
          </div>
          <div class="d-md-none">
            <button class="btn btn-outline-primary" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
              <i class="bi bi-list"></i>
            </button>
          </div>
        </div>
      </div>

      <div class="container-fluid px-4 py-4">
        <!-- Flash messages server-side (si las hay) -->
        <?php if (!empty($_SESSION['flash_success'])): ?>
          <div class="alert alert-success py-2"><?= htmlspecialchars($_SESSION['flash_success']) ?></div>
          <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>
        <?php if (!empty($_SESSION['flash_error'])): ?>
          <div class="alert alert-danger py-2"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
          <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>

        <div class="row g-4">
          <!-- Monedas -->
          <div class="col-lg-4">
            <div class="card shadow-sm">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Monedas</h6>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddCurrency">
                  <i class="bi bi-plus-lg"></i> Nueva
                </button>
              </div>
              <div class="card-body p-2">
                <ul id="currencies-list" class="list-group list-group-flush">
                  <?php if (!empty($currencies)): ?>
                    <?php foreach ($currencies as $c): ?>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                          <strong><?= htmlspecialchars($c['currency_code']) ?></strong>
                          <div class="small text-muted"><?= htmlspecialchars($c['currency_name']) ?> — <?= htmlspecialchars($c['country']) ?></div>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                          <?php if (!empty($lastRates[$c['currency_id']])): ?>
                            <div class="text-end me-2 small">
                              <div class="fw-bold"><?= number_format($lastRates[$c['currency_id']]['rate'], 6) ?></div>
                              <div class="text-muted"><?= htmlspecialchars($lastRates[$c['currency_id']]['rate_date']) ?></div>
                            </div>
                          <?php else: ?>
                            <div class="text-end me-2 small text-muted">Sin paridad</div>
                          <?php endif; ?>
                          <button class="btn btn-sm btn-outline-danger btn-delete-currency" data-id="<?= $c['currency_id'] ?>"><i class="bi bi-trash"></i></button>
                        </div>
                      </li>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <li class="list-group-item text-muted">No hay monedas registradas.</li>
                  <?php endif; ?>
                </ul>
              </div>
            </div>
          </div>

          <!-- Tipos de cambio -->
          <div class="col-lg-8">
            <div class="card shadow-sm">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Histórico de Tipos de Cambio</h6>
                <div>
                  <button class="btn btn-sm btn-outline-secondary me-2" onclick="location.reload();"><i class="bi bi-arrow-clockwise"></i> Actualizar</button>
                  <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddRate"><i class="bi bi-plus-lg"></i> Nuevo</button>
                </div>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-striped mb-0">
                    <thead class="table-light small">
                      <tr>
                        <th>Fecha</th>
                        <th>Moneda</th>
                        <th>Tipo</th>
                        <th>Notas</th>
                        <th>Creado</th>
                        <th class="text-end">Acciones</th>
                      </tr>
                    </thead>
                    <tbody id="rates-tbody">
                      <?php if (!empty($rates)): ?>
                        <?php foreach ($rates as $r): ?>
                          <tr>
                            <td><?= htmlspecialchars($r['rate_date']) ?></td>
                            <td><?= htmlspecialchars($r['currency_code']) ?> — <?= htmlspecialchars($r['currency_name']) ?></td>
                            <td class="fw-bold">$<?= number_format($r['rate'], 6) ?></td>
                            <td><?= htmlspecialchars($r['notes']) ?></td>
                            <td class="small text-muted"><?= htmlspecialchars($r['created_at']) ?></td>
                            <td class="text-end">
                              <button class="btn btn-sm btn-outline-danger btn-delete-rate" data-id="<?= $r['rate_id'] ?>"><i class="bi bi-trash"></i></button>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">No hay registros de tipo de cambio.</td></tr>
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

include __DIR__ . '/../partials/modals/exchange_rates/add_currency_modal.php';
include __DIR__ . '/../partials/modals/exchange_rates/add_rate_modal.php';

$content = ob_get_clean();
include __DIR__ . '/../partials/layouts/navbar.php';

?>

<!-- carga el JS AJAX -->
<script src="<?= BASE_URL ?>assets/js/ajax/exchange_rates.js"></script>
