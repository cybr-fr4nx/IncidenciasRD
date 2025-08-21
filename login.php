<?php
$page_title = 'Iniciar Sesión';
include 'includes/header.php';
?>
<div class="container d-flex align-items-center justify-content-center" style="min-height: 90vh;">
    <div class="card shadow p-4" style="max-width: 400px; width: 100%;">
        <div class="text-center mb-4">
            <img src="https://cdn-icons-png.flaticon.com/512/5957/5957766.png" alt="Login" style="height:60px;">
            <h2 class="mt-2">Iniciar Sesión</h2>
            <p class="text-muted mb-0">Reporta incidencias con tu cuenta Office 365</p>
        </div>
        <a href="<?= $basePath ?>/api/auth.php" class="btn btn-primary btn-lg w-100 mb-3"
            style="background:#0078d4; border:none;">
            <img src="https://cdn-icons-png.flaticon.com/512/732/732221.png" alt="Office 365"
                style="height:22px;vertical-align:middle;margin-right:8px;"> Iniciar sesión con Office 365
        </a>
        <div class="text-center text-muted mb-2">o</div>
        <a href="<?= $basePath ?>/super/login.php" class="btn btn-outline-dark w-100">Acceso para Validadores</a>
        <div class="mt-4 text-center">
            <small class="text-muted">¿Problemas para acceder? Contacta al administrador.</small>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>