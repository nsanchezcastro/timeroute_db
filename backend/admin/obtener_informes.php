<?php
// Subimos dos niveles para salir de 'admin' y entrar en 'config'
require_once '../../config/cors.php'; 
header("Content-Type: application/json; charset=UTF-8");

require_once '../../config/db.php';
require_once '../../src/Models/Informe.php';

$database = new Database();
$db = $database->getConnection();

$informe = new Informe($db);

// Capturamos la fecha de la URL, por defecto hoy
$fecha = $_GET['fecha'] ?? date('Y-m-d');

$resultado = $informe->obtenerHistorialDiario($fecha);

http_response_code($resultado['status'] === 'success' ? 200 : 500);
echo json_encode($resultado, JSON_PRETTY_PRINT);