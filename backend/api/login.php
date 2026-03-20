<?php
require_once '../config/cors.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

require_once '../config/db.php';
require_once '../src/Models/Usuario.php';

$database = new Database();
$db = $database->getConnection();
$usuario = new Usuario($db);

// Obtener los datos que envía el frontend 
$data = json_decode(file_get_contents("php://input"));

if(!empty($data->email) && !empty($data->password)){
    $stmt = $usuario->login($data->email);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if($row && $data->password == $row['password']){
        http_response_code(200);
        echo json_encode([
            "message" => "Acceso concedido",
            "usuario" => [
                "id" => $row['id_usuario'],
                "nombre" => $row['nombre'],
                "rol" => $row['rol']
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode(["message" => "Email o contraseña incorrectos"]);
    }


if ($row) {
  
    if (password_verify($data->password, $row['password'])) {
        
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "message" => "Acceso concedido",
            "usuario" => [
                "id" => $row['id_usuario'],
                "nombre" => $row['nombre'],
                "rol" => $row['rol']
            ]
        ]);

    } else {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Contraseña incorrecta"]);
    }
} else {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "El usuario no existe"]);
}
} else {
    http_response_code(400);
    echo json_encode(["message" => "Datos incompletos"]);
}