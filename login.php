<?php
$page_title = 'Iniciar Sesión';
include 'includes/header.php';
// Aqui logica de autenticacion con OAuth
?>
<div class="login-container">
    <h2 class="text-center">Iniciar Sesión / Registrarse</h2>
    <p class="text-center text-muted">Usa tu cuenta de Office365 para reportar.</p>
    <div class="text-center d-grid gap-2 mt-4">
        <a href="<?= $basePath ?>/api/auth.php">Iniciar sesión con Office 365</a>
    </div>
    <div class="text-center mt-3">
        <a href="<?= $basePath ?>/super/login.php">Acceso para Validadores</a>
    </div>
</div>
<?php include 'includes/footer.php'; ?>