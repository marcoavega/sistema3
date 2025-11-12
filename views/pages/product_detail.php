<?php
if (!isset($_SESSION['user'])) {
  header("Location: " . BASE_URL . "auth/login/");
  exit();
}

$uri = $_GET['url'] ?? 'product_detail';
$segment = explode('/', trim($uri, '/'))[0];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  header("Location: " . BASE_URL . "product_not_found");
  exit();
}

$product_id = (int) $_GET['id'];

ob_start();
require_once __DIR__ . '/../../models/Database.php';

try {
  $pdo = (new Database())->getConnection();

  $stmt = $pdo->prepare("
    SELECT 
        product_id,
        product_code,
        barcode,
        product_name,
        product_description,
        location,
        price,
        stock,
        registration_date,
        category_id,
        supplier_id,
        unit_id,
        currency_id,
        image_url,
        subcategory_id,
        desired_stock,
        status,
        sale_price,
        weight,
        height,
        length,
        width,
        diameter,
        updated_at
    FROM products
    WHERE product_id = :product_id
    LIMIT 1
  ");
  $stmt->execute(['product_id' => $product_id]);
  $product = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$product) {
    header("Location: " . BASE_URL . "product_not_found");
    exit();
  }
} catch (PDOException $e) {
  echo "Error en la base de datos: " . $e->getMessage();
  exit();
}

$username = htmlspecialchars($_SESSION['user']['username']);
$activeMenu = 'list_product';
require_once __DIR__ . '/../partials/layouts/lateral_menu_products.php';
?>

<div class="container-fluid m-0 p-0 min-vh-100" data-bs-theme="auto">
  <div class="row g-0">

    <!-- Barra lateral -->
    <nav class="col-md-2 d-none d-md-block sidebar min-vh-100">
      <div class="pt-4 px-3">
        <div class="text-center mb-4">
          <div class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
            <i class="bi bi-box-seam text-primary fs-3"></i>
          </div>
          <h6 class="mt-2 mb-0">Inventario</h6>
        </div>

        <ul class="nav flex-column">
          <?php foreach ($menuItems as $route => $item): ?>
            <li class="nav-item mb-2">
              <a class="nav-link d-flex align-items-center px-3 py-2 rounded-3 <?= isset($activeMenu) && $activeMenu === $route ? 'bg-primary text-white fw-bold' : 'text-body' ?>"
                href="<?= BASE_URL . $route ?>">
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
                <a class="nav-link text-body d-flex align-items-center px-3 py-2 rounded-3 <?= $segment === $route ? 'active bg-primary text-white' : '' ?>"
                  href="<?= BASE_URL . $route ?>">
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
                <p class="text-muted"><?= nl2br(htmlspecialchars($product['product_description'])) ?></p>
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
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProductModal"><i class="bi bi-pencil me-2"></i>Editar</button>
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
                  <i class="bi bi-info-circle text-primary me-2"></i>
                  Información Detallada
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
                                <span class="fw-semibold"><?= htmlspecialchars($product['barcode']) ?></span>
                              </div>
                            </div>

                            <div class="d-flex align-items-center mb-3">
                              <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="bi bi-geo-alt text-success"></i>
                              </div>
                              <div>
                                <small class="text-muted d-block">Ubicación</small>
                                <span class="fw-semibold"><?= htmlspecialchars($product['location']) ?></span>
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
                              <h4 class="fw-bold mb-0">$<?= number_format($product['price'], 2) ?></h4>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="text-center p-3 bg-success bg-opacity-10 rounded-3">
                              <i class="bi bi-cash text-success fs-3 mb-2"></i>
                              <h6 class="text-success mb-1">Precio Venta</h6>
                              <h4 class="fw-bold mb-0">
                                <?= $product['sale_price'] !== null ? '$' . number_format($product['sale_price'], 2) : 'N/A' ?>
                              </h4>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="text-center p-3 bg-info bg-opacity-10 rounded-3">
                              <i class="bi bi-percent text-info fs-3 mb-2"></i>
                              <h6 class="text-info mb-1">Margen</h6>
                              <h4 class="fw-bold mb-0">
                                <?php
                                if ($product['sale_price'] !== null && $product['price'] > 0) {
                                  $margin = (($product['sale_price'] - $product['price']) / $product['price']) * 100;
                                  echo number_format($margin, 1) . '%';
                                } else {
                                  echo 'N/A';
                                }
                                ?>
                              </h4>
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
                                <span class="fw-semibold"><?= $product['height'] ?? 'N/A' ?> cm</span>
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
                                <span class="fw-semibold"><?= $product['length'] ?? 'N/A' ?> cm</span>
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
                                <span class="fw-semibold"><?= $product['width'] ?? 'N/A' ?> cm</span>
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
                                <span class="fw-semibold"><?= $product['diameter'] ?? 'N/A' ?> cm</span>
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
                                <h5 class="fw-bold mb-0"><?= $product['weight'] !== null ? $product['weight'] . ' kg' : 'N/A' ?></h5>
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

          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<?php include __DIR__ . '/../partials/modals/modal_edit_product.php'; ?>

<!-- SCRIPT -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const modalEl = document.getElementById("editProductModal");
    const form = document.getElementById("editProductForm");
    const saveBtn = document.getElementById("saveEditProductBtn");

    if (!modalEl || !form || !saveBtn) {
      console.warn("No se encontró el modal, formulario o botón Guardar.");
      return;
    }

    const fillForm = () => {
      const map = {
        "edit-product-id": "<?= $product['product_id'] ?>",
        "edit-product-code": "<?= htmlspecialchars($product['product_code']) ?>",
        "edit-barcode": "<?= htmlspecialchars($product['barcode']) ?>",
        "edit-product-name": "<?= htmlspecialchars($product['product_name']) ?>",
        "edit-product-description": `<?= htmlspecialchars($product['product_description']) ?>`,
        "edit-price": "<?= $product['price'] ?>",
        "edit-stock": "<?= $product['stock'] ?>",
        "edit-desired-stock": "<?= $product['desired_stock'] ?>",
        "edit-location": "<?= htmlspecialchars($product['location']) ?>",
        "edit-category": "<?= $product['category_id'] ?>",
        "edit-subcategory": "<?= $product['subcategory_id'] ?>",
        "edit-unit": "<?= $product['unit_id'] ?>",
        "edit-currency": "<?= $product['currency_id'] ?>",
        "edit-supplier": "<?= $product['supplier_id'] ?>",
        "edit-status": "<?= $product['status'] ?>"
      };
      for (const id in map) {
        const el = document.getElementById(id);
        if (el) el.value = map[id];
      }
    };
    modalEl.addEventListener("show.bs.modal", fillForm);

    function toast(msg, err = false) {
      const t = document.createElement("div");
      t.textContent = msg;
      t.style.position = "fixed";
      t.style.bottom = "25px";
      t.style.right = "25px";
      t.style.padding = "10px 15px";
      t.style.borderRadius = "8px";
      t.style.background = err ? "#dc3545" : "#198754";
      t.style.color = "white";
      t.style.zIndex = 9999;
      document.body.appendChild(t);
      setTimeout(() => t.remove(), 3000);
    }

    saveBtn.addEventListener("click", async (e) => {
      e.preventDefault();
      saveBtn.disabled = true;
      const spinner = saveBtn.querySelector(".spinner-border");
      if (spinner) spinner.classList.remove("d-none");

      try {
        const fd = new FormData(form);
        if (!fd.get("product_id")) {
          toast("ID de producto faltante", true);
          return;
        }

        const resp = await fetch(`${BASE_URL}api/products.php?action=update`, {
          method: "POST",
          body: fd
        });
        const txt = await resp.text();
        if (!resp.ok || !txt) {
          toast("Error al conectar con el servidor.", true);
          console.error(txt);
          return;
        }

        let data;
        try {
          data = JSON.parse(txt);
        } catch {
          toast("Respuesta no válida del servidor.", true);
          return;
        }

        if (!data.success) {
          toast(data.message || "No se pudo actualizar.", true);
          return;
        }

        const u = data.product || {};
        bootstrap.Modal.getInstance(modalEl)?.hide();

        document.querySelectorAll(".product-name").forEach(el => el.textContent = u.product_name || form["product_name"].value);
        // 🔹 Actualizar el código del producto en tiempo real
        const codigoEl = document.querySelector(".product-code");
        if (codigoEl && (u.product_code || form["product_code"].value)) {
          codigoEl.innerHTML = `<i class="bi bi-upc-scan me-2"></i>Código: ${u.product_code || form["product_code"].value}`;
        }

        document.querySelector(".product-price").textContent = `$${parseFloat(u.price ?? form["price"].value).toFixed(2)}`;
        document.querySelector(".product-stock").textContent = u.stock ?? form["stock"].value;

        // 🔹 Actualizar el estado (Activo/Inactivo) en tiempo real
        const statusBadge = document.getElementById('productStatusBadge');
        const statusFromServer = (typeof u.status !== 'undefined') ? u.status : null;
        let statusVal = null;
        if (statusFromServer !== null) {
          statusVal = parseInt(statusFromServer) === 1 ? 1 : 0;
        } else {
          const s = form.querySelector('#edit-status')?.value;
          statusVal = (s === '1' || s === 1) ? 1 : 0;
        }

        if (statusBadge) {
          statusBadge.classList.remove('bg-success', 'bg-warning', 'text-white');
          if (statusVal === 1) {
            statusBadge.classList.add('bg-success', 'text-white');
            statusBadge.innerHTML = `<i class="bi bi-check-circle me-1"></i> Activo`;
          } else {
            statusBadge.classList.add('bg-warning', 'text-white');
            statusBadge.innerHTML = `<i class="bi bi-exclamation-triangle me-1"></i> Inactivo`;
          }
        }


        if (u.image_url) {
          const img = document.getElementById("productImage");
          if (img) img.src = `${BASE_URL}${u.image_url}?v=${Date.now()}`;
        }

        toast("Producto actualizado correctamente.");
      } catch (err) {
        console.error(err);
        toast("Error de conexión con el servidor.", true);
      } finally {
        saveBtn.disabled = false;
        if (spinner) spinner.classList.add("d-none");
      }
    });
  });
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {
  // Selector de imágenes que abrirán el modal: puedes usar #productImage u otras con clase 'zoomable'
  const zoomableSelector = ".zoomable, #productImage";
  const previewModalEl = document.getElementById("imagePreviewModal");
  const previewImg = document.getElementById("imagePreviewModalImg");

  if (!previewModalEl || !previewImg) return;

  const modalInstance = new bootstrap.Modal(previewModalEl, {
    keyboard: true,
    backdrop: true
  });

  // Añadimos listener a todas las imágenes existentes que encajen con el selector
  function attachZoomListeners() {
    const imgs = document.querySelectorAll(zoomableSelector);
    imgs.forEach(img => {
      // evitar múltiples listeners
      if (img.dataset.zoomAttached) return;
      img.dataset.zoomAttached = "1";

      img.addEventListener("click", (e) => {
        const src = img.getAttribute("src") || img.dataset.src;
        if (!src) return;
        // usar cache-buster para forzar recarga si se actualizó la imagen
        const cacheBusted = src + (src.includes('?') ? '&' : '?') + 'v=' + Date.now();
        previewImg.src = cacheBusted;
        previewImg.alt = img.alt || 'Imagen del producto';
        modalInstance.show();
      });
    });
  }

  attachZoomListeners();

  // Si cargas/actualizas la imagen dinámicamente y quieres re-attach (por ejemplo tras editar),
  // llama a attachZoomListeners() de nuevo desde donde actualizas la imagen.
});
</script>


<style>
  .sidebar .nav-link:hover {
    transform: translateX(5px);
  }

  .card {
    transition: transform .2s ease, box-shadow .2s ease;
  }

  .card:hover {
    transform: translateY(-2px);
  }


  /* Cursor pointer para miniaturas zoomables */
.zoomable {
  cursor: pointer;
  transition: transform .15s ease;
}
.zoomable:hover {
  transform: scale(1.02);
}

</style>


  <!-- Modal imagen grande -->
  <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content border-0 bg-transparent">
      <div class="modal-body p-0 d-flex justify-content-center align-items-center">
        <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        <img id="imagePreviewModalImg" src="" alt="Vista previa" class="img-fluid rounded" style="max-width:100%; max-height:80vh; object-fit:contain;">
      </div>
    </div>
  </div>
</div>


<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layouts/navbar.php';
?>