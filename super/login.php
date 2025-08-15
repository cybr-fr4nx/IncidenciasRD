<?php
require_once '../includes/session.php';
// Definir $basePath siempre
$basePath = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\');
if ($basePath === '.' || $basePath === '/')
    $basePath = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../includes/db.php';
    $correo = $_POST['correo'] ?? '';
    $clave = $_POST['clave'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM Usuario WHERE correo = ? AND tipo_usuario = 'validador'");
    $stmt->execute([$correo]);
    $user = $stmt->fetch();

    if ($user && password_verify($clave, $user['clave'])) {
        $_SESSION['user_id'] = $user['id_usuario'];
        $_SESSION['user_name'] = $user['nombre'];
        $_SESSION['user_type'] = $user['tipo_usuario'];
        header('Location: ' . $basePath . '/super/');
        exit();
    } else {
        $error = 'Credenciales incorrectas.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>Login Validador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .login-container {
            max-width: 400px;
            margin: 5rem auto;
            padding: 2rem;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2 class="text-center">Administrador</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        <form method="POST">
            <div class="mb-3"><label for="correo" class="form-label">Correo</label><input type="email"
                    class="form-control" id="correo" name="correo" required></div>
            <div class="mb-3"><label for="clave" class="form-label">Contraseña</label><input type="password"
                    class="form-control" id="clave" name="clave" required></div>
            <div class="d-grid"><button type="submit" class="btn btn-primary">Ingresar</button></div>
        </form>
        <div class="d-grid gap-2 mt-4">
            <h6 class="mb-2">Usuarios:</h6>
            <a class="btn btn-outline-secondary" href="<?= $basePath ?>/api/auth.php">
                Iniciar sesión con Office 365
            </a>
        </div>
    </div>
</body>

</html>