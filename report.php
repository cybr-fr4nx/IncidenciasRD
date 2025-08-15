<?php
require_once 'includes/session.php';
require_once 'includes/header.php';
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo '<div class="alert alert-warning mt-4">Debes iniciar sesión para reportar una incidencia.</div>';
    include 'includes/footer.php';
    exit;
}

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $id_provincia = $_POST['provincia'] ?? '';
    $id_municipio = $_POST['municipio'] ?? '';
    $id_barrio = $_POST['barrio'] ?? '';
    $latitud = $_POST['latitud'] ?? '';
    $longitud = $_POST['longitud'] ?? '';
    $tipos = $_POST['tipos'] ?? [];
    $fecha = $_POST['fecha_ocurrencia'] ?? date('Y-m-d');
    $muertos = $_POST['muertos'] ?? 0;
    $heridos = $_POST['heridos'] ?? 0;
    $perdida = $_POST['perdida'] ?? 0;
    $redes = $_POST['redes'] ?? '';
    $id_usuario = $_SESSION['user_id'];
    $foto = $_FILES['foto'] ?? null;
    $foto_path = null;

    // Validar y subir foto
    if ($foto && $foto['tmp_name']) {
        $ext = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($ext, $permitidas) && $foto['size'] < 5 * 1024 * 1024) {
            $nombre_archivo = uniqid('incidencia_') . '.' . $ext;
            $destino = __DIR__ . '/uploads/' . $nombre_archivo;
            if (move_uploaded_file($foto['tmp_name'], $destino)) {
                $foto_path = 'uploads/' . $nombre_archivo;
            }
        }
    }

    if ($titulo && $descripcion && $id_provincia && $id_municipio && $id_barrio && $latitud && $longitud && !empty($tipos)) {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO Incidencia (titulo, descripcion, id_provincia, id_municipio, id_barrio, latitud, longitud, id_usuario, estado, fecha_registro, fecha_ocurrencia, muertos, heridos, perdida_estimada, link_redes, foto_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pendiente', NOW(), ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$titulo, $descripcion, $id_provincia, $id_municipio, $id_barrio, $latitud, $longitud, $id_usuario, $fecha, $muertos, $heridos, $perdida, $redes, $foto_path]);
        $id_incidencia = $pdo->lastInsertId();
        // Insertar tipos
        $stmtTipo = $pdo->prepare("INSERT INTO Incidencia_Tipo (id_incidencia, id_tipo) VALUES (?, ?)");
        foreach ($tipos as $tipo) {
            $stmtTipo->execute([$id_incidencia, $tipo]);
        }
        $pdo->commit();
        $mensaje = '<div class="alert alert-success mt-4">¡Incidencia reportada! Será revisada por un validador.</div>';
    } else {
        $mensaje = '<div class="alert alert-danger mt-4">Completa todos los campos y selecciona la ubicación en el mapa.</div>';
    }
}

$provincias = $pdo->query("SELECT * FROM Provincia ORDER BY nombre")->fetchAll();
$tipos = $pdo->query("SELECT * FROM Tipo_Incidencia ORDER BY nombre")->fetchAll();

?>
<div class="container mt-4">
    <h2>Reportar Incidencia</h2>
    <?= $mensaje ?>
    <form method="POST" class="mb-4" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Fecha de ocurrencia</label>
            <input type="date" name="fecha_ocurrencia" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" name="titulo" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="3" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Provincia</label>
            <select name="provincia" id="provincia" class="form-select" required>
                <option value="">Selecciona provincia</option>
                <?php foreach ($provincias as $prov): ?>
                    <option value="<?= $prov['id_provincia'] ?>"><?= htmlspecialchars($prov['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Municipio</label>
            <select name="municipio" id="municipio" class="form-select" required>
                <option value="">Selecciona municipio</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Barrio</label>
            <select name="barrio" id="barrio" class="form-select" required>
                <option value="">Selecciona barrio</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Tipo de Incidencia</label>
            <select name="tipos[]" class="form-select" multiple required>
                <?php foreach ($tipos as $tipo): ?>
                    <option value="<?= $tipo['id_tipo'] ?>"><?= htmlspecialchars($tipo['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
            <small class="text-muted">Puedes seleccionar varios tipos (Ctrl+Click).</small>
        </div>
        <div class="mb-3">
            <label class="form-label">Muertos</label>
            <input type="number" name="muertos" class="form-control" min="0" value="0">
        </div>
        <div class="mb-3">
            <label class="form-label">Heridos</label>
            <input type="number" name="heridos" class="form-control" min="0" value="0">
        </div>
        <div class="mb-3">
            <label class="form-label">Pérdida estimada (RD$)</label>
            <input type="number" name="perdida" class="form-control" min="0" step="0.01" value="0">
        </div>
        <div class="mb-3">
            <label class="form-label">Link a redes sociales</label>
            <input type="url" name="redes" class="form-control" placeholder="https://">
        </div>
        <div class="mb-3">
            <label class="form-label">Foto del hecho</label>
            <input type="file" name="foto" class="form-control" accept="image/*">
        </div>
        <div class="mb-3">
            <label class="form-label">Ubicación en el mapa</label>
            <div id="mapa" style="height: 350px;"></div>
            <input type="hidden" name="latitud" id="latitud">
            <input type="hidden" name="longitud" id="longitud">
            <div class="form-text">Haz clic en el mapa para seleccionar la ubicación.</div>
        </div>
        <button type="submit" class="btn btn-primary">Reportar</button>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Municipios
        const provinciaSel = document.getElementById('provincia');
        const municipioSel = document.getElementById('municipio');
        const barrioSel = document.getElementById('barrio');
        provinciaSel.addEventListener('change', function () {
            municipioSel.innerHTML = '<option value="">Cargando...</option>';
            fetch(window.basePath + '/api/catalogs.php?action=municipios&id=' + this.value)
                .then(r => r.json())
                .then(data => {
                    municipioSel.innerHTML = '<option value="">Selecciona municipio</option>';
                    data.forEach(m => {
                        municipioSel.innerHTML += `<option value="${m.id_municipio}">${m.nombre}</option>`;
                    });
                    barrioSel.innerHTML = '<option value="">Selecciona barrio</option>';
                });
        });
        municipioSel.addEventListener('change', function () {
            barrioSel.innerHTML = '<option value="">Cargando...</option>';
            fetch(window.basePath + '/api/catalogs.php?action=barrios&id=' + this.value)
                .then(r => r.json())
                .then(data => {
                    barrioSel.innerHTML = '<option value="">Selecciona barrio</option>';
                    data.forEach(b => {
                        barrioSel.innerHTML += `<option value="${b.id_barrio}">${b.nombre}</option>`;
                    });
                });
        });
        // Mapa interactivo
        if (document.getElementById('mapa')) {
            let map = L.map('mapa').setView([18.7357, -70.1627], 8);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            let marker;
            map.on('click', function (e) {
                if (marker) map.removeLayer(marker);
                marker = L.marker(e.latlng).addTo(map);
                document.getElementById('latitud').value = e.latlng.lat;
                document.getElementById('longitud').value = e.latlng.lng;
                // --- Autocompletar ubicacion ---
                fetch(window.basePath + '/api/reverse_geocode.php?lat=' + e.latlng.lat + '&lng=' + e.latlng.lng)
                    .then(r => r.json())
                    .then(data => {
                        if (data && !data.error) {
                            // Seleccionar provincia
                            provinciaSel.value = data.id_provincia;
                            provinciaSel.dispatchEvent(new Event('change'));
                            // Esperar a que municipios se carguen
                            setTimeout(() => {
                                municipioSel.value = data.id_municipio;
                                municipioSel.dispatchEvent(new Event('change'));
                                // Esperar a que barrios se carguen
                                setTimeout(() => {
                                    barrioSel.value = data.id_barrio;
                                }, 400);
                            }, 400);
                        }
                    });
            });
        }
    });
</script>
<?php include 'includes/footer.php'; ?>