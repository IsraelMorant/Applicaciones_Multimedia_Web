<?php
// Obtener la URL que el usuario está solicitando
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Si entran a la raíz del sitio, cargamos tu landing page
if ($uri === '/' || $uri === '/index.php') {
    require 'inicio.php';
} 
// Si solicitan un archivo que sí existe (ej. /login.php o /procesar_cita.php), lo cargamos
elseif (file_exists(__DIR__ . $uri) && is_file(__DIR__ . $uri)) {
    require __DIR__ . $uri;
} 
// Si la ruta no existe, mostramos un error 404 limpio
else {
    http_response_code(404);
    echo "<h1 style='text-align: center; margin-top: 50px; font-family: sans-serif;'>404 - Página no encontrada</h1>";
}
?>