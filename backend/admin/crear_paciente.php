<?php
require_once '../config/cors.php';
header("Content-Type: application/json");
require_once '../config/db.php';
require_once '../src/Models/GestionVisitas.php';

$database = new Database();
$db = $database->getConnection();
$gestion = new GestionVisitas($db);

// 1. Recibir datos del formulario de Angular
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->nombre) && !empty($data->direccion)) {
    // Aquí podrías comprobar el ROL del usuario que hace la petición
    // if ($user_role !== 'admin') { die(json_encode(["message" => "No autorizado"])); }

    $resultado = $gestion->crearPaciente($data->nombre, $data->direccion, $data->lat, $data->lng);

    if ($resultado) {
        http_response_code(201);
        echo json_encode(["status" => "success", "message" => "Paciente creado correctamente"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Error al guardar en la BD"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Datos incompletos"]);
}