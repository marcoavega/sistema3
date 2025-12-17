<?php
// modal_stock_transfer.php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<div class="modal fade" id="modalStockTransfer" tabindex="-1" aria-hidden="true" aria-labelledby="modalStockTransferLabel">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            
            <div class="modal-header bg-info text-white position-relative overflow-hidden border-0 py-4">
                <div class="modal-header-icon-bg">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                
                <div class="position-relative z-1 ms-2">
                    <h4 class="modal-title fw-bold d-flex align-items-center" id="modalStockTransferLabel">
                        <i class="fas fa-route me-2"></i> Transferencia de Stock
                    </h4>
                    <p class="mb-0 opacity-75 small">Mueva mercancía entre diferentes ubicaciones</p>
                </div>
                <button type="button" class="btn-close btn-close-white z-1" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body p-4 bg-body-tertiary">
                <form id="formStockTransfer" onsubmit="return false;">
                    <input type="hidden" name="product_id" value="<?= intval($product['product_id']) ?>">

                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-body p-4">
                            
                            <div class="mb-3">
                                <label class="form-label small text-muted fw-bold text-uppercase">Almacén de Origen</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-sign-out-alt text-info"></i></span>
                                    <select name="from_warehouse_id" class="form-select fw-semibold" required>
                                        <option value="">Seleccione origen...</option>
                                        <?php if (!empty($whs)): foreach ($whs as $w): ?>
                                            <option value="<?= intval($w['warehouse_id']) ?>">
                                                <?= htmlspecialchars($w['name']) ?>
                                            </option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="text-center my-2">
                                <i class="fas fa-chevron-down text-muted opacity-50"></i>
                            </div>

                            <div class="mb-0">
                                <label class="form-label small text-muted fw-bold text-uppercase">Almacén de Destino</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-sign-in-alt text-info"></i></span>
                                    <select name="to_warehouse_id" class="form-select fw-semibold" required>
                                        <option value="">Seleccione destino...</option>
                                        <?php if (!empty($whs)): foreach ($whs as $w): ?>
                                            <option value="<?= intval($w['warehouse_id']) ?>">
                                                <?= htmlspecialchars($w['name']) ?>
                                            </option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label small text-muted fw-bold text-uppercase">Cantidad</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-layer-group text-info"></i></span>
                                        <input type="number" name="quantity" min="1" step="1" 
                                               class="form-control fw-bold" placeholder="0" required>
                                    </div>
                                </div>

                                <div class="col-md-7">
                                    <label class="form-label small text-muted fw-bold text-uppercase">Notas / Referencia</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-pen text-info"></i></span>
                                        <textarea name="notes" class="form-control" rows="1" 
                                                  placeholder="Opcional..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer border-top-0 bg-body py-3">
                <button class="btn btn-secondary px-4" data-bs-dismiss="modal" type="button">Cancelar</button>
                <button class="btn btn-info text-white px-4 shadow-sm js-submit-transfer" type="button" data-action="transfer">
                    <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                    <i class="fas fa-exchange-alt me-2"></i> Realizar Transferencia
                </button>
            </div>
        </div>
    </div>
</div>