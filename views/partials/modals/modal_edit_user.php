<!-- Solo se carga cuando este modal se incluye -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/modal-edit-user-style.css">

<!-- Modal para la edición de usuario, utilizando Bootstrap -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            
            <div class="modal-header bg-primary text-white position-relative overflow-hidden border-0 py-4">
                <div class="modal-header-icon-bg">
                    <i class="fas fa-user-edit"></i>
                </div>
                
                <div class="position-relative z-1 ms-2">
                    <h4 class="modal-title fw-bold d-flex align-items-center" id="editUserModalLabel">
                        <i class="fas fa-edit me-2"></i> Editar Usuario
                    </h4>
                    <p class="mb-0 opacity-75 small">Modifique los privilegios o información de la cuenta seleccionada</p>
                </div>
                <button type="button" class="btn-close btn-close-white z-1" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body p-4 bg-body-tertiary">
                <form id="editUserForm">
                    <input type="hidden" id="edit-user-id" name="user_id">

                    <div class="row g-4">
                        
                        <div class="col-lg-8">
                            
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header bg-body border-bottom-0 pt-4 pb-2">
                                    <h6 class="fw-bold text-primary mb-0"><i class="fas fa-user-circle me-2"></i>Perfil del Usuario</h6>
                                </div>
                                <div class="card-body pt-2">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="edit-username" class="form-label small text-muted fw-bold text-uppercase">Nombre de Usuario</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user text-primary"></i></span>
                                                <input type="text" class="form-control" id="edit-username" name="username" required placeholder="Ej. juan.perez">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="edit-email" class="form-label small text-muted fw-bold text-uppercase">Email Institucional</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-envelope text-primary"></i></span>
                                                <input type="email" class="form-control" id="edit-email" name="email" required placeholder="usuario@correo.com">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-body border-bottom-0 pt-4 pb-2">
                                    <h6 class="fw-bold text-primary mb-0"><i class="fas fa-lock me-2"></i>Seguridad</h6>
                                </div>
                                <div class="card-body pt-2">
                                    <p class="small text-muted mb-0">
                                        <i class="fas fa-info-circle me-1"></i> Si desea cambiar la contraseña, diríjase al módulo de restablecimiento o gestión de seguridad avanzada.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-body">
                                    <h6 class="fw-bold text-primary mb-3"><i class="fas fa-user-shield me-2"></i>Nivel de Acceso</h6>
                                    
                                    <div class="mb-3">
                                        <label for="edit-level" class="form-label small fw-bold">Nivel Asignado</label>
                                        <select id="edit-level" name="id_level_user" class="form-select" required>
                                            <option value="">Seleccionar nivel...</option>
                                            <?php foreach ($levels as $lvl): ?>
                                                <option value="<?= $lvl['id_level_user'] ?>">
                                                    <?= htmlspecialchars($lvl['description_level']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-0">
                                        <label for="edit-user-status" class="form-label small fw-bold">Estado de Cuenta</label>
                                        <select class="form-select" id="edit-user-status" name="status">
                                            <option value="1">✅ Activo</option>
                                            <option value="0">❌ Suspendido</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow-sm border-0 bg-info text-white">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-history fa-2x me-3 opacity-50"></i>
                                        <div>
                                            <small class="fw-bold d-block">Auditoría:</small>
                                            <small>Toda modificación queda registrada en el log del sistema.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div> </form>
            </div>

            <div class="modal-footer border-top-0 bg-body py-3">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary px-4 shadow-sm" id="saveChangesBtn">
                    <i class="fas fa-save me-2"></i> Guardar Cambios
                    <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                </button>
            </div>

        </div>
    </div>
</div>