<?php
// 1. Cabeceras y Errores 
require_once '../../../../config/cors.php';
header("Content-Type: application/json; charset=UTF-8");

require_once '../../../../config/db.php';

require_once '../../../../src/Models/GestionVisitas.php'; 

$database = new Database();
$db = $database->getConnection();
$gestion = new GestionVisitas($db);

// 2. Capturamos los datos que envía Angular
$data = json_decode(file_get_contents("php://input"));

// Extraemos los IDs
$id_jornada = $data->jId ?? null; 
$id_cliente = $data->cId ?? null;

if ($id_jornada && $id_cliente) {

    // 3. ACTUALIZACIÓN DE LA SALIDA
    // Registramos la hora de fin y calculamos los minutos de duración
    // TIMESTAMPDIFF calcula la diferencia entre la llegada (ya guardada) y el "ahora"
    $update = "UPDATE visitas 
               SET salida = NOW(), 
                   duracion_minutos = TIMESTAMPDIFF(MINUTE, llegada, NOW()) 
               WHERE id_jornada = :jId AND id_cliente = :pId AND llegada IS NOT NULL";
    
    $stmt = $db->prepare($update);
    $stmt->bindParam(':jId', $id_jornada);
    $stmt->bindParam(':pId', $id_cliente);

    if ($stmt->execute()) {
        // Comprobamos si realmente se actualizó alguna fila
        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode([
                "status" => "success", 
                "message" => "Visita finalizada con éxito."
            ]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "No se pudo finalizar. Asegúrate de haber fichado la entrada primero."]);
        }
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error interno al cerrar la visita."]);
    }

} else {
    http_response_code(400);
    echo json_encode(["error" => "Faltan los identificadores de jornada o cliente."]);
}