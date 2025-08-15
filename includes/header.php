<?php require_once 'session.php';
// Detecta el path base automaticamente
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
if ($basePath === '.' || $basePath === '/')
    $basePath = '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Reporte de Incidencias RD' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    <link rel="stylesheet" href="<?= $basePath ?>/assets/css/style.css">
    <script>window.basePath = "<?= $basePath ?>";</script>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="<?= $basePath ?>/">🚨 Incidencias RD</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= $basePath ?>/report.php">Reportar Incidencia</a>
                        </li>
                        <li class="nav-item"><span class="nav-link text-white">Hola,
                                <?= htmlspecialchars($_SESSION['user_name']) ?></span></li>
                        <li class="nav-item"><a class="nav-link" href="<?= $basePath ?>/logout.php">Cerrar Sesión</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="<?= $basePath ?>/login.php">Iniciar Sesión /
                                Registrarse</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <main class="container mt-4 pt-5">