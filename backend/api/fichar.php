<?php
header("Content-Type: application/json");
require_once '../config/db.php';
require_once '../src/Models/GestionVisitas.php';

$database = new Database();
$db = $database->getConnection();
$gestion = new GestionVisitas($db);

// Recibimos los datos del JSON (Angular enviará esto)
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->id_asignacion) && isset($data->latitud) && isset($data->longitud)) {
    
    // Llamamos al método del Modelo
    $resultado = $gestion->iniciarVisita(
        $data->id_asignacion, 
        $data->latitud, 
        $data->longitud
    );

    if ($resultado['status'] === 'success') {
        http_response_code(201);
    } else {
        http_response_code(400); // Error de validación (ej: lejos del paciente)
    }
    echo json_encode($resultado);

} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Datos de fichaje incompletos (ID, Lat o Lng)"]);
}