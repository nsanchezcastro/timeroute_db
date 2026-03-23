<?php
require_once '../../../config/cors.php';
header("Content-Type: application/json; charset=UTF-8");
require_once '../../../config/db.php';
// Buscamos la pausa abierta (sin hora_fin) y la cerramos
$query = "UPDATE pausas SET hora_fin = NOW(), 
          minutos = TIMESTAMPDIFF(MINUTE, hora_inicio, NOW()) 
          WHERE id_jornada = :id AND hora_fin IS NULL";
// Marcamos pausa_activa = 0 en la jornada
$query_jornada = "UPDATE jornadas SET pausa_activa = 0 WHERE id_jornada = :id";
// ... (ejecutar) ...
echo json_encode(["status" => "success"]);