<?php
require_once '../includes/session.php';
require_once '../includes/db.php';

// Solo validadores/Admin pueden gestionar incidencias
if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ['validador', 'admin'])) {
    http_response_code(403);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

header('Content-Type: application/json');

// Listar incidencias (GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("SELECT i.*, p.nombre as provincia, m.nombre as municipio, b.nombre as barrio, u.nombre as reportero FROM Incidencia i JOIN Provincia p ON i.id_provincia=p.id_provincia JOIN Municipio m ON i.id_municipio=m.id_municipio JOIN Barrio b ON i.id_barrio=b.id_barrio JOIN Usuario u ON i.id_usuario=u.id_usuario ORDER BY i.fecha_registro DESC");
    $incidencias = $stmt->fetchAll();
    echo json_encode($incidencias);
    exit;
}

// Editar incidencia (POST con action=edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit') {
    $id = $_POST['id_incidencia'] ?? null;
    $titulo = $_POST['titulo'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $estado = $_POST['estado'] ?? '';
    $muertos = $_POST['muertos'] ?? 0;
    $heridos = $_POST['heridos'] ?? 0;
    $perdida = $_POST['perdida_estimada'] ?? 0;
    $redes = $_POST['link_redes'] ?? '';
    if ($id && $titulo && $descripcion && in_array($estado, ['pendiente', 'validada', 'rechazada', 'fusionada'])) {
        $stmt = $pdo->prepare("UPDATE Incidencia SET titulo=?, descripcion=?, estado=?, muertos=?, heridos=?, perdida_estimada=?, link_redes=? WHERE id_incidencia=?");
        $stmt->execute([$titulo, $descripcion, $estado, $muertos, $heridos, $perdida, $redes, $id]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Datos inválidos']);
    }
    exit;
}

// Eliminar incidencia (POST con action=delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = $_POST['id_incidencia'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM Incidencia WHERE id_incidencia=?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'ID inválido']);
    }
    exit;
}

echo json_encode(['error' => 'Acción no válida']);