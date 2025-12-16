<div class="modal fade" id="editWarehouseModal" tabindex="-1" aria-labelledby="editWarehouseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            
            <div class="modal-header bg-primary text-white position-relative overflow-hidden border-0 py-4">
                <div class="modal-header-icon-bg">
                    <i class="fas fa-edit"></i>
                </div>
                
                <div class="position-relative z-1 ms-2">
                    <h4 class="modal-title fw-bold d-flex align-items-center" id="editWarehouseModalLabel">
                        <i class="fas fa-pen-square me-2"></i> Actualizar Almacén
                    </h4>
                    <p class="mb-0 opacity-75 small">Modifique el nombre o identificador del almacén seleccionado</p>
                </div>
                <button type="button" class="btn-close btn-close-white z-1" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body p-4 bg-body-tertiary">
                <form id="editWarehouseForm">
                    <input type="hidden" id="edit-warehouse-id" name="id">

                    <div class="row g-4">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-body border-bottom-0 pt-4 pb-2">
                                    <h6 class="fw-bold text-primary mb-0">
                                        <i class="fas fa-sync-alt me-2"></i>Editar Datos
                                    </h6>
                                </div>
                                <div class="card-body pt-2">
                                    <div class="mb-3">
                                        <label for="edit-warehouse-name" class="form-label small text-muted fw-bold text-uppercase">
                                            Nombre del Almacén
                                        </label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text border-end-0">
                                                <i class="fas fa-tag text-primary"></i>
                                            </span>
                                            <input type="text" id="edit-warehouse-name" name="name" 
                                                   class="form-control border-start-0 ps-0 fw-bold" 
                                                   required maxlength="255">
                                        </div>
                                        <div class="form-text mt-2 text-info">
                                            <i class="fas fa-info-circle me-1"></i> 
                                            Este cambio se verá reflejado inmediatamente en todos los productos vinculados.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer border-top-0 bg-body py-3">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="editWarehouseForm" class="btn btn-primary px-4 shadow-sm" id="saveChangesWarehouseBtn">
                    <i class="fas fa-check-circle me-2"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>