<?php
// modal_stock_entry.php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<div class="modal fade" id="modalStockEntry" tabindex="-1" aria-hidden="true" aria-labelledby="modalStockEntryLabel">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            
            <div class="modal-header bg-success text-white position-relative overflow-hidden border-0 py-4">
                <div class="modal-header-icon-bg">
                    <i class="fas fa-boxes"></i>
                </div>
                
                <div class="position-relative z-1 ms-2">
                    <h4 class="modal-title fw-bold d-flex align-items-center" id="modalStockEntryLabel">
                        <i class="fas fa-plus-circle me-2"></i> Entrada de Stock
                    </h4>
                    <p class="mb-0 opacity-75 small">Incremente el inventario en un almacén específico</p>
                </div>
                <button type="button" class="btn-close btn-close-white z-1" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body p-4 bg-body-tertiary">
                <form id="formStockEntry" onsubmit="return false;">
                    <input type="hidden" name="product_id" value="<?= intval($product['product_id']) ?>">

                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            
                            <div class="mb-4">
                                <label class="form-label small text-muted fw-bold text-uppercase">Almacén de Destino</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-warehouse text-success"></i></span>
                                    <select name="warehouse_id" class="form-select fw-bold" required>
                                        <option value="">Seleccione ubicación...</option>
                                        <?php if (!empty($whs)): foreach ($whs as $w): ?>
                                            <option value="<?= intval($w['warehouse_id']) ?>">
                                                <?= htmlspecialchars($w['name']) ?>
                                            </option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small text-muted fw-bold text-uppercase">Cantidad a Ingresar</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-sort-numeric-up-alt text-success"></i></span>
                                    <input type="number" name="quantity" min="1" step="1" 
                                           class="form-control form-control-lg fw-bold" 
                                           placeholder="0" required>
                                </div>
                                <div class="form-text mt-2">Solo números enteros positivos.</div>
                            </div>

                            <div class="mb-0">
                                <label class="form-label small text-muted fw-bold text-uppercase">Notas de Movimiento</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-sticky-note text-success"></i></span>
                                    <textarea name="notes" class="form-control" rows="2" 
                                              placeholder="Ej: Compra a proveedor, devolución, producción propia..."></textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer border-top-0 bg-body py-3">
                <button class="btn btn-secondary px-4" data-bs-dismiss="modal" type="button">Cancelar</button>
                <button class="btn btn-success px-4 shadow-sm js-submit-entry" type="button" data-action="entry">
                    <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                    <i class="fas fa-check-circle me-2"></i> Registrar Entrada
                </button>
            </div>
        </div>
    </div>
</div>