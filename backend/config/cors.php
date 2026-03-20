<?php
// 1. Permitir que cualquier aplicación acceda a esta API
// En producción, podrías cambiar el "*" por "http://localhost:4200"
header("Access-Control-Allow-Origin: *");

// 2. Indicar qué métodos HTTP están permitidos
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

// 3. Permitir cabeceras personalizadas (como Content-Type o Authorization)
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// 4. Responder a la petición "Pre-flight" (OPTIONS)
// Los navegadores envían un OPTIONS antes de un POST para ver si el servidor es seguro.
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    // Si es una petición OPTIONS, respondemos con un 200 OK y salimos
    http_response_code(200);
    exit;
}