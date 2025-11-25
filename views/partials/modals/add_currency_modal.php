<div class="modal fade" id="modalAddCurrency" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <form method="post" id="formAddCurrency">
        <input type="hidden" name="action" value="create_currency">
        <div class="modal-header">
          <h5 class="modal-title">Nueva Moneda</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label class="form-label small">Código (ej. USD)</label>
            <input required class="form-control" name="currency_code" maxlength="10" placeholder="USD">
          </div>
          <div class="mb-2">
            <label class="form-label small">Nombre</label>
            <input required class="form-control" name="currency_name" maxlength="100" placeholder="Dólar estadounidense">
          </div>
          <div class="mb-2">
            <label class="form-label small">País</label>
            <input required class="form-control" name="country" maxlength="100" placeholder="Estados Unidos">
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
          <button class="btn btn-primary" type="submit">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>
