<?php
require_once '../../../config/cors.php';
header("Content-Type: application/json; charset=UTF-8");
require_once '../../../config/db.php';

$data = json_decode(file_get_contents("php://input"));
$id_jornada = $data->id ?? null;

if ($id_jornada) {
    $db->prepare("INSERT INTO pausas (id_jornada, hora_inicio) VALUES (?, NOW())")->execute([$id_jornada]);
    $db->prepare("UPDATE jornadas SET pausa_activa = 1 WHERE id_jornada = ?")->execute([$id_jornada]);
    echo json_encode(["status" => "success"]);
}

// Insertamos un nuevo registro en la tabla de pausas
$query = "INSERT INTO pausas (id_jornada, hora_inicio) VALUES (:id, NOW())";
// Y marcamos la jornada con pausa_activa = 1
$query_jornada = "UPDATE jornadas SET pausa_activa = 1 WHERE id_jornada = :id";
// ... (ejecutar ambos) ...
echo json_encode(["status" => "success"]);