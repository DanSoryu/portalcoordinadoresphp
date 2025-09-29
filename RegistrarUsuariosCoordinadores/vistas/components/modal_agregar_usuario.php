<!-- Modal para Agregar Usuario -->
<div class="modal fade" id="modalAgregarUsuario" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel"><i class="fas fa-user-plus"></i> Agregar Usuario Coordinador</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAgregarUsuario" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Nombre de Usuario</label>
                        <input type="text" class="form-control" id="usuario" name="usuario" required minlength="2" maxlength="25">
                        <div class="invalid-feedback">
                            El nombre de usuario debe tener entre 2 y 25 caracteres
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="input-group has-validation">
                            <input type="password" class="form-control" id="password" name="password" required minlength="8">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-outline-primary" type="button" onclick="generarPassword()" title="Generar contraseña segura">
                                <i class="fas fa-key"></i>
                            </button>
                            <div class="invalid-feedback">
                                La contraseña debe tener al menos 8 caracteres
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">COPEs Disponibles</label>
                        <div class="cope-checkboxes" style="max-height: 200px; overflow-y: auto;">
                            <?php if (empty($copes)): ?>
                                <div class="alert alert-info">
                                    No hay COPEs disponibles para asignar.
                                </div>
                            <?php else: ?>
                                <?php foreach ($copes as $cope): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="copes[]" 
                                           value="<?php echo htmlspecialchars($cope['id']); ?>" 
                                           id="cope<?php echo htmlspecialchars($cope['id']); ?>">
                                    <label class="form-check-label" for="cope<?php echo htmlspecialchars($cope['id']); ?>">
                                        <?php echo htmlspecialchars($cope['COPE']); ?>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <small class="form-text text-muted">
                            Solo se muestran los COPEs que no están asignados a ningún coordinador.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>