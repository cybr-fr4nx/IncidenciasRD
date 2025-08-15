<?php
header('Content-Type: application/json');
require '../includes/db.php';

$query = "
    SELECT 
        i.id_incidencia, 
        i.titulo, 
        i.descripcion, 
        i.latitud, 
        i.longitud,
        i.muertos, 
        i.heridos, 
        i.perdida_estimada AS perdida, 
        i.link_redes AS redes, 
        i.foto_url AS foto, 
        i.fecha_ocurrencia,
        p.nombre AS provincia, 
        m.nombre AS municipio, 
        b.nombre AS barrio,
        (
            SELECT GROUP_CONCAT(ti.nombre SEPARATOR ', ') 
            FROM Incidencia_Tipo it
            JOIN Tipo_Incidencia ti ON it.id_tipo = ti.id_tipo
            WHERE it.id_incidencia = i.id_incidencia
        ) AS tipos,
        (
            SELECT ti.icono_url
            FROM Incidencia_Tipo it
            JOIN Tipo_Incidencia ti ON it.id_tipo = ti.id_tipo
            WHERE it.id_incidencia = i.id_incidencia
            LIMIT 1
        ) AS icono_url
    FROM Incidencia i
    JOIN Provincia p ON i.id_provincia = p.id_provincia
    JOIN Municipio m ON i.id_municipio = m.id_municipio
    JOIN Barrio b ON i.id_barrio = b.id_barrio
    WHERE i.estado = 'validada' 
      AND i.fecha_ocurrencia >= DATE_SUB(NOW(), INTERVAL 1 DAY)
";

$params = [];
if (!empty($_GET['provincia'])) {
    $query .= " AND i.id_provincia = ?";
    $params[] = $_GET['provincia'];
}
if (!empty($_GET['tipo'])) {
    $query .= " AND i.id_incidencia IN (
                    SELECT id_incidencia 
                    FROM Incidencia_Tipo 
                    WHERE id_tipo = ?
                )";
    $params[] = $_GET['tipo'];
}
if (!empty($_GET['fecha_inicio']) && !empty($_GET['fecha_fin'])) {
    $query .= " AND i.fecha_ocurrencia BETWEEN ? AND ?";
    $params[] = $_GET['fecha_inicio'];
    $params[] = $_GET['fecha_fin'];
}
if (!empty($_GET['titulo'])) {
    $query .= " AND i.titulo LIKE ?";
    $params[] = '%' . $_GET['titulo'] . '%';
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$incidents = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($incidents);
