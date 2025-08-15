<?php
require_once 'includes/session.php';
require_once 'includes/header.php';
// Destruir la sesion
session_unset();
session_destroy();
header('Location: ' . $basePath . '/login.php');
exit();
?>