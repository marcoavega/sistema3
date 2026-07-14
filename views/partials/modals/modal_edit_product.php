<?php
// Modal: Editar Producto
// Ruta: views/partials/modals/modal_edit_product.php

// Cargar listas desplegables.
$categories = $suppliers = $units = $currencies = $subcategories = [];
try {
    $categories = $pdo->query("SELECT category_id, category_name FROM categories ORDER BY category_name")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log($e->getMessage());
}
try {
    $suppliers = $pdo->query("SELECT supplier_id, supplier_name FROM suppliers ORDER BY supplier_name")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log($e->getMessage());
}
try {
    $units = $pdo->query("SELECT unit_id, unit_name FROM units ORDER BY unit_name")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log($e->getMessage());
}
try {
    $currencies = $pdo->query("SELECT currency_id, currency_name FROM currencies ORDER BY currency_name")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log($e->getMessage());
}
try {
    $subcategories = $pdo->query("SELECT subcategory_id, subcategory_name FROM subcategories ORDER BY subcategory_name")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log($e->getMessage());
}
?>

<!-- Estilos CSS consistentes con el modal de Agregar -->
<style>
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

    .form-control:focus,
    .form-select:focus {
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.15);
    }

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

<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">

            <!-- Header: Diseño consistente y adaptable al tema -->
            <div class="modal-header bg-primary text-white position-relative overflow-hidden border-0 py-4">
                <div class="modal-header-icon-bg">
                    <i class="fas fa-edit"></i>
                </div>

                <div class="position-relative z-1 ms-2">
                    <h4 class="modal-title fw-bold d-flex align-items-center" id="editProductModalLabel">
                        <i class="fas fa-pen-to-square me-2"></i> Editar Producto
                    </h4>
                    <p class="mb-0 opacity-75 small">Modifique la información del producto seleccionado</p>
                </div>
                <button type="button" class="btn-close btn-close-white z-1" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body p-4 bg-body-tertiary">
                <form id="editProductForm" enctype="multipart/form-data">
                    <!-- ID del producto (Oculto pero vital) -->
                    <input type="hidden" id="edit-product-id" name="product_id">

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
                                            <label for="edit-product-code" class="form-label small text-muted fw-bold text-uppercase">Código</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-barcode text-primary"></i></span>
                                                <input type="text" class="form-control" id="edit-product-code" name="product_code" required>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="edit-barcode" class="form-label small text-muted fw-bold text-uppercase">Código Barras</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-qrcode text-dark"></i></span>
                                                <input type="text" class="form-control" id="edit-barcode" name="barcode">
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label for="edit-product-name" class="form-label small text-muted fw-bold text-uppercase">Nombre del Producto</label>
                                            <input type="text" class="form-control form-control-lg fw-bold" id="edit-product-name" name="product_name" required>
                                        </div>

                                        <div class="col-12">
                                            <label for="edit-product-description" class="form-label small text-muted fw-bold text-uppercase">Descripción</label>
                                            <textarea name="product_description" id="edit-product-description" rows="2" class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. Precios e Inventario -->
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header bg-body border-bottom-0 pt-4 pb-2">
                                    <h6 class="fw-bold text-primary mb-0"><i class="fas fa-calculator me-2"></i>Costos e Inventario</h6>
                                </div>
                                <div class="card-body pt-2">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="edit-price" class="form-label small text-muted fw-bold">Costo</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-primary text-white">$</span>
                                                <input type="number" step="0.01" class="form-control fw-bold" id="edit-price" name="price" required>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="edit-sale-price" class="form-label small text-muted fw-bold">Precio Venta</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-success text-white">$</span>
                                                <input type="number" step="0.01" class="form-control fw-bold" id="edit-sale-price" name="sale_price">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="edit-currency" class="form-label small text-muted fw-bold">Moneda</label>
                                            <select class="form-select" id="edit-currency" name="currency_id" required>
                                                <?php foreach ($currencies as $cur): ?>
                                                    <option value="<?= htmlspecialchars($cur['currency_id']) ?>">
                                                        <?= htmlspecialchars($cur['currency_name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="col-12">
                                            <hr class="text-muted opacity-25">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="edit-stock" class="form-label small text-muted fw-bold">Stock Actual</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-cubes"></i></span>
                                                <input type="number" class="form-control" id="edit-stock" name="stock" required>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="edit-desired-stock" class="form-label small text-muted fw-bold">Stock Deseado</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-bullseye"></i></span>
                                                <input type="number" class="form-control" id="edit-desired-stock" name="desired_stock">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="edit-unit" class="form-label small text-muted fw-bold">Unidad</label>
                                            <select class="form-select" id="edit-unit" name="unit_id" required>
                                                <?php foreach ($units as $unit): ?>
                                                    <option value="<?= htmlspecialchars($unit['unit_id']) ?>">
                                                        <?= htmlspecialchars($unit['unit_name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Dimensiones (Diseño Compacto) -->
                            <div class="row g-2">
                                <div class="col-6 col-md-3">
                                    <label for="edit-weight" class="small text-muted">Peso (kg)</label>
                                    <input type="number" step="0.0001" id="edit-weight" name="weight" class="form-control form-control-sm">
                                </div>

                                <div class="col-6 col-md-3">
                                    <label for="edit-height" class="small text-muted">Alto (cm)</label>
                                    <input type="number" step="0.0001" id="edit-height" name="height" class="form-control form-control-sm">
                                </div>

                                <div class="col-6 col-md-3">
                                    <label for="edit-width" class="small text-muted">Ancho (cm)</label>
                                    <input type="number" step="0.0001" id="edit-width" name="width" class="form-control form-control-sm">
                                </div>

                                <div class="col-6 col-md-3">
                                    <label for="edit-length" class="small text-muted">Largo (cm)</label>
                                    <input type="number" step="0.0001" id="edit-length" name="length" class="form-control form-control-sm">
                                </div>

                                <div class="col-6 col-md-3">
                                    <label for="edit-diameter" class="small text-muted">Diámetro (cm)</label>
                                    <input type="number" step="0.0001" id="edit-diameter" name="diameter" class="form-control form-control-sm">
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
                                        <label for="edit-category" class="form-label small fw-bold">Categoría</label>
                                        <select id="edit-category" name="category_id" class="form-select" required>
                                            <option value="">Seleccionar...</option>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?= htmlspecialchars($cat['category_id']) ?>">
                                                    <?= htmlspecialchars($cat['category_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="edit-subcategory" class="form-label small fw-bold">Subcategoría</label>
                                        <select class="form-select" id="edit-subcategory" name="subcategory_id" required>
                                            <option value="">Seleccionar...</option>
                                            <?php foreach ($subcategories as $sub): ?>
                                                <option value="<?= htmlspecialchars($sub['subcategory_id']) ?>">
                                                    <?= htmlspecialchars($sub['subcategory_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="edit-supplier" class="form-label small fw-bold">Proveedor</label>
                                        <select class="form-select" id="edit-supplier" name="supplier_id" required>
                                            <option value="">Seleccionar...</option>
                                            <?php foreach ($suppliers as $sup): ?>
                                                <option value="<?= htmlspecialchars($sup['supplier_id']) ?>">
                                                    <?= htmlspecialchars($sup['supplier_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-0">
                                        <label for="edit-location" class="form-label small fw-bold">Ubicación</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                            <input type="text" class="form-control" id="edit-location" name="location">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Imagen y Estado -->
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-body">
                                    <h6 class="fw-bold text-primary mb-3"><i class="fas fa-images me-2"></i>Multimedia</h6>

                                    <div class="upload-zone p-4 text-center mb-3 position-relative">
                                        <input type="file" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" id="edit-image" name="image_file" accept="image/*" style="cursor: pointer;">

                                        <i class="fas fa-cloud-upload-alt fa-3x text-secondary mb-2"></i>
                                        <p class="mb-0 fw-bold text-body">Cambiar Imagen</p>
                                        <small class="text-muted" style="font-size: 0.75rem;">Arrastra o click aquí</small>
                                    </div>

                                    <label for="edit-status" class="form-label small fw-bold">Estado</label>
                                    <select class="form-select" id="edit-status" name="status">
                                        <option value="1">✅ Activo</option>
                                        <option value="0">❌ Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div> <!-- Fin Row -->

                </form>
            </div>

            <!-- Footer -->
            <div class="modal-footer border-top-0 bg-body py-3">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary px-4 shadow-sm" id="saveEditProductBtn">
                    <i class="fas fa-save me-2"></i> Guardar Cambios
                    <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                </button>
            </div>
        </div>
    </div>
</div>