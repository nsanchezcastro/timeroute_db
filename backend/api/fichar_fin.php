<?php
require_once '../config/cors.php';
header("Content-Type: application/json");
require_once '../config/db.php';
require_once '../src/Models/GestionVisitas.php';

$database = new Database();
$db = $database->getConnection();
$gestion = new GestionVisitas($db);

// Recibimos el ID de la asignación (visita actual)
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->id_asignacion)) {
    $resultado = $gestion->finalizarVisita($data->id_asignacion);
    
    if ($resultado['status'] === 'success') {
        http_response_code(200);
    } else {
        http_response_code(400);
    }
    echo json_encode($resultado);
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Falta el ID de asignación"]);
}