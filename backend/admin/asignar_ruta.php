<?php
require_once '../config/cors.php';
header("Content-Type: application/json");
require_once '../config/db.php';
require_once '../src/Models/GestionVisitas.php';

$database = new Database();
$db = $database->getConnection();
$gestion = new GestionVisitas($db);

$data = json_decode(file_get_contents("php://input"));

// Validamos que vengan todos los campos necesarios
if (!empty($data->id_usuario) && !empty($data->id_paciente) && !empty($data->fecha)) {
    
    // El orden por defecto será 1 si no se envía
    $orden = isset($data->orden) ? $data->orden : 1;

    $resultado = $gestion->asignarVisita(
        $data->id_usuario, 
        $data->id_paciente, 
        $data->fecha, 
        $orden
    );

    if ($resultado['status'] === 'success') {
        http_response_code(201);
    } else {
        http_response_code(400);
    }
    echo json_encode($resultado);

} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Datos incompletos para la asignación."]);
}