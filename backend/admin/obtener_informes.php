<?php
require_once '../config/cors.php';
header("Content-Type: application/json");
require_once '../config/db.php';
require_once '../src/Models/Informe.php';

$database = new Database();
$db = $database->getConnection();

$informe = new Informe($db);

// Recogemos la fecha que nos pase Angular por la URL (GET). Si no, usamos la de hoy.
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

$resultado = $informe->obtenerHistorialDiario($fecha);

if ($resultado['status'] === 'success') {
    http_response_code(200);
} else {
    http_response_code(500);
}

echo json_encode($resultado);
?>