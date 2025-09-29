<!-- Modal para Logout (Cerrar Sesión) -->
<div class="modal fade" id="modalLogout" tabindex="-1" aria-labelledby="modalLogoutLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalLogoutLabel"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="formLogout" method="POST">
				<div class="modal-body">
					<p>¿Estás seguro que deseas cerrar sesión?</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-danger">Cerrar Sesión</button>
				</div>
			</form>
		</div>
	</div>
</div>
