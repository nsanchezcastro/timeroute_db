<?php
require_once '../../../config/cors.php';
header("Content-Type: application/json; charset=UTF-8");
require_once '../../../config/db.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));
$id_jornada = $data->id ?? null;
$texto = $data->incidencia ?? '';

if ($id_jornada) {
    // Guardamos el texto en el campo incidencia de la tabla jornadas
    $query = "UPDATE jornadas SET incidencia = :texto WHERE id_jornada = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id_jornada);
    $stmt->bindParam(':texto', $texto);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error al guardar"]);
    }
}