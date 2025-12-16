<div class="modal fade" id="modalAddCurrency" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg">

      <!-- HEADER -->
      <div class="modal-header bg-primary text-white position-relative overflow-hidden border-0 py-3">
        <div class="modal-header-icon-bg">
          <i class="fas fa-coins"></i>
        </div>

        <div class="position-relative z-1">
          <h5 class="modal-title fw-bold d-flex align-items-center mb-0">
            <i class="fas fa-plus-circle me-2"></i> Nueva Moneda
          </h5>
          <small class="opacity-75">Registro de moneda del sistema</small>
        </div>

        <button type="button" class="btn-close btn-close-white z-1" data-bs-dismiss="modal"></button>
      </div>

      <!-- BODY -->
      <div class="modal-body p-4 bg-body-tertiary">
        <form id="formAddCurrency">

          <input type="hidden" name="action" value="create_currency">

          <div class="mb-3">
            <label class="form-label small fw-bold text-uppercase text-muted">
              Código
            </label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="fas fa-hashtag text-primary"></i>
              </span>
              <input
                type="text"
                class="form-control fw-semibold"
                name="currency_code"
                maxlength="10"
                required
                placeholder="USD">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-bold text-uppercase text-muted">
              Nombre
            </label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="fas fa-font"></i>
              </span>
              <input
                type="text"
                class="form-control"
                name="currency_name"
                maxlength="100"
                required
                placeholder="Dólar estadounidense">
            </div>
          </div>

          <div class="mb-0">
            <label class="form-label small fw-bold text-uppercase text-muted">
              País
            </label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="fas fa-flag"></i>
              </span>
              <input
                type="text"
                class="form-control"
                name="country"
                maxlength="100"
                required
                placeholder="Estados Unidos">
            </div>
          </div>

        </form>
      </div>

      <!-- FOOTER -->
      <div class="modal-footer bg-body border-top-0 py-3">
        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
          Cancelar
        </button>
        <button type="submit" form="formAddCurrency" class="btn btn-primary px-4 shadow-sm">
          <i class="fas fa-save me-2"></i> Guardar
        </button>
      </div>

    </div>
  </div>
</div>
