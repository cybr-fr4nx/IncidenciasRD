<?php
session_start();
// Destruir todas las variables de sesion
$_SESSION = array();
// (Si se desea destruir la sesion completamente, tambien se debe borrar la cookie de sesion)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}
session_destroy();
// Redirigir al login de super
$basePath = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\');
if ($basePath === '.' || $basePath === '/')
    $basePath = '';
header('Location: ' . $basePath . '/super/login.php');
exit;
