<?php
// =======================================================
// Archivo: views/pages/product_detail.php
// =======================================================
// Esta vista se encarga de mostrar el detalle de un producto específico.
// Incluye validaciones de sesión, validación de ID,
// consultas a base de datos y preparación de datos
// para ser usados en el HTML de la vista.
// =======================================================


// -------------------------------------------------------
// Verificación de autenticación del usuario
// -------------------------------------------------------
// Se incluye el archivo que valida si el usuario
// tiene una sesión activa.
// Si el usuario NO está autenticado, este archivo
// normalmente redirige al login y detiene la ejecución.
require_once __DIR__ . '/../inc/auth_check.php';


// -------------------------------------------------------
// Obtención de la URL lógica enviada por el sistema
// -------------------------------------------------------
// Se obtiene el parámetro 'url' desde $_GET.
// Si no existe, se asigna 'product_detail' como valor por defecto.
$uri = $_GET['url'] ?? 'product_detail';


// -------------------------------------------------------
// Extracción del primer segmento de la URL
// -------------------------------------------------------
// 1. trim($uri, '/') elimina barras al inicio y final
// 2. explode('/', ...) separa la URL en segmentos
// 3. [0] obtiene el primer segmento
// NOTA: Esta variable actualmente no se usa más adelante,
// pero puede servir para validaciones o lógica futura.
$segment = explode('/', trim($uri, '/'))[0];


// -------------------------------------------------------
// Validación del parámetro ID del producto
// -------------------------------------------------------
// Se verifica:
// - Que el parámetro 'id' exista
// - Que sea numérico
// Si no cumple, se redirige a la página product_not_found
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  header("Location: " . BASE_URL . "product_not_found");
  exit();
}


// -------------------------------------------------------
// Conversión segura del ID del producto
// -------------------------------------------------------
// Se fuerza el ID a entero para mayor seguridad
// y evitar inyecciones o valores inválidos.
$product_id = (int) $_GET['id'];


// -------------------------------------------------------
// Activación del buffer de salida
// -------------------------------------------------------
// Permite usar header() sin errores aunque
// luego se genere salida HTML.
ob_start();

//require_once __DIR__ . '/../../models/ProductDetailModel.php';
require_once __DIR__ . '/../../models/ProductDetailModel.php';
$pdo = (new Database())->getConnection();

$model = new ProductDetailModel();
$data = $model->getProductDetail($product_id);

if (!$data) {
  header("Location: " . BASE_URL . "product_not_found");
  exit();
}

$product = $data['product'];
$whs = $data['warehouses'];
$warehouses_stock = $data['warehouses_stock'];

// -------------------------------------------------------
// Variables auxiliares para la vista
// -------------------------------------------------------

// Se obtiene el nombre del usuario logueado
// y se escapa para evitar XSS.
$username = htmlspecialchars($_SESSION['user']['username']);

// Variable usada para marcar el menú activo en la UI
$activeMenu = 'list_product';

?>



<div class="container-fluid m-0 p-0 min-vh-100" data-bs-theme="auto">
  <div class="row g-0">

    <!-- Barra lateral -->
    <?php require_once __DIR__ . '/../partials/layouts/laterals_menus/lateral_menu_products.php'; ?>

    <!-- Contenido principal -->
    <main class="col-12 col-md-10">

      <!-- Header -->
      <div class="bg-body shadow-sm border-bottom">
        <div class="container-fluid px-4 py-3 d-flex justify-content-between align-items-center">
          <div>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>list_product">Inventario</a></li>
                <li class="breadcrumb-item active">Detalle del Producto</li>
              </ol>
            </nav>
            <h4 class="mb-0 fw-bold product-name"><?= htmlspecialchars($product['product_name']) ?></h4>
          </div>
          <div class="d-md-none">
            <button class="btn btn-outline-primary" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
              <i class="bi bi-list"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Menú móvil -->
      <div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="mobileMenu">
        <div class="offcanvas-header bg-primary-subtle">
          <h5 class="offcanvas-title"><i class="bi bi-box-seam me-2"></i>Inventario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body bg-body">
          <ul class="nav flex-column">
            <?php foreach ($menuItems as $route => $item): ?>
              <li class="nav-item mb-2">
                <a class="nav-link text-body d-flex align-items-center px-3 py-2 rounded-3 <?= $segment === $route ? 'active bg-primary text-white' : '' ?>" href="<?= BASE_URL . $route ?>">
                  <i class="bi bi-<?= $item['icon'] ?> me-3"></i><?= $item['label'] ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>

      <!-- Contenido del producto -->
      <div class="container-fluid px-4 py-4">

        <!-- Tarjeta principal -->
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden mb-4">
          <div class="card-header bg-gradient p-4 d-flex justify-content-between align-items-center">
            <div>
              <h2 class="mb-1 fw-bold product-name"><?= htmlspecialchars($product['product_name']) ?></h2>
              <p class="mb-0 opacity-75 product-code"><i class="bi bi-upc-scan me-2"></i>Código: <?= htmlspecialchars($product['product_code']) ?></p>
            </div>
            <div class="text-end">
              <span id="productStatusBadge" class="badge <?= $product['status'] ? 'bg-success' : 'bg-warning' ?> fs-6 px-3 py-2 rounded-pill">
                <i class="bi bi-<?= $product['status'] ? 'check-circle' : 'exclamation-triangle' ?> me-1"></i>
                <?= $product['status'] ? 'Activo' : 'Inactivo' ?>
              </span>

            </div>
          </div>

          <div class="card-body p-0 row g-0">
            <!-- Imagen -->
            <div class="col-md-5 p-4 bg-body-secondary d-flex justify-content-center align-items-center">
              <?php if ($product['image_url']): ?>
                <img id="productImage" src="<?= BASE_URL . htmlspecialchars($product['image_url']) ?>" class="img-fluid rounded-3 border shadow-sm zoomable" style="max-height:300px;">
              <?php else: ?>
                <div class="text-center text-muted"><i class="bi bi-image fs-1"></i>
                  <p>Sin imagen</p>
                </div>
              <?php endif; ?>
            </div>

            <!-- Info -->
            <div class="col-md-7 p-4">
              <div class="mb-4">
                <h5 class="text-primary"><i class="bi bi-file-text me-2"></i>Descripción</h5>
                <p id="detail-description" class="text-muted"><?= nl2br(htmlspecialchars($product['product_description'])) ?></p>
              </div>

              <div class="row g-3 mb-4">
                <div class="col-sm-6 text-center">
                  <i class="bi bi-currency-dollar text-primary fs-2"></i>
                  <h6 class="text-primary">Precio</h6>
                  <h4 class="fw-bold product-price">$<?= number_format($product['price'], 2) ?></h4>
                </div>
                <div class="col-sm-6 text-center">
                  <i class="bi bi-boxes text-success fs-2"></i>
                  <h6 class="text-success">Stock</h6>
                  <h4 class="fw-bold product-stock"><?= intval($product['stock']) ?></h4>
                </div>
              </div>

              <div class="d-flex gap-2 flex-wrap">
                <a href="<?= BASE_URL ?>list_product" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Volver</a>

                <button class="btn btn-success" id="btn-stock-entry" data-bs-toggle="modal" data-bs-target="#modalStockEntry">
                  <i class="bi bi-box-arrow-in-down me-2"></i>Entrada
                </button>

                <button class="btn btn-danger" id="btn-stock-exit" data-bs-toggle="modal" data-bs-target="#modalStockExit">
                  <i class="bi bi-box-arrow-up me-2"></i>Salida
                </button>

                <button class="btn btn-info text-white" id="btn-stock-transfer" data-bs-toggle="modal" data-bs-target="#modalStockTransfer">
                  <i class="bi bi-arrow-left-right me-2"></i>Transferir
                </button>

                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProductModal">
                  <i class="bi bi-pencil me-2"></i>Editar
                </button>
              </div>

            </div>
          </div>
        </div>

        <!-- Accordion con detalles -->
        <div class="card shadow-sm border-0 rounded-4">
          <div class="card-header bg-body py-3">
            <h5 class="fw-bold"><i class="bi bi-info-circle text-primary me-2"></i>Información Detallada</h5>
          </div>
          <div class="card-body p-3">
            <!-- Detalles técnicos en accordion -->
            <div class="card shadow-sm border-0 rounded-4">
              <div class="card-header bg-body py-3">
                <h5 class="mb-0 fw-bold">
                  <i class="bi bi-info-circle text-primary me-2"></i> Información Detallada
                </h5>
              </div>
              <div class="card-body p-0">
                <div class="accordion accordion-flush" id="productDetails">

                  <!-- Información básica -->
                  <div class="accordion-item">
                    <h2 class="accordion-header">
                      <button class="accordion-button fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#basicInfo">
                        <i class="bi bi-info-square me-2 text-primary"></i>
                        Información Básica
                      </button>
                    </h2>
                    <div id="basicInfo" class="accordion-collapse collapse show" data-bs-parent="#productDetails">
                      <div class="accordion-body">
                        <div class="row g-4">
                          <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                              <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="bi bi-upc text-primary"></i>
                              </div>
                              <div>
                                <small class="text-muted d-block">Código de Barras</small>
                                <span id="detail-barcode" class="fw-semibold"><?= htmlspecialchars($product['barcode']) ?></span>
                              </div>
                            </div>

                            <div class="d-flex align-items-center mb-3">
                              <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="bi bi-geo-alt text-success"></i>
                              </div>
                              <div>
                                <small class="text-muted d-block">Ubicación</small>
                                <span id="detail-location" class="fw-semibold"><?= htmlspecialchars($product['location']) ?></span>
                              </div>
                            </div>
                          </div>

                          <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                              <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="bi bi-calendar text-info"></i>
                              </div>
                              <div>
                                <small class="text-muted d-block">Fecha de Registro</small>
                                <span class="fw-semibold"><?= date("d/m/Y H:i", strtotime($product['registration_date'])) ?></span>
                              </div>
                            </div>

                            <div class="d-flex align-items-center mb-3">
                              <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="bi bi-clock text-warning"></i>
                              </div>
                              <div>
                                <small class="text-muted d-block">Última Actualización</small>
                                <span class="fw-semibold"><?= date("d/m/Y H:i", strtotime($product['updated_at'])) ?></span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Precios y ventas -->
                  <div class="accordion-item">
                    <h2 class="accordion-header">
                      <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#priceInfo">
                        <i class="bi bi-currency-exchange me-2 text-success"></i>
                        Precios y Ventas
                      </button>
                    </h2>
                    <div id="priceInfo" class="accordion-collapse collapse" data-bs-parent="#productDetails">
                      <div class="accordion-body">
                        <div class="row g-4">
                          <div class="col-md-4">
                            <div class="text-center p-3 bg-primary bg-opacity-10 rounded-3">
                              <i class="bi bi-tag text-primary fs-3 mb-2"></i>
                              <h6 class="text-primary mb-1">Precio Base</h6>
                              <h4 class="fw-bold mb-0"><span id="detail-price-base">$<?= number_format($product['price'], 2) ?></span></h4>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="text-center p-3 bg-success bg-opacity-10 rounded-3">
                              <i class="bi bi-cash text-success fs-3 mb-2"></i>
                              <h6 class="text-success mb-1">Precio Venta</h6>
                              <h4 class="fw-bold mb-0"><span id="detail-sale-price"><?= $product['sale_price'] !== null ? '$' . number_format($product['sale_price'], 2) : 'N/A' ?></span></h4>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="text-center p-3 bg-info bg-opacity-10 rounded-3">
                              <i class="bi bi-percent text-info fs-3 mb-2"></i>
                              <h6 class="text-info mb-1">Margen</h6>
                              <h4 class="fw-bold mb-0"><span id="detail-margin"><?php
                                                                                if ($product['sale_price'] !== null && $product['price'] > 0) {
                                                                                  $margin = (($product['sale_price'] - $product['price']) / $product['price']) * 100;
                                                                                  echo number_format($margin, 1) . '%';
                                                                                } else {
                                                                                  echo 'N/A';
                                                                                }
                                                                                ?></span></h4>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Dimensiones y peso -->
                  <div class="accordion-item">
                    <h2 class="accordion-header">
                      <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#dimensions">
                        <i class="bi bi-rulers me-2 text-warning"></i>
                        Dimensiones y Peso
                      </button>
                    </h2>
                    <div id="dimensions" class="accordion-collapse collapse" data-bs-parent="#productDetails">
                      <div class="accordion-body">
                        <div class="row g-4">
                          <div class="col-md-3">
                            <div class="d-flex align-items-center">
                              <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="bi bi-arrow-up text-warning"></i>
                              </div>
                              <div>
                                <small class="text-muted d-block">Alto</small>
                                <span id="detail-height" class="fw-semibold"><?= $product['height'] ?? 'N/A' ?><?= isset($product['height']) ? ' cm' : '' ?></span>
                              </div>
                            </div>
                          </div>

                          <div class="col-md-3">
                            <div class="d-flex align-items-center">
                              <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="bi bi-arrow-right text-info"></i>
                              </div>
                              <div>
                                <small class="text-muted d-block">Largo</small>
                                <span id="detail-length" class="fw-semibold"><?= $product['length'] ?? 'N/A' ?><?= isset($product['length']) ? ' cm' : '' ?></span>
                              </div>
                            </div>
                          </div>

                          <div class="col-md-3">
                            <div class="d-flex align-items-center">
                              <div class="bg-secondary bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="bi bi-arrows text-secondary"></i>
                              </div>
                              <div>
                                <small class="text-muted d-block">Ancho</small>
                                <span id="detail-width" class="fw-semibold"><?= $product['width'] ?? 'N/A' ?><?= isset($product['width']) ? ' cm' : '' ?></span>
                              </div>
                            </div>
                          </div>

                          <div class="col-md-3">
                            <div class="d-flex align-items-center">
                              <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="bi bi-circle text-success"></i>
                              </div>
                              <div>
                                <small class="text-muted d-block">Diámetro</small>
                                <span id="detail-diameter" class="fw-semibold"><?= $product['diameter'] ?? 'N/A' ?><?= isset($product['diameter']) ? ' cm' : '' ?></span>
                              </div>
                            </div>
                          </div>

                          <div class="col-md-12 mt-4">
                            <div class="d-flex align-items-center justify-content-center">
                              <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-speedometer text-primary fs-5"></i>
                              </div>
                              <div>
                                <small class="text-muted d-block">Peso</small>
                                <h5 id="detail-weight" class="fw-bold mb-0"><?= $product['weight'] !== null ? $product['weight'] . ' kg' : 'N/A' ?></h5>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- NUEVA SECCIÓN: Stock por Almacén -->
                  <div class="accordion-item">
                    <h2 class="accordion-header">
                      <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#stockByWarehouse">
                        <i class="bi bi-boxes me-2 text-primary"></i>
                        Stock por Almacén
                      </button>
                    </h2>
                    <div id="stockByWarehouse" class="accordion-collapse collapse" data-bs-parent="#productDetails">
                      <div class="accordion-body">
                        <div class="table-responsive">
                          <table class="table table-sm table-bordered align-middle text-center mb-0">
                            <thead class="table-dark">
                              <tr>
                                <th class="text-start">Almacén</th>
                                <th>Stock</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php foreach ($warehouses_stock as $ws): ?>
                                <tr data-warehouse-id="<?= intval($ws['warehouse_id']) ?>">
                                  <td class="fw-semibold"><?= htmlspecialchars($ws['warehouse_name']) ?></td>
                                  <!-- agregar data-stock-cell y clase para buscarlo desde JS -->
                                  <td data-stock-cell class="fw-bold warehouse-stock-value"><?= intval($ws['stock']) ?></td>
                                </tr>
                              <?php endforeach; ?>
                            </tbody>

                          </table>
                        </div>
                      </div>
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
//Modal para editar
include __DIR__ . '/../partials/modals/modal_edit_product.php';

// incluir modales de stock
include __DIR__ . '/../partials/modals/modal_stock_entry.php';
include __DIR__ . '/../partials/modals/modal_stock_exit.php';
include __DIR__ . '/../partials/modals/modal_stock_transfer.php';
?>

<!-- JS: lógica de modales de stock -->
<script src="<?= BASE_URL ?>assets/js/modals_stock.js"></script>


                            
<script>
  window.EDIT_PRODUCT_DATA = <?= json_encode($product, JSON_UNESCAPED_UNICODE) ?>;
  window.BASE_URL = "<?= BASE_URL ?>";
</script>

<script src="<?= BASE_URL ?>assets/js/products/edit-product-modal.js"></script>

<script src="<?= BASE_URL ?>assets/js/product_image_zoom.js"></script>

<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/product_detail.css">

<!-- Modal imagen grande -->
<?php
include __DIR__ . '/../partials/modals/modal_image_preview.php';


$content = ob_get_clean();
include __DIR__ . '/../partials/layouts/navbar.php';
?>