<?php
require_once '../../config/cors.php';
header("Content-Type: application/json; charset=UTF-8");

require_once '../../config/db.php';
require_once '../../src/Models/GestionVisitas.php'; 

$database = new Database();
$db = $database->getConnection();
$gestion = new GestionVisitas($db);

// IMPORTANTE: Cambiamos 'id_usuario' por 'id' si ellos lo mandan así en el login
$id_usuario = $_GET['id_usuario'] ?? 1; 

$datos_db = $gestion->obtenerHojaDeRuta($id_usuario); 

if ($datos_db) {
    
    $respuesta = [
        "id" => (int)$datos_db['id_jornada'],
        "fecha" => date('Y-m-d'),
        "estado" => strtoupper($datos_db['estado']), 
        "nombre_ruta" => $datos_db['nombre_ruta'],
        "inicio_plan" => $datos_db['fecha'] . " 08:00:00",
        "fin_plan" => $datos_db['fecha'] . " 15:00:00",
        // Ajustamos estos dos según la nueva lógica de pausas
        "pausa_activa" => (bool)($datos_db['pausa_activa'] ?? false),
        "total_min_descanso" => (int)($datos_db['total_min_descanso'] ?? 0),
        "visitas" => [] 
    ];

    foreach ($datos_db['paradas'] as $parada) {
        $respuesta['visitas'][] = [
            // CAMBIO CLAVE: id_paciente -> id_cliente
            "id_cliente" => (int)$parada['id_cliente'], 
            "nombre" => $parada['nombre_cliente'], // Asegúrate que tu modelo use 'nombre_cliente'
            "direccion" => $parada['direccion'],
            "orden" => (int)$parada['orden'],
            // CAMBIO CLAVE: latitud/longitud -> lat/lng (Angular lo espera corto)
            "lat" => (float)$parada['lat'], 
            "lng" => (float)$parada['lng'], 
            "llegada" => $parada['llegada'] ?? null, 
            "salida" => $parada['salida'] ?? null,
            "minutos_visita" => $parada['duracion_minutos'] ?? 0
        ];
    }

    http_response_code(200);
    echo json_encode($respuesta);

} else {
    http_response_code(404);
    echo json_encode(["message" => "No tienes jornada asignada para hoy"]);
}