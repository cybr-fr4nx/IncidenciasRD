<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
require_validator();
require_once '../includes/db.php';

// Definir $basePath manualmente para rutas absolutas
$basePath = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\');
if ($basePath === '.' || $basePath === '/')
    $basePath = '';

// Logica para actualizar estado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_incidencia'])) {
    $estado = $_POST['estado'];
    $id = $_POST['id_incidencia'];
    $stmt = $pdo->prepare("UPDATE Incidencia SET estado = ? WHERE id_incidencia = ?");
    $stmt->execute([$estado, $id]);
    header('Location: ' . $basePath . '/super/incidents.php'); // Redireccion absoluta
    exit();
}

$stmt = $pdo->query("SELECT i.*, u.nombre as reportero FROM Incidencia i JOIN Usuario u ON i.id_usuario = u.id_usuario WHERE i.estado = 'pendiente' ORDER BY i.fecha_registro DESC");
$pendientes = $stmt->fetchAll();

$page_title = 'Validar Incidencias';
?>
<!-- Header HTML para el panel -->
<!DOCTYPE html>
<html lang="es">

<head>
    <title><?= $page_title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
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
        <h2>Incidencias Pendientes de Validación</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Descripción</th>
                        <th>Reportado por</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendientes as $inc): ?>
                        <tr>
                            <td><?= htmlspecialchars($inc['titulo']) ?></td>
                            <td><?= htmlspecialchars($inc['descripcion']) ?></td>
                            <td><?= htmlspecialchars($inc['reportero']) ?></td>
                            <td><?= $inc['fecha_registro'] ?></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="id_incidencia" value="<?= $inc['id_incidencia'] ?>">
                                    <button type="submit" name="estado" value="validada"
                                        class="btn btn-success btn-sm">Validar</button>
                                    <button type="submit" name="estado" value="rechazada"
                                        class="btn btn-danger btn-sm">Rechazar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($pendientes)): ?>
                        <tr>
                            <td colspan="5" class="text-center">No hay incidencias pendientes.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <h3 class="mt-5">Mapa de todas las incidencias</h3>
        <div id="mapa" class="rounded shadow mb-4" style="height: 450px;"></div>
    </main>

    <!-- Modal para detalles -->
    <div class="modal fade" id="incidentModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="incidentModalTitle"></h5><button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="incidentModalBody"></div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Cerrar</button></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            if (!document.getElementById("mapa")) return;
            const map = L.map("mapa").setView([18.7357, -70.1627], 8);
            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png").addTo(map);
            const markers = L.markerClusterGroup();
            const incidentModal = new bootstrap.Modal(document.getElementById("incidentModal"));

            async function cargarIncidencias() {
                try {
                    let url = "<?= $basePath ?>/api/get_incidents.php?all=1";
                    const response = await fetch(url);
                    const incidencias = await response.json();
                    markers.clearLayers();
                    incidencias.forEach((inc) => {
                        let customIcon;
                        if (inc.icono_url) {
                            customIcon = L.icon({
                                iconUrl: inc.icono_url,
                                iconSize: [32, 32],
                                iconAnchor: [16, 32],
                                popupAnchor: [0, -32],
                            });
                        }
                        const marker = L.marker([inc.latitud, inc.longitud], {
                            icon: customIcon,
                        });
                        marker.on("click", () => {
                            let fotoHtml = inc.foto ? `<img src='<?= $basePath ?>/${inc.foto}' class='img-fluid mb-2' style='max-height:180px;'>` : "";
                            let redesHtml = inc.redes ? `<a href='${inc.redes}' target='_blank'>Ver en redes sociales</a>` : "";
                            document.getElementById("incidentModalTitle").innerText = inc.titulo;
                            document.getElementById("incidentModalBody").innerHTML = `
                ${fotoHtml}
                <p><strong>Descripción:</strong> ${inc.descripcion}</p>
                <p><strong>Tipos:</strong> <span class=\"badge bg-primary\">${inc.tipos.replace(/,/g, '</span> <span class=\"badge bg-primary\">')}</span></p>
                <p><strong>Ubicación:</strong> ${inc.barrio}, ${inc.municipio}, ${inc.provincia}</p>
                <p><strong>Fecha:</strong> ${inc.fecha_ocurrencia}</p>
                <hr>
                <p><strong>Víctimas:</strong> ${inc.muertos} muertos, ${inc.heridos} heridos.</p>
                <p><strong>Pérdida estimada:</strong> RD$ ${Number(inc.perdida_estimada).toLocaleString()}</p>
                ${redesHtml}
              `;
                            incidentModal.show();
                        });
                        markers.addLayer(marker);
                    });
                    map.addLayer(markers);
                } catch (error) {
                    console.error("Error al cargar incidencias:", error);
                }
            }

            cargarIncidencias();
            setInterval(cargarIncidencias, 30000); // Actualizar cada 30 segundos
        });
    </script>
</body>

</html>