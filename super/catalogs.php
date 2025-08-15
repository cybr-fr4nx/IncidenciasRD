<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
require_validator();
require_once '../includes/db.php';

$basePath = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\');
if ($basePath === '.' || $basePath === '/')
    $basePath = '';

// --- CRUD Provincias ---
if (isset($_POST['add_provincia'])) {
    $nombre = trim($_POST['nombre_provincia']);
    if ($nombre) {
        $stmt = $pdo->prepare("INSERT INTO Provincia (nombre) VALUES (?)");
        $stmt->execute([$nombre]);
    }
    header('Location: ' . $basePath . '/super/catalogs.php#provincias');
    exit;
}
if (isset($_POST['edit_provincia'])) {
    $id = $_POST['id_provincia'];
    $nombre = trim($_POST['nombre_provincia']);
    $stmt = $pdo->prepare("UPDATE Provincia SET nombre=? WHERE id_provincia=?");
    $stmt->execute([$nombre, $id]);
    header('Location: ' . $basePath . '/super/catalogs.php#provincias');
    exit;
}
if (isset($_POST['delete_provincia'])) {
    $id = $_POST['id_provincia'];
    $stmt = $pdo->prepare("DELETE FROM Provincia WHERE id_provincia=?");
    $stmt->execute([$id]);
    header('Location: ' . $basePath . '/super/catalogs.php#provincias');
    exit;
}

// --- CRUD Municipios ---
if (isset($_POST['add_municipio'])) {
    $nombre = trim($_POST['nombre_municipio']);
    $id_provincia = $_POST['id_provincia'];
    if ($nombre && $id_provincia) {
        $stmt = $pdo->prepare("INSERT INTO Municipio (nombre, id_provincia) VALUES (?, ?)");
        $stmt->execute([$nombre, $id_provincia]);
    }
    header('Location: ' . $basePath . '/super/catalogs.php#municipios');
    exit;
}
if (isset($_POST['edit_municipio'])) {
    $id = $_POST['id_municipio'];
    $nombre = trim($_POST['nombre_municipio']);
    $id_provincia = $_POST['id_provincia'];
    $stmt = $pdo->prepare("UPDATE Municipio SET nombre=?, id_provincia=? WHERE id_municipio=?");
    $stmt->execute([$nombre, $id_provincia, $id]);
    header('Location: ' . $basePath . '/super/catalogs.php#municipios');
    exit;
}
if (isset($_POST['delete_municipio'])) {
    $id = $_POST['id_municipio'];
    $stmt = $pdo->prepare("DELETE FROM Municipio WHERE id_municipio=?");
    $stmt->execute([$id]);
    header('Location: ' . $basePath . '/super/catalogs.php#municipios');
    exit;
}

// --- CRUD Barrios ---
if (isset($_POST['add_barrio'])) {
    $nombre = trim($_POST['nombre_barrio']);
    $id_municipio = $_POST['id_municipio'];
    if ($nombre && $id_municipio) {
        $stmt = $pdo->prepare("INSERT INTO Barrio (nombre, id_municipio) VALUES (?, ?)");
        $stmt->execute([$nombre, $id_municipio]);
    }
    header('Location: ' . $basePath . '/super/catalogs.php#barrios');
    exit;
}
if (isset($_POST['edit_barrio'])) {
    $id = $_POST['id_barrio'];
    $nombre = trim($_POST['nombre_barrio']);
    $id_municipio = $_POST['id_municipio'];
    $stmt = $pdo->prepare("UPDATE Barrio SET nombre=?, id_municipio=? WHERE id_barrio=?");
    $stmt->execute([$nombre, $id_municipio, $id]);
    header('Location: ' . $basePath . '/super/catalogs.php#barrios');
    exit;
}
if (isset($_POST['delete_barrio'])) {
    $id = $_POST['id_barrio'];
    $stmt = $pdo->prepare("DELETE FROM Barrio WHERE id_barrio=?");
    $stmt->execute([$id]);
    header('Location: ' . $basePath . '/super/catalogs.php#barrios');
    exit;
}

// --- CRUD Tipos de Incidencia ---
if (isset($_POST['add_tipo'])) {
    $nombre = trim($_POST['nombre_tipo']);
    $icono = trim($_POST['icono_url']);
    if ($nombre) {
        $stmt = $pdo->prepare("INSERT INTO Tipo_Incidencia (nombre, icono_url) VALUES (?, ?)");
        $stmt->execute([$nombre, $icono]);
    }
    header('Location: ' . $basePath . '/super/catalogs.php#tipos');
    exit;
}
if (isset($_POST['edit_tipo'])) {
    $id = $_POST['id_tipo'];
    $nombre = trim($_POST['nombre_tipo']);
    $icono = trim($_POST['icono_url']);
    $stmt = $pdo->prepare("UPDATE Tipo_Incidencia SET nombre=?, icono_url=? WHERE id_tipo=?");
    $stmt->execute([$nombre, $icono, $id]);
    header('Location: ' . $basePath . '/super/catalogs.php#tipos');
    exit;
}
if (isset($_POST['delete_tipo'])) {
    $id = $_POST['id_tipo'];
    $stmt = $pdo->prepare("DELETE FROM Tipo_Incidencia WHERE id_tipo=?");
    $stmt->execute([$id]);
    header('Location: ' . $basePath . '/super/catalogs.php#tipos');
    exit;
}

// Obtener catalogos
$provincias = $pdo->query("SELECT * FROM Provincia ORDER BY nombre")->fetchAll();
$municipios = $pdo->query("SELECT m.*, p.nombre as provincia FROM Municipio m JOIN Provincia p ON m.id_provincia=p.id_provincia ORDER BY m.nombre")->fetchAll();
$barrios = $pdo->query("SELECT b.*, m.nombre as municipio FROM Barrio b JOIN Municipio m ON b.id_municipio=m.id_municipio ORDER BY b.nombre")->fetchAll();
$tipos = $pdo->query("SELECT * FROM Tipo_Incidencia ORDER BY nombre")->fetchAll();

$page_title = 'Catálogos del Sistema';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title><?= $page_title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .tab-content {
            border: 1px solid #ddd;
            border-top: 0;
            padding: 2rem;
            background: #fff;
        }

        .nav-tabs .nav-link.active {
            background: #f8f9fa;
            border-bottom: 1px solid #fff;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container"><a class="navbar-brand" href="<?= $basePath ?>/super/">Panel Validador</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="<?= $basePath ?>/super/incidents.php">Validar
                            Incidencias</a></li>
                    <li class="nav-item"><a class="nav-link active" href="<?= $basePath ?>/super/catalogs.php">Gestionar
                            Catálogos</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $basePath ?>/super/logout.php">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="container mt-4">
        <h2>Catálogos del Sistema</h2>
        <ul class="nav nav-tabs" id="catalogTabs" role="tablist">
            <li class="nav-item" role="presentation"><button class="nav-link active" id="provincias-tab"
                    data-bs-toggle="tab" data-bs-target="#provincias" type="button" role="tab">Provincias</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" id="municipios-tab" data-bs-toggle="tab"
                    data-bs-target="#municipios" type="button" role="tab">Municipios</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" id="barrios-tab" data-bs-toggle="tab"
                    data-bs-target="#barrios" type="button" role="tab">Barrios</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" id="tipos-tab" data-bs-toggle="tab"
                    data-bs-target="#tipos" type="button" role="tab">Tipos de Incidencia</button></li>
        </ul>
        <div class="tab-content">
            <!-- Provincias -->
            <div class="tab-pane fade show active" id="provincias" role="tabpanel">
                <h4>Provincias</h4>
                <form method="POST" class="row g-2 mb-3">
                    <div class="col-auto"><input type="text" name="nombre_provincia" class="form-control"
                            placeholder="Nueva provincia" required></div>
                    <div class="col-auto"><button type="submit" name="add_provincia"
                            class="btn btn-success">Agregar</button></div>
                </form>
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($provincias as $p): ?>
                            <tr>
                                <form method="POST">
                                    <td><input type="text" name="nombre_provincia"
                                            value="<?= htmlspecialchars($p['nombre']) ?>" class="form-control" required>
                                    </td>
                                    <td>
                                        <input type="hidden" name="id_provincia" value="<?= $p['id_provincia'] ?>">
                                        <button type="submit" name="edit_provincia"
                                            class="btn btn-primary btn-sm">Editar</button>
                                        <button type="submit" name="delete_provincia" class="btn btn-danger btn-sm"
                                            onclick="return confirm('¿Eliminar provincia?')">Eliminar</button>
                                    </td>
                                </form>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <!-- Municipios -->
            <div class="tab-pane fade" id="municipios" role="tabpanel">
                <h4>Municipios</h4>
                <form method="POST" class="row g-2 mb-3">
                    <div class="col-auto"><input type="text" name="nombre_municipio" class="form-control"
                            placeholder="Nuevo municipio" required></div>
                    <div class="col-auto">
                        <select name="id_provincia" class="form-select" required>
                            <option value="">Provincia</option>
                            <?php foreach ($provincias as $p): ?>
                                <option value="<?= $p['id_provincia'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-auto"><button type="submit" name="add_municipio"
                            class="btn btn-success">Agregar</button></div>
                </form>
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Provincia</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($municipios as $m): ?>
                            <tr>
                                <form method="POST">
                                    <td><input type="text" name="nombre_municipio"
                                            value="<?= htmlspecialchars($m['nombre']) ?>" class="form-control" required>
                                    </td>
                                    <td>
                                        <select name="id_provincia" class="form-select" required>
                                            <?php foreach ($provincias as $p): ?>
                                                <option value="<?= $p['id_provincia'] ?>"
                                                    <?= $m['id_provincia'] == $p['id_provincia'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($p['nombre']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="hidden" name="id_municipio" value="<?= $m['id_municipio'] ?>">
                                        <button type="submit" name="edit_municipio"
                                            class="btn btn-primary btn-sm">Editar</button>
                                        <button type="submit" name="delete_municipio" class="btn btn-danger btn-sm"
                                            onclick="return confirm('¿Eliminar municipio?')">Eliminar</button>
                                    </td>
                                </form>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <!-- Barrios -->
            <div class="tab-pane fade" id="barrios" role="tabpanel">
                <h4>Barrios</h4>
                <form method="POST" class="row g-2 mb-3">
                    <div class="col-auto"><input type="text" name="nombre_barrio" class="form-control"
                            placeholder="Nuevo barrio" required></div>
                    <div class="col-auto">
                        <select name="id_municipio" class="form-select" required>
                            <option value="">Municipio</option>
                            <?php foreach ($municipios as $m): ?>
                                <option value="<?= $m['id_municipio'] ?>"><?= htmlspecialchars($m['nombre']) ?>
                                    (<?= htmlspecialchars($m['provincia']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-auto"><button type="submit" name="add_barrio"
                            class="btn btn-success">Agregar</button></div>
                </form>
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Municipio</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($barrios as $b): ?>
                            <tr>
                                <form method="POST">
                                    <td><input type="text" name="nombre_barrio"
                                            value="<?= htmlspecialchars($b['nombre']) ?>" class="form-control" required>
                                    </td>
                                    <td>
                                        <select name="id_municipio" class="form-select" required>
                                            <?php foreach ($municipios as $m): ?>
                                                <option value="<?= $m['id_municipio'] ?>"
                                                    <?= $b['id_municipio'] == $m['id_municipio'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($m['nombre']) ?>
                                                    (<?= htmlspecialchars($m['provincia']) ?>)</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="hidden" name="id_barrio" value="<?= $b['id_barrio'] ?>">
                                        <button type="submit" name="edit_barrio"
                                            class="btn btn-primary btn-sm">Editar</button>
                                        <button type="submit" name="delete_barrio" class="btn btn-danger btn-sm"
                                            onclick="return confirm('¿Eliminar barrio?')">Eliminar</button>
                                    </td>
                                </form>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <!-- Tipos de Incidencia -->
            <div class="tab-pane fade" id="tipos" role="tabpanel">
                <h4>Tipos de Incidencia</h4>
                <form method="POST" class="row g-2 mb-3">
                    <div class="col-auto"><input type="text" name="nombre_tipo" class="form-control"
                            placeholder="Nuevo tipo" required></div>
                    <div class="col-auto"><input type="url" name="icono_url" class="form-control"
                            placeholder="URL del icono (opcional)"></div>
                    <div class="col-auto"><button type="submit" name="add_tipo" class="btn btn-success">Agregar</button>
                    </div>
                </form>
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Icono</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tipos as $t): ?>
                            <tr>
                                <form method="POST">
                                    <td><input type="text" name="nombre_tipo" value="<?= htmlspecialchars($t['nombre']) ?>"
                                            class="form-control" required></td>
                                    <td><input type="url" name="icono_url" value="<?= htmlspecialchars($t['icono_url']) ?>"
                                            class="form-control"><br><?php if ($t['icono_url']): ?><img
                                                src="<?= $t['icono_url'] ?>" alt="icono" style="height:32px;"><?php endif; ?>
                                    </td>
                                    <td>
                                        <input type="hidden" name="id_tipo" value="<?= $t['id_tipo'] ?>">
                                        <button type="submit" name="edit_tipo"
                                            class="btn btn-primary btn-sm">Editar</button>
                                        <button type="submit" name="delete_tipo" class="btn btn-danger btn-sm"
                                            onclick="return confirm('¿Eliminar tipo?')">Eliminar</button>
                                    </td>
                                </form>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>