<!-- Modal para eliminar usuario -->
<div class="modal fade" id="modalEliminarUsuario" tabindex="-1" aria-labelledby="modalEliminarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalEliminarUsuarioLabel">
                    <i class="fas fa-trash-alt"></i> Eliminar Usuario Coordinador
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-0">¿Está seguro que desea eliminar al usuario coordinador <strong id="nombreUsuarioEliminar"></strong>?</p>
                <p class="text-danger mt-2"><i class="fas fa-exclamation-triangle"></i> Esta acción no se puede deshacer.</p>
                
                <form id="formEliminarUsuario" class="needs-validation" novalidate>
                    <input type="hidden" id="idUsuarioEliminar" name="idUsuario">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="submit" form="formEliminarUsuario" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</div>