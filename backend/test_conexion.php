<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Rutas absolutas
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/src/Models/Usuario.php'; 

$database = new Database();
$db = $database->getConnection();

if($db) {
    echo "Conexión exitosa.<br>";
    
    // Instanciar Usuario
    if (class_exists('Usuario')) {
        $usuario = new Usuario($db);
        $stmt = $usuario->leer();
        $num = $stmt->rowCount();
        echo "Usuarios encontrados en la tabla: " . $num;
    } else {
        echo "Error: La clase Usuario no se ha cargado. Revisa la ruta en src/Models/Usuario.php";
    }
}
?>