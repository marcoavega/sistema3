<?php
// views/partials/modals/modal_add_product.php

require_once __DIR__ . '/../../../models/Database.php';
$pdo = (new Database())->getConnection();

// Cargar listas desplegables:
$categories = $pdo->query("SELECT category_id, category_name FROM categories ORDER BY category_name")->fetchAll(PDO::FETCH_ASSOC);
$suppliers  = $pdo->query("SELECT supplier_id, supplier_name FROM suppliers ORDER BY supplier_name")->fetchAll(PDO::FETCH_ASSOC);
$units      = $pdo->query("SELECT unit_id, unit_name FROM units ORDER BY unit_name")->fetchAll(PDO::FETCH_ASSOC);
$currencies = $pdo->query("SELECT currency_id, currency_name FROM currencies ORDER BY currency_name")->fetchAll(PDO::FETCH_ASSOC);
$subcategories = $pdo->query("SELECT subcategory_id, subcategory_name FROM subcategories ORDER BY subcategory_name")->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Estilos CSS ligeros que respetan el tema de Bootstrap -->
<style>
    /* Zona de carga de archivos con borde discontinuo */
    .upload-zone {
        border: 2px dashed var(--bs-border-color);
        border-radius: 0.5rem;
        background-color: var(--bs-tertiary-bg);
        transition: all 0.3s ease;
        position: relative;
        cursor: pointer;
    }
    .upload-zone:hover {
        border-color: var(--bs-primary);
        background-color: var(--bs-secondary-bg);
    }
    
    /* Inputs con transición suave */
    .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.15);
    }

    /* Cabecera con icono de fondo */
    .modal-header-icon-bg {
        position: absolute;
        top: -10%;
        left: 5%;
        font-size: 8rem;
        opacity: 0.1;
        transform: rotate(-15deg);
        pointer-events: none;
        color: white;
    }

    .input-group-text {
        background-color: var(--bs-tertiary-bg);
        border-color: var(--bs-border-color);
    }
</style>

<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg">
      
      <!-- Header: Usa bg-primary para adaptarse al color de tu tema -->
      <div class="modal-header bg-primary text-white position-relative overflow-hidden border-0 py-4">
        <div class="modal-header-icon-bg">
            <i class="fas fa-box-open"></i>
        </div>
        
        <div class="position-relative z-1 ms-2">
          <h4 class="modal-title fw-bold d-flex align-items-center" id="addProductModalLabel">
            <i class="fas fa-plus-circle me-2"></i> Agregar Nuevo Producto
          </h4>
          <p class="mb-0 opacity-75 small">Complete la información para registrar en inventario</p>
        </div>
        <button type="button" class="btn-close btn-close-white z-1" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body p-4 bg-body-tertiary">
        <form id="addProductForm" enctype="multipart/form-data">

          <div class="row g-4">
            
            <!-- COLUMNA IZQUIERDA: DATOS PRINCIPALES -->
            <div class="col-lg-8">
                
                <!-- 1. Información Básica -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-body border-bottom-0 pt-4 pb-2">
                        <h6 class="fw-bold text-primary mb-0"><i class="fas fa-info-circle me-2"></i>Información Básica</h6>
                    </div>
                    <div class="card-body pt-2">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="new-product-code" class="form-label small text-muted fw-bold text-uppercase">Código</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-hashtag text-primary"></i></span>
                                    <!-- ID restaurado: new-product-code -->
                                    <input type="text" class="form-control" id="new-product-code" name="product_code" required placeholder="Ej. P-1001">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="new-barcode" class="form-label small text-muted fw-bold text-uppercase">Código Barras</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-barcode text-dark"></i></span>
                                    <!-- ID restaurado: new-barcode -->
                                    <input type="text" class="form-control" id="new-barcode" name="barcode" placeholder="Escanea aquí...">
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="new-product-name" class="form-label small text-muted fw-bold text-uppercase">Nombre del Producto</label>
                                <!-- ID restaurado: new-product-name -->
                                <input type="text" class="form-control form-control-lg fw-bold" id="new-product-name" name="product_name" required placeholder="Nombre comercial del producto">
                            </div>
                            <div class="col-12">
                                <label for="product_description" class="form-label small text-muted fw-bold text-uppercase">Descripción</label>
                                <!-- ID restaurado: product_description -->
                                <textarea name="product_description" id="product_description" rows="2" class="form-control" placeholder="Detalles adicionales..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Precios e Inventario -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-body border-bottom-0 pt-4 pb-2">
                        <h6 class="fw-bold text-primary mb-0"><i class="fas fa-coins me-2"></i>Precios e Inventario</h6>
                    </div>
                    <div class="card-body pt-2">
                        <div class="row g-3">
                             <div class="col-md-4">
                                <label for="new-price" class="form-label small text-muted fw-bold">Costo Compra</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white">$</span>
                                    <!-- ID restaurado: new-price -->
                                    <input type="number" step="0.01" class="form-control fw-bold" id="new-price" name="price" required placeholder="0.00">
                                </div>
                            </div>
                             <div class="col-md-4">
                                <label for="new-sale-price" class="form-label small text-muted fw-bold">Precio Venta</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-success text-white">$</span>
                                    <!-- ID restaurado: new-sale-price -->
                                    <input type="number" step="0.01" class="form-control fw-bold" id="new-sale-price" name="sale_price" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="new-currency" class="form-label small text-muted fw-bold">Moneda</label>
                                <!-- ID restaurado: new-currency -->
                                <select class="form-select" id="new-currency" name="currency_id" required>
                                    <?php foreach ($currencies as $cur): ?>
                                    <option value="<?= htmlspecialchars($cur['currency_id']) ?>"><?= htmlspecialchars($cur['currency_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-12"><hr class="text-muted opacity-25"></div>

                            <div class="col-md-4">
                                <label for="new-stock" class="form-label small text-muted fw-bold">Stock Actual</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-cubes"></i></span>
                                    <!-- ID restaurado: new-stock -->
                                    <input type="number" class="form-control" id="new-stock" name="stock" required placeholder="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="new-desired-stock" class="form-label small text-muted fw-bold">Stock Deseado</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-bullseye"></i></span>
                                    <!-- ID restaurado: new-desired-stock -->
                                    <input type="number" class="form-control" id="new-desired-stock" name="desired_stock" placeholder="0">
                                </div>
                            </div>
                             <div class="col-md-4">
                                <label for="new-unit" class="form-label small text-muted fw-bold">Unidad</label>
                                <!-- ID restaurado: new-unit -->
                                <select class="form-select" id="new-unit" name="unit_id" required>
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($units as $unit): ?>
                                    <option value="<?= htmlspecialchars($unit['unit_id']) ?>"><?= htmlspecialchars($unit['unit_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                 <!-- 3. Dimensiones (Compacto) -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-body border-bottom-0 pt-4 pb-2">
        <h6 class="fw-bold text-primary mb-0"><i class="fas fa-ruler-combined me-2"></i>Dimensiones (Opcional)</h6>
    </div>
    <div class="card-body pt-2">
        <div class="row g-2">

            <div class="col-6 col-md-3">
                <label for="new-weight" class="small text-muted">Peso (kg)</label>
                <input type="number" step="0.01" id="new-weight" name="weight" class="form-control form-control-sm" placeholder="0.00">
            </div>

            <div class="col-6 col-md-3">
                <label for="new-height" class="small text-muted">Alto (cm)</label>
                <input type="number" step="0.01" id="new-height" name="height" class="form-control form-control-sm" placeholder="0.00">
            </div>

            <div class="col-6 col-md-3">
                <label for="new-width" class="small text-muted">Ancho (cm)</label>
                <input type="number" step="0.01" id="new-width" name="width" class="form-control form-control-sm" placeholder="0.00">
            </div>

            <div class="col-6 col-md-3">
                <label for="new-length" class="small text-muted">Largo (cm)</label>
                <input type="number" step="0.01" id="new-length" name="length" class="form-control form-control-sm" placeholder="0.00">
            </div>

            <div class="col-6 col-md-3">
                <label for="new-diameter" class="small text-muted">Diámetro (cm)</label>
                <input type="number" step="0.01" id="new-diameter" name="diameter" class="form-control form-control-sm" placeholder="0.00">
            </div>

        </div>
    </div>
</div>


            </div>

            <!-- COLUMNA DERECHA: CLASIFICACIÓN Y EXTRAS -->
            <div class="col-lg-4">
                
                <!-- Clasificación -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold text-primary mb-3"><i class="fas fa-tags me-2"></i>Clasificación</h6>
                        
                        <div class="mb-3">
                            <label for="new-category" class="form-label small fw-bold">Categoría</label>
                            <!-- ID restaurado: new-category -->
                            <select id="new-category" name="category_id" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat['category_id']) ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="new-subcategory" class="form-label small fw-bold">Subcategoría</label>
                            <!-- ID restaurado: new-subcategory -->
                            <select class="form-select" id="new-subcategory" name="subcategory_id" required>
                                <option value="">Seleccionar...</option>
                                <?php foreach ($subcategories as $sub): ?>
                                <option value="<?= htmlspecialchars($sub['subcategory_id']) ?>"><?= htmlspecialchars($sub['subcategory_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="new-supplier" class="form-label small fw-bold">Proveedor</label>
                            <!-- ID restaurado: new-supplier -->
                            <select class="form-select" id="new-supplier" name="supplier_id" required>
                                <option value="">Seleccionar...</option>
                                <?php foreach ($suppliers as $sup): ?>
                                <option value="<?= htmlspecialchars($sup['supplier_id']) ?>"><?= htmlspecialchars($sup['supplier_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                         <div class="mb-0">
                            <label for="new-location" class="form-label small fw-bold">Ubicación</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                <!-- ID restaurado: new-location -->
                                <input type="text" class="form-control" id="new-location" name="location" placeholder="Ej. Estante A1">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Imagen y Estado -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold text-primary mb-3"><i class="fas fa-camera me-2"></i>Imagen</h6>
                        
                        <div class="upload-zone p-4 text-center mb-3 position-relative">
                            <!-- Input invisible que cubre toda la zona -->
                            <!-- ID restaurado: new-image -->
                            <input type="file" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" id="new-image" name="image_file" accept="image/*" style="cursor: pointer;">
                            
                            <i class="fas fa-cloud-upload-alt fa-3x text-secondary mb-2"></i>
                            <p class="mb-0 fw-bold text-body">Click para subir</p>
                            <small class="text-muted" style="font-size: 0.75rem;">JPG/PNG máx 5MB</small>
                        </div>

                        <label for="new-status" class="form-label small fw-bold">Estado del Producto</label>
                        <!-- ID restaurado: new-status -->
                        <select class="form-select" id="new-status" name="status">
                            <option value="1" selected>✅ Activo</option>
                            <option value="0">❌ Inactivo</option>
                        </select>
                    </div>
                </div>

            </div>
          </div> <!-- Fin Row Principal -->

        </form>
      </div>

      <!-- Footer -->
      <div class="modal-footer border-top-0 bg-body py-3">
        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
        <!-- ID restaurado: saveNewProductBtn -->
        <button type="button" class="btn btn-primary px-4 shadow-sm" id="saveNewProductBtn">
            <i class="fas fa-save me-2"></i> Guardar
            <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
        </button>
      </div>

    </div>
  </div>
</div>