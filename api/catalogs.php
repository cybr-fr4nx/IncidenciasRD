<?php
header('Content-Type: application/json');
require '../includes/db.php';

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? 0;

$response = [];

try {
    if ($action === 'provincias') {
        $stmt = $pdo->query("SELECT * FROM Provincia ORDER BY nombre");
        $response = $stmt->fetchAll();
    } elseif ($action === 'municipios' && $id > 0) {
        $stmt = $pdo->prepare("SELECT * FROM Municipio WHERE id_provincia = ? ORDER BY nombre");
        $stmt->execute([$id]);
        $response = $stmt->fetchAll();
    } elseif ($action === 'barrios' && $id > 0) {
        $stmt = $pdo->prepare("SELECT * FROM Barrio WHERE id_municipio = ? ORDER BY nombre");
        $stmt->execute([$id]);
        $response = $stmt->fetchAll();
    }
} catch (PDOException $e) {
    http_response_code(500);
    $response = ['error' => $e->getMessage()];
}

echo json_encode($response);
