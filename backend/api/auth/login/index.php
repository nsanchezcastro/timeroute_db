<?php
// 1. Configuración de cabeceras y errores
require_once '../../../config/cors.php';
header("Content-Type: application/json; charset=UTF-8");

require_once '../../../config/db.php';
require_once '../../../src/Models/Usuario.php';

$database = new Database();
$db = $database->getConnection();
$usuario = new Usuario($db);

// 2. Obtener los datos del frontend
$data = json_decode(file_get_contents("php://input"));

if(!empty($data->email) && !empty($data->password)){
    
    // Buscamos al usuario por email
    $stmt = $usuario->login($data->email);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // 3. Verificación de contraseña (Usamos password_verify por seguridad)
    if ($row && password_verify($data->password, $row['password'])) {
        
    
        $respuesta = [
            "token" => "tr_session_" . bin2hex(random_bytes(16)), // Token ficticio para que Angular no falle
            "user" => [
                "id" => (int)$row['id'], // Lo convertimos a "id" (sin _usuario)
                "nombre" => $row['nombre'],
                "email" => $row['email'],
                "rol" => ($row['rol'] === 'admin') ? 'admin' : 'trabajador' // Normalizamos el rol
            ]
        ];

        http_response_code(200);
        echo json_encode($respuesta);

    } else {
        // Si falla la contraseña o el usuario no existe
        http_response_code(401);
        echo json_encode(["error" => "Email o contraseña incorrectos"]);
    }

} else {
    // Datos incompletos en el POST
    http_response_code(400);
    echo json_encode(["error" => "Por favor, introduce email y contraseña"]);
}