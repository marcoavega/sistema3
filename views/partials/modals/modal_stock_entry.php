<?php
// views/partials/modals/modal_stock_entry.php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<div class="modal fade" id="modalStockEntry" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <form id="formStockEntry">
        <input type="hidden" name="product_id" id="entry-product-id" value="">
        <div class="modal-header">
          <h5 class="modal-title">Entrada de Stock</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label class="form-label small">Almacén</label>
            <select name="warehouse_id" id="entry-warehouse" class="form-select" required>
              <option value="">Cargando...</option>
            </select>
          </div>
          <div class="mb-2">
            <label class="form-label small">Cantidad</label>
            <input required type="number" min="1" name="qty" id="entry-qty" class="form-control" placeholder="ej. 5">
          </div>
          <div class="mb-2">
            <label class="form-label small">Notas (opcional)</label>
            <textarea name="note" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
          <button class="btn btn-success" type="submit">Registrar Entrada</button>
        </div>
      </form>
    </div>
  </div>
</div>
