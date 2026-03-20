<?php
require_once '../config/cors.php';
header("Content-Type: application/json");
require_once '../config/db.php';
require_once '../src/Models/GestionVisitas.php';

$database = new Database();
$db = $database->getConnection();
$gestion = new GestionVisitas($db);

// Supongamos que recibimos el ID por la URL: obtener_ruta.php?id=1
$id_usuario = isset($_GET['id']) ? $_GET['id'] : null;

if ($id_usuario) {
    $ruta = $gestion->obtenerHojaDeRuta($id_usuario);
    echo json_encode([
        "status" => "success",
        "total_visitas" => count($ruta),
        "hoja_de_ruta" => $ruta
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "ID de usuario no proporcionado"]);
}