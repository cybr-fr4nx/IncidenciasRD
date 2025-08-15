<?php
require_once '../includes/db.php';
header('Content-Type: application/json');
$lat = isset($_GET['lat']) ? floatval($_GET['lat']) : null;
$lng = isset($_GET['lng']) ? floatval($_GET['lng']) : null;
if (!$lat || !$lng) {
    echo json_encode(['error' => 'Parámetros inválidos']);
    exit;
}
// Buscar el barrio más cercano (usando distancia euclidiana simple)
$stmt = $pdo->prepare("SELECT b.id_barrio, b.nombre AS barrio, m.id_municipio, m.nombre AS municipio, p.id_provincia, p.nombre AS provincia
FROM Barrio b
JOIN Municipio m ON b.id_municipio = m.id_municipio
JOIN Provincia p ON m.id_provincia = p.id_provincia
WHERE b.latitud IS NOT NULL AND b.longitud IS NOT NULL
ORDER BY POW(b.latitud-?,2) + POW(b.longitud-?,2) ASC LIMIT 1");
$stmt->execute([$lat, $lng]);
$row = $stmt->fetch();
if ($row) {
    echo json_encode($row);
} else {
    echo json_encode(['error' => 'No se encontró barrio cercano']);
}
