<div class="modal fade" id="modalAddRate" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <form method="post" id="formAddRate">
        <input type="hidden" name="action" value="create_rate">
        <div class="modal-header">
          <h5 class="modal-title">Nuevo Tipo de Cambio</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label class="form-label small">Moneda</label>
            <select name="currency_id" class="form-select" required>
              <option value="">Selecciona...</option>
              <?php foreach ($currencies as $c): ?>
                <option value="<?= $c['currency_id'] ?>">
                  <?= htmlspecialchars($c['currency_code']) ?> — <?= htmlspecialchars($c['currency_name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-2">
            <label class="form-label small">Fecha</label>
            <input type="date" name="rate_date" class="form-control" required value="<?= date('Y-m-d') ?>">
          </div>

          <div class="mb-2">
            <label class="form-label small">Tipo de cambio</label>
            <input type="number" step="0.000001" name="rate" class="form-control" required placeholder="ej. 18.500000">
          </div>

          <div class="mb-2">
            <label class="form-label small">Notas (opcional)</label>
            <textarea name="notes" class="form-control" rows="2"></textarea>
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
