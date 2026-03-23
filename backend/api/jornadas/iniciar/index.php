<?php
require_once '../../../config/cors.php';
header("Content-Type: application/json; charset=UTF-8");
require_once '../../../config/db.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));
$id_jornada = $data->id ?? null; 

if ($id_jornada) {
    // Actualizamos el estado y grabamos la hora de inicio real
    $query = "UPDATE jornadas SET estado = 'EN_CURSO', inicio_real = NOW() WHERE id_jornada = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id_jornada);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(["status" => "success", "message" => "Jornada iniciada"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo iniciar la jornada"]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "ID de jornada no recibido"]);
}