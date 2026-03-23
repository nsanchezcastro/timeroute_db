<?php
// 1. Cabeceras y Errores 
require_once '../../../config/cors.php';
header("Content-Type: application/json; charset=UTF-8");

require_once '../../../config/db.php';

$database = new Database();
$db = $database->getConnection();

// 2. Capturamos los datos que envía Angular

$data = json_decode(file_get_contents("php://input"));


$id_jornada = $data->id ?? $data->jId ?? null;

if ($id_jornada) {

    // 3. ACTUALIZACIÓN FINAL
    // Cambiamos el estado a 'FINALIZADA' y registramos la hora de fin real
    $query = "UPDATE jornadas 
              SET estado = 'FINALIZADA', 
                  fin_real = NOW() 
              WHERE id_jornada = :id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id_jornada);

    if ($stmt->execute()) {
     
        
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "message" => "Jornada finalizada correctamente. ¡Buen trabajo!"
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo cerrar la jornada en la base de datos."]);
    }

} else {
    http_response_code(400);
    echo json_encode(["error" => "ID de jornada no recibido o inválido."]);
}