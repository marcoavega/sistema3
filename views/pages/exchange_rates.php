<?php
// views/pages/exchange_rates.php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user'])) {
  header("Location: " . BASE_URL . "auth/login/");
  exit();
}

ob_start();

require_once __DIR__ . '/../../models/Database.php';
$pdo = (new Database())->getConnection();

$uri = $_GET['url'] ?? 'exchange_rates';
$segment = explode('/', trim($uri, '/'))[0];

$username = htmlspecialchars($_SESSION['user']['username']);
$activeMenu = 'settings';

// incluir lateral (igual que en otras páginas)
require_once __DIR__ . '/../partials/layouts/lateral_menu_dashboard.php';

// -----------------------
// POST handlers (crear / borrar) - PRG
// -----------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  try {
    if ($action === 'create_currency') {
      $code = strtoupper(trim($_POST['currency_code'] ?? ''));
      $name = trim($_POST['currency_name'] ?? '');
      $country = trim($_POST['country'] ?? '');
      if (!$code || !$name || !$country) throw new Exception('Faltan campos para moneda.');
      $stmt = $pdo->prepare("INSERT INTO currencies (currency_code, currency_name, country) VALUES (?, ?, ?)");
      $stmt->execute([$code, $name, $country]);
      header("Location: " . BASE_URL . "exchange_rates?m=currency_created");
      exit();
    }

    if ($action === 'create_rate') {
      $currency_id = (int)($_POST['currency_id'] ?? 0);
      $rate = $_POST['rate'] ?? null;
      $rate_date = $_POST['rate_date'] ?? null;
      $notes = $_POST['notes'] ?? null;
      if (!$currency_id || $rate === null || !$rate_date) throw new Exception('Faltan campos para tipo de cambio.');
      $stmt = $pdo->prepare("INSERT INTO exchange_rates (currency_id, rate, rate_date, notes) VALUES (?, ?, ?, ?)");
      $stmt->execute([$currency_id, $rate, $rate_date, $notes]);
      header("Location: " . BASE_URL . "exchange_rates?m=rate_created");
      exit();
    }

    if ($action === 'delete_currency') {
      $id = (int)($_POST['currency_id'] ?? 0);
      if (!$id) throw new Exception('ID moneda inválido.');
      $stmt = $pdo->prepare("DELETE FROM currencies WHERE currency_id = ?");
      $stmt->execute([$id]);
      header("Location: " . BASE_URL . "exchange_rates?m=currency_deleted");
      exit();
    }

    if ($action === 'delete_rate') {
      $id = (int)($_POST['rate_id'] ?? 0);
      if (!$id) throw new Exception('ID rate inválido.');
      $stmt = $pdo->prepare("DELETE FROM exchange_rates WHERE rate_id = ?");
      $stmt->execute([$id]);
      header("Location: " . BASE_URL . "exchange_rates?m=rate_deleted");
      exit();
    }
  } catch (Exception $e) {
    $err = rawurlencode($e->getMessage());
    header("Location: " . BASE_URL . "exchange_rates?error={$err}");
    exit();
  }
}

// -----------------------
// Datos
// -----------------------
$currencies = $pdo->query("SELECT currency_id, currency_code, currency_name, country FROM currencies ORDER BY currency_code")->fetchAll(PDO::FETCH_ASSOC);

$ratesStmt = $pdo->prepare("
  SELECT er.rate_id, er.currency_id, er.rate, er.rate_date, er.notes, er.created_at, c.currency_code, c.currency_name
  FROM exchange_rates er
  JOIN currencies c ON c.currency_id = er.currency_id
  ORDER BY er.rate_date DESC, er.created_at DESC
  LIMIT 500
");
$ratesStmt->execute();
$rates = $ratesStmt->fetchAll(PDO::FETCH_ASSOC);
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
        <?php if (!empty($_GET['m'])): ?>
          <div class="alert alert-success py-2"><?= htmlspecialchars($_GET['m']) ?></div>
        <?php endif; ?>
        <?php if (!empty($_GET['error'])): ?>
          <div class="alert alert-danger py-2"><?= htmlspecialchars(rawurldecode($_GET['error'])) ?></div>
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
                <ul class="list-group list-group-flush">
                  <?php foreach ($currencies as $c): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      <div>
                        <strong><?= htmlspecialchars($c['currency_code']) ?></strong>
                        <div class="small text-muted"><?= htmlspecialchars($c['currency_name']) ?> — <?= htmlspecialchars($c['country']) ?></div>
                      </div>
                      <div class="d-flex gap-2">
                        <form method="post" onsubmit="return confirm('Eliminar moneda y sus tipos de cambio?');">
                          <input type="hidden" name="action" value="delete_currency">
                          <input type="hidden" name="currency_id" value="<?= $c['currency_id'] ?>">
                          <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar moneda"><i class="bi bi-trash"></i></button>
                        </form>
                      </div>
                    </li>
                  <?php endforeach; ?>
                  <?php if (empty($currencies)): ?>
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
                    <tbody>
                      <?php foreach ($rates as $r): ?>
                        <tr>
                          <td><?= htmlspecialchars($r['rate_date']) ?></td>
                          <td><?= htmlspecialchars($r['currency_code']) ?> — <?= htmlspecialchars($r['currency_name']) ?></td>
                          <td class="fw-bold">$<?= number_format($r['rate'], 6) ?></td>
                          <td><?= htmlspecialchars($r['notes']) ?></td>
                          <td class="small text-muted"><?= htmlspecialchars($r['created_at']) ?></td>
                          <td class="text-end">
                            <form method="post" class="d-inline" onsubmit="return confirm('Eliminar este registro?');">
                              <input type="hidden" name="action" value="delete_rate">
                              <input type="hidden" name="rate_id" value="<?= $r['rate_id'] ?>">
                              <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                      <?php if (empty($rates)): ?>
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

<!-- Modal: agregar moneda -->
<div class="modal fade" id="modalAddCurrency" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <form method="post" id="formAddCurrency">
        <input type="hidden" name="action" value="create_currency">
        <div class="modal-header">
          <h5 class="modal-title">Nueva Moneda</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label class="form-label small">Código (ej. USD)</label>
            <input required class="form-control" name="currency_code" maxlength="10" placeholder="USD">
          </div>
          <div class="mb-2">
            <label class="form-label small">Nombre</label>
            <input required class="form-control" name="currency_name" maxlength="100" placeholder="Dólar estadounidense">
          </div>
          <div class="mb-2">
            <label class="form-label small">País</label>
            <input required class="form-control" name="country" maxlength="100" placeholder="Estados Unidos">
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
          <button class="btn btn-primary" type="submit">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal: agregar tipo de cambio -->
<div class="modal fade" id="modalAddRate" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <form method="post" id="formAddRate">
        <input type="hidden" name="action" value="create_rate">
        <div class="modal-header">
          <h5 class="modal-title">Nuevo Tipo de Cambio</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label class="form-label small">Moneda</label>
            <select name="currency_id" class="form-select" required>
              <option value="">Selecciona...</option>
              <?php foreach ($currencies as $c): ?>
                <option value="<?= $c['currency_id'] ?>"><?= htmlspecialchars($c['currency_code']) ?> — <?= htmlspecialchars($c['currency_name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-2">
            <label class="form-label small">Fecha</label>
            <input type="date" name="rate_date" class="form-control" required value="<?= date('Y-m-d') ?>">
          </div>
          <div class="mb-2">
            <label class="form-label small">Tipo de cambio</label>
            <input type="number" step="0.000001" name="rate" class="form-control" required placeholder="ej. 18.500000">
          </div>
          <div class="mb-2">
            <label class="form-label small">Notas (opcional)</label>
            <textarea name="notes" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
          <button class="btn btn-primary" type="submit">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layouts/navbar.php';
?>
