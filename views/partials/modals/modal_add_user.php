<!-- Modal: Agregar Usuario -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg">
      
      <!-- Header con estilo e ícono -->
      <div class="modal-header bg-gradient position-relative overflow-hidden">
        <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10">
          <div class="d-flex align-items-center justify-content-center h-100">
            <i class="fas fa-user-plus" style="font-size: 120px;"></i>
          </div>
        </div>
        <div class="position-relative">
          <h4 class="modal-title fw-bold mb-0" id="addUserModalLabel">
            <i class="fas fa-user-plus me-2"></i>
            Agregar Nuevo Usuario
          </h4>
          <small class="opacity-75">Complete la información del usuario</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <!-- Cuerpo del formulario -->
      <div class="modal-body p-4">
        <form id="addUserForm">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="new-username" class="form-label fw-semibold">
                <i class="fas fa-user me-1 text-muted"></i> Usuario
              </label>
              <input type="text" class="form-control form-control-lg" id="new-username" required placeholder="Ej. juan123">
            </div>

            <div class="col-md-6">
              <label for="new-email" class="form-label fw-semibold">
                <i class="fas fa-envelope me-1 text-muted"></i> Email
              </label>
              <input type="email" class="form-control form-control-lg" id="new-email" required placeholder="usuario@correo.com">
            </div>

            <div class="col-md-6">
              <label for="new-password" class="form-label fw-semibold">
                <i class="fas fa-lock me-1 text-muted"></i> Contraseña
              </label>
              <input type="password" class="form-control form-control-lg" id="new-password" required placeholder="••••••••">
            </div>

            <div class="col-md-6">
              <label for="new-level" class="form-label fw-semibold">
                <i class="fas fa-user-shield me-1 text-muted"></i> Nivel
              </label>
              <select id="new-level" class="form-select form-select-lg" required>
                <?php foreach ($levels as $lvl): ?>
                  <option value="<?= $lvl['id_level_user'] ?>">
                    <?= htmlspecialchars($lvl['description_level']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </form>
      </div>

      <!-- Footer con botones estilizados -->
      <div class="modal-footer border-0 p-4">
        <div class="d-flex gap-2 w-100 justify-content-end">
          <button type="button" class="btn btn-outline-secondary btn-lg px-4" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i> Cancelar
          </button>
          <button type="button" class="btn btn-primary btn-lg px-4 shadow" id="saveNewUserBtn">
            <i class="fas fa-save me-2"></i>
            <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
            Guardar Usuario
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Estilos del modal (igual al de productos) -->

