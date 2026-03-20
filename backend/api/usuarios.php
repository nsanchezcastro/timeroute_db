<?php
require_once '../config/cors.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../config/db.php';
require_once '../src/Models/Usuario.php';

$database = new Database();
$db = $database->getConnection();
$usuario = new Usuario($db);

$stmt = $usuario->leer();
$usuarios_arr = array();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    array_push($usuarios_arr, $row);
}

echo json_encode($usuarios_arr);