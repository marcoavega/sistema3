<div class="modal fade" id="modalAddRate" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg">

      <!-- HEADER -->
      <div class="modal-header bg-primary text-white position-relative overflow-hidden border-0 py-3">
        <div class="modal-header-icon-bg">
          <i class="fas fa-chart-line"></i>
        </div>

        <div class="position-relative z-1">
          <h5 class="modal-title fw-bold d-flex align-items-center mb-0">
            <i class="fas fa-plus-circle me-2"></i> Nuevo Tipo de Cambio
          </h5>
          <small class="opacity-75">Registro histórico de conversión</small>
        </div>

        <button type="button" class="btn-close btn-close-white z-1" data-bs-dismiss="modal"></button>
      </div>

      <!-- BODY -->
      <div class="modal-body p-4 bg-body-tertiary">
        <form id="formAddRate">

          <input type="hidden" name="action" value="create_rate">

          <!-- Moneda -->
          <div class="mb-3">
            <label class="form-label small fw-bold text-uppercase text-muted">
              Moneda
            </label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="fas fa-coins text-primary"></i>
              </span>
              <select name="currency_id" class="form-select" required>
                <option value="">Selecciona...</option>
                <?php foreach ($currencies as $c): ?>
                  <option value="<?= $c['currency_id'] ?>">
                    <?= htmlspecialchars($c['currency_code']) ?> — <?= htmlspecialchars($c['currency_name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <!-- Fecha -->
          <div class="mb-3">
            <label class="form-label small fw-bold text-uppercase text-muted">
              Fecha
            </label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="fas fa-calendar-alt"></i>
              </span>
              <input
                type="date"
                name="rate_date"
                class="form-control"
                required
                value="<?= date('Y-m-d') ?>">
            </div>
          </div>

          <!-- Tipo de cambio -->
          <div class="mb-3">
            <label class="form-label small fw-bold text-uppercase text-muted">
              Tipo de Cambio
            </label>
            <div class="input-group">
              <span class="input-group-text bg-success text-white fw-bold">$</span>
              <input
                type="number"
                step="0.000001"
                name="rate"
                class="form-control fw-semibold"
                required
                placeholder="Ej. 18.500000">
            </div>
          </div>

          <!-- Notas -->
          <div class="mb-0">
            <label class="form-label small fw-bold text-uppercase text-muted">
              Notas
            </label>
            <textarea
              name="notes"
              class="form-control"
              rows="2"
              placeholder="Observaciones opcionales..."></textarea>
          </div>

        </form>
      </div>

      <!-- FOOTER -->
      <div class="modal-footer bg-body border-top-0 py-3">
        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
          Cancelar
        </button>
        <button type="submit" form="formAddRate" class="btn btn-primary px-4 shadow-sm">
          <i class="fas fa-save me-2"></i> Guardar
        </button>
      </div>

    </div>
  </div>
</div>
