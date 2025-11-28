<?php
// views/partials/modals/modal_stock_exit.php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<div class="modal fade" id="modalStockExit" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <form id="formStockExit">
        <input type="hidden" name="product_id" id="exit-product-id" value="">
        <div class="modal-header">
          <h5 class="modal-title">Salida / Descontar Stock</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label class="form-label small">Almacén</label>
            <select name="warehouse_id" id="exit-warehouse" class="form-select" required>
              <option value="">Cargando...</option>
            </select>
          </div>
          <div class="mb-2">
            <label class="form-label small">Cantidad a descontar</label>
            <input required type="number" min="1" name="qty" id="exit-qty" class="form-control" placeholder="ej. 2">
          </div>
          <div class="mb-2">
            <label class="form-label small">Motivo / Nota</label>
            <textarea name="note" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
          <button class="btn btn-danger" type="submit">Descontar</button>
        </div>
      </form>
    </div>
  </div>
</div>
