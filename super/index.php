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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    <style>
        #mapa-accidentes {
            height: 450px;
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

        <h3 class="mt-5">Mapa de Accidentes Actuales</h3>
        <div id="mapa" class="rounded shadow mb-4" style="height: 450px;"></div>

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
    </main>
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
                    let url = "<?= $basePath ?>/api/get_incidents.php?estado=validada";
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