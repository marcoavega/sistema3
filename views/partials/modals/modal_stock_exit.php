<?php
// modal_stock_exit.php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<div class="modal fade" id="modalStockExit" tabindex="-1" aria-hidden="true" aria-labelledby="modalStockExitLabel">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            
            <div class="modal-header bg-danger text-white position-relative overflow-hidden border-0 py-4">
                <div class="modal-header-icon-bg">
                    <i class="fas fa-box-open"></i>
                </div>
                
                <div class="position-relative z-1 ms-2">
                    <h4 class="modal-title fw-bold d-flex align-items-center" id="modalStockExitLabel">
                        <i class="fas fa-minus-circle me-2"></i> Salida de Stock
                    </h4>
                    <p class="mb-0 opacity-75 small">Retire unidades del inventario de un almacén</p>
                </div>
                <button type="button" class="btn-close btn-close-white z-1" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body p-4 bg-body-tertiary">
                <form id="formStockExit" onsubmit="return false;">
                    <input type="hidden" name="product_id" value="<?= intval($product['product_id']) ?>">

                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            
                            <div class="mb-4">
                                <label class="form-label small text-muted fw-bold text-uppercase">Almacén de Origen</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-warehouse text-danger"></i></span>
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
                                <label class="form-label small text-muted fw-bold text-uppercase">Cantidad a Descontar</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-sort-numeric-down-alt text-danger"></i></span>
                                    <input type="number" name="quantity" min="1" step="1" 
                                           class="form-control form-control-lg fw-bold" 
                                           placeholder="0" required>
                                </div>
                                <div class="form-text mt-2 text-danger opacity-75">
                                    <i class="fas fa-exclamation-triangle me-1"></i> Verifique que exista stock suficiente.
                                </div>
                            </div>

                            <div class="mb-0">
                                <label class="form-label small text-muted fw-bold text-uppercase">Motivo de Salida</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-comment-alt text-danger"></i></span>
                                    <textarea name="notes" class="form-control" rows="2" 
                                              placeholder="Ej: Venta, merma, uso interno, producto dañado..."></textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer border-top-0 bg-body py-3">
                <button class="btn btn-secondary px-4" data-bs-dismiss="modal" type="button">Cancelar</button>
                <button class="btn btn-danger px-4 shadow-sm js-submit-exit" type="button" data-action="exit">
                    <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                    <i class="fas fa-minus-circle me-2"></i> Registrar Salida
                </button>
            </div>
        </div>
    </div>
</div>