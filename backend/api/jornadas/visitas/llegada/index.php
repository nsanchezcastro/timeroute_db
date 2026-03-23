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

// Extraemos los datos (Angular envía 'lat' y 'lng')
$id_jornada = $data->jId ?? null; 
$id_cliente = $data->cId ?? null;
$lat_movil = $data->lat ?? null;
$lng_movil = $data->lng ?? null;

if ($id_jornada && $id_cliente && $lat_movil && $lng_movil) {

    // 3. Obtenemos las coordenadas del PACIENTE (Cliente) de la base de datos
    // Suponiendo que tienes un método para obtener un cliente específico
    $query = "SELECT latitud, longitud FROM clientes WHERE id_cliente = :id LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id_cliente);
    $stmt->execute();
    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($paciente) {
        $lat_paciente = $paciente['latitud'];
        $lng_paciente = $paciente['longitud'];

        // 4. CÁLCULO DE DISTANCIA (Haversine)
        $radio_tierra = 6371000; // Radio en metros
        
        $dLat = deg2rad($lat_paciente - $lat_movil);
        $dLng = deg2rad($lng_paciente - $lng_movil);
        
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat_movil)) * cos(deg2rad($lat_paciente)) * sin($dLng/2) * sin($dLng/2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distancia = $radio_tierra * $c;

        // 5. VALIDACIÓN (Umbral de 200 metros)
        if ($distancia <= 200) {
            // Si está cerca, registramos la llegada en la tabla de asignaciones/visitas
            // Ajusta los nombres de tabla y campos a tu DB
            $update = "UPDATE asignaciones 
                       SET hora_llegada = NOW() 
                       WHERE id_jornada = :jId AND id_cliente = :pId";
            
            $stmtUpd = $db->prepare($update);
            $stmtUpd->bindParam(':jId', $id_jornada);
            $stmtUpd->bindParam(':pId', $id_cliente);

            if ($stmtUpd->execute()) {
                http_response_code(200);
                echo json_encode([
                    "status" => "success", 
                    "message" => "Llegada registrada correctamente. Distancia: " . round($distancia, 2) . "m"
                ]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error al actualizar la base de datos"]);
            }
        } else {
            // Si está muy lejos (Error 400 para que Angular lo muestre en rojo)
            http_response_code(400);
            echo json_encode([
                "error" => "Estás demasiado lejos del paciente (" . round($distancia, 2) . "m). Acércate más para fichar."
            ]);
        }
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Paciente no encontrado"]);
    }

} else {
    http_response_code(400);
    echo json_encode(["error" => "Datos de ubicación o IDs incompletos"]);
}