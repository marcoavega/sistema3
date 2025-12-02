<?php
// modal_stock_exit.php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<div class="modal fade" id="modalStockExit" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <form id="formStockExit" onsubmit="return false;">
        <input type="hidden" name="product_id" value="<?= intval($product['product_id']) ?>">
        <div class="modal-header">
          <h5 class="modal-title">Salida de Stock</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label class="form-label small">Almacén</label>
            <select name="warehouse_id" class="form-select" required>
              <option value="">Selecciona...</option>
              <?php if (!empty($whs)): foreach ($whs as $w): ?>
                <option value="<?= intval($w['warehouse_id']) ?>"><?= htmlspecialchars($w['name']) ?></option>
              <?php endforeach; endif; ?>
            </select>
          </div>
          <div class="mb-2">
            <label class="form-label small">Cantidad a descontar</label>
            <input type="number" name="quantity" min="1" step="1" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="form-label small">Motivo</label>
            <textarea name="notes" class="form-control" rows="2" placeholder="Ej. devolución, venta, uso interno"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
          <button class="btn btn-danger js-submit-exit" type="button" data-action="exit">
            <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
            Registrar salida
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
