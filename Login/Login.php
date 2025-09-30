<?php
session_start();
if (isset($_SESSION['usuario'])) {
	header('Location: ../Dashboard/Dashboard.php');
	exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
	<title>Login | Portal Contratistas</title>
	<style>
	.login-bg {
		min-height: 100vh;
		background: #f7fafc;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		padding: 32px 0;
	}
	.login-container {
		width: 100%;
		max-width: 400px;
		background: none;
	}
	.login-header {
		text-align: center;
		margin-bottom: 24px;
	}
	.login-icon-bg {
		background: #2563eb;
		display: inline-flex;
		padding: 12px;
		border-radius: 12px;
		margin-bottom: 8px;
	}
	.login-icon {
		width: 32px;
		height: 32px;
		color: #fff;
	}
	.login-title {
		font-size: 2rem;
		font-weight: bold;
		color: #1a202c;
		margin: 16px 0 4px 0;
	}
	.login-subtitle {
		color: #4b5563;
		font-size: 1rem;
	}
	.login-form-card {
		background: #fff;
		border-radius: 12px;
		box-shadow: 0 4px 24px 0 rgba(0,0,0,0.08);
		padding: 32px 24px;
	}
	.login-form {
		display: flex;
		flex-direction: column;
		gap: 20px;
	}
	.login-field {
		display: flex;
		flex-direction: column;
		gap: 6px;
	}
	.login-label {
		font-size: 0.95rem;
		color: #374151;
		font-weight: 500;
	}
	.login-input-icon {
		position: relative;
		display: flex;
		align-items: center;
	}
	.login-input {
		width: 100%;
		padding: 10px 12px 10px 38px;
		border: 1px solid #d1d5db;
		border-radius: 6px;
		font-size: 1rem;
		color: #374151;
		background: #f9fafb;
		transition: border 0.2s, box-shadow 0.2s;
	}
	.login-input:focus {
		outline: none;
		border-color: #2563eb;
		box-shadow: 0 0 0 2px #2563eb33;
	}
	.login-svg-icon {
		position: absolute;
		left: 10px;
		top: 50%;
		transform: translateY(-50%);
		width: 18px;
		height: 18px;
		color: #9ca3af;
		pointer-events: none;
	}
	.login-options {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-bottom: 8px;
	}
	.login-remember {
		display: flex;
		align-items: center;
		font-size: 0.95rem;
		color: #374151;
	}
	.login-checkbox {
		margin-right: 6px;
		accent-color: #2563eb;
	}
	.login-forgot {
		color: #2563eb;
		text-decoration: none;
		font-size: 0.95rem;
		transition: color 0.2s;
	}
	.login-forgot:hover {
		color: #1d4ed8;
	}
	.login-btn {
		width: 100%;
		padding: 10px 0;
		background: #2563eb;
		color: #fff;
		font-size: 1rem;
		font-weight: 600;
		border: none;
		border-radius: 6px;
		cursor: pointer;
		transition: background 0.2s;
	}
	.login-btn:hover {
		background: #1d4ed8;
	}
	.login-btn:disabled {
		opacity: 0.5;
		cursor: not-allowed;
	}
	.login-error {
		background: #fee2e2;
		color: #b91c1c;
		border-radius: 6px;
		padding: 10px;
		margin-top: 10px;
		font-size: 0.95rem;
		display: flex;
		align-items: center;
		gap: 8px;
	}
	.login-footer {
		text-align: center;
		margin-top: 32px;
		color: #6b7280;
		font-size: 0.85rem;
	}
	@media (max-width: 500px) {
		.login-container {
			max-width: 95vw;
		}
		.login-form-card {
			padding: 20px 8px;
		}
	}
	</style>
</head>
<body>
	<div class="login-bg">
		<div class="login-container">
			<div class="login-header">
				<div class="login-icon-bg">
					<svg class="login-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0h4M9 7h6m-6 4h6m-6 4h6"></path>
					</svg>
				</div>
				<h2 class="login-title">Portal Contratistas</h2>
				<p class="login-subtitle">Inicia sesión en tu cuenta</p>
			</div>
			<div class="login-form-card">
				<form class="login-form">
					<div class="login-field">
						<label for="usuario" class="login-label">Usuario</label>
						<div class="login-input-icon">
							<input id="usuario" type="text" maxlength="30" required class="login-input" placeholder="nombre_usuario">
							<span class="login-svg-icon">
								<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
								</svg>
							</span>
						</div>
					</div>
					<div class="login-field">
						<label for="password" class="login-label">Contraseña</label>
						<div class="login-input-icon">
							<input id="password" type="password" minlength="8" required class="login-input" placeholder="••••••••">
							<span class="login-svg-icon">
								<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
								</svg>
							</span>
						</div>
					</div>
					<div class="login-options">
						<label class="login-remember">
							<input type="checkbox" class="login-checkbox"> Recordarme
						</label>
						<a href="#" class="login-forgot">¿Olvidaste tu contraseña?</a>
					</div>
					<button type="submit" class="login-btn">Iniciar Sesión</button>
					<div class="login-error" style="display:none;">Error de autenticación</div>
				</form>
			</div>
			<div class="login-footer">
				<p>© 2025 Portal Contratistas. Todos los derechos reservados.</p>
			</div>
		</div>
	</div>
		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
		<script src="vistas/assets/js/notifications.js"></script>
        <script src="vistas/assets/js/toasts.js"></script>
		<script src="vistas/assets/js/Login.js"></script>
        <script src="vistas/assets/js/prevent-multiple-submits.js"></script>
</body>
</html>