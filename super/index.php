<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
require_validator();
$page_title = 'Dashboard Validador';
// Definir $basePath manualmente para rutas absolutas
$basePath = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\');
if ($basePath === '.' || $basePath === '/')
    $basePath = '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title><?= $page_title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container"><a class="navbar-brand" href="<?= $basePath ?>/super/">Panel Validador</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="<?= $basePath ?>/super/incidents.php">Validar
                            Incidencias</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $basePath ?>/super/catalogs.php">Gestionar
                            Catálogos</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $basePath ?>/super/logout.php">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="container mt-4">
        <h1>Bienvenido, <?= htmlspecialchars($_SESSION['user_name']) ?></h1>
        <p>Desde aquí puedes gestionar las incidencias y catálogos del sistema.</p>
        <!-- Aquí irian las estadisticas con Chart.js -->
    </main>
</body>

</html>