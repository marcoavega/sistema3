<?php
// modal_stock_transfer.php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<div class="modal fade" id="modalStockTransfer" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <form id="formStockTransfer" onsubmit="return false;">
        <input type="hidden" name="product_id" value="<?= intval($product['product_id']) ?>">
        <div class="modal-header">
          <h5 class="modal-title">Transferir entre almacenes</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label class="form-label small">Almacén origen</label>
            <select name="from_warehouse_id" class="form-select" required>
              <option value="">Selecciona...</option>
              <?php if (!empty($whs)): foreach ($whs as $w): ?>
                <option value="<?= intval($w['warehouse_id']) ?>"><?= htmlspecialchars($w['name']) ?></option>
              <?php endforeach; endif; ?>
            </select>
          </div>
          <div class="mb-2">
            <label class="form-label small">Almacén destino</label>
            <select name="to_warehouse_id" class="form-select" required>
              <option value="">Selecciona...</option>
              <?php if (!empty($whs)): foreach ($whs as $w): ?>
                <option value="<?= intval($w['warehouse_id']) ?>"><?= htmlspecialchars($w['name']) ?></option>
              <?php endforeach; endif; ?>
            </select>
          </div>
          <div class="mb-2">
            <label class="form-label small">Cantidad</label>
            <input type="number" name="quantity" min="1" step="1" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="form-label small">Notas (opcional)</label>
            <textarea name="notes" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
          <button class="btn btn-info text-white js-submit-transfer" type="button" data-action="transfer">
            <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
            Transferir
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
