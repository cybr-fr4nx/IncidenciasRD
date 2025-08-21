<?php
$host = 'localhost'; 
$db   = 'incidencias2_rd';
$user = 'root'; 
$pass = ''; 
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    // Lanza excepciones en caso de error
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    // Devuelve los resultados como arrays asociativos
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // Desactiva la emulacion de prepared statements para mayor seguridad
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     
     http_response_code(500);
     echo json_encode(['error' => 'Error de conexión a la base de datos.']);
     // throw new \PDOException($e->getMessage(), (int)$e->getCode());
     exit();
}
?>


