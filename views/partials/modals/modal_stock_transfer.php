<?php
// views/partials/modals/modal_stock_transfer.php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<div class="modal fade" id="modalStockTransfer" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <form id="formStockTransfer">
        <input type="hidden" name="product_id" id="transfer-product-id" value="">
        <div class="modal-header">
          <h5 class="modal-title">Transferencia entre Almacenes</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-2">
            <div class="col-6">
              <label class="form-label small">Desde</label>
              <select name="from_warehouse" id="transfer-from" class="form-select" required>
                <option value="">Cargando...</option>
              </select>
            </div>
            <div class="col-6">
              <label class="form-label small">Hacia</label>
              <select name="to_warehouse" id="transfer-to" class="form-select" required>
                <option value="">Cargando...</option>
              </select>
            </div>
          </div>
          <div class="mb-2 mt-2">
            <label class="form-label small">Cantidad</label>
            <input required type="number" min="1" name="qty" id="transfer-qty" class="form-control" placeholder="ej. 3">
          </div>
          <div class="mb-2">
            <label class="form-label small">Nota (opcional)</label>
            <textarea name="note" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
          <button class="btn btn-primary" type="submit">Transferir</button>
        </div>
      </form>
    </div>
  </div>
</div>
