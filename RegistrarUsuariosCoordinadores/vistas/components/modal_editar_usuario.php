<!-- Modal para Editar Usuario -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarLabel"><i class="fas fa-user-edit"></i> Editar Usuario Coordinador</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditarUsuario" method="POST">
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_usuario" class="form-label">Nombre de Usuario</label>
                        <input type="text" class="form-control" id="edit_usuario" name="usuario" required minlength="2" maxlength="25">
                        <div class="invalid-feedback">
                            El nombre de usuario debe tener entre 2 y 25 caracteres
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_password" class="form-label">Nueva Contraseña (dejar vacío para mantener la actual)</label>
                        <div class="input-group has-validation">
                            <input type="password" class="form-control" id="edit_password" minlength="8">
                            <button class="btn btn-outline-secondary" type="button" onclick="toggleEditPassword()">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-outline-primary" type="button" onclick="generarEditPassword()" title="Generar contraseña segura">
                                <i class="fas fa-key"></i>
                            </button>
                            <div class="invalid-feedback">
                                La contraseña debe tener al menos 8 caracteres
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">COPEs Disponibles</label>
                        <div class="cope-checkboxes" id="edit-cope-checkboxes" style="max-height: 200px; overflow-y: auto;">
                            <!-- Los COPEs se cargarán dinámicamente via JavaScript -->
                        </div>
                        <small class="form-text text-muted">
                            Puede modificar los COPEs asignados a este coordinador. Se muestran todos los COPEs disponibles.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>