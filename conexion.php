<?php
// 1. Cargar variables del .env si estamos en entorno local
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $variables = parse_ini_file($envFile);
    foreach ($variables as $key => $value) {
        putenv("$key=$value"); // Guarda en el entorno
        $_ENV[$key] = $value;  // Guarda en el arreglo superglobal
    }
}

// 2. Leer las variables (funcionará localmente y en Vercel)
$host = getenv('DB_HOST') ?: $_ENV['DB_HOST'];
$port = getenv('DB_PORT') ?: $_ENV['DB_PORT'];
$dbname = getenv('DB_NAME') ?: $_ENV['DB_NAME'];
$user = getenv('DB_USER') ?: $_ENV['DB_USER'];
$password = getenv('DB_PASSWORD') ?: $_ENV['DB_PASSWORD'];

// 3. Crear el DSN para PostgreSQL
$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

try {
    $pdo = new PDO($dsn, $user, $password);
    
    // Configuración de seguridad y manejo de errores de PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
} catch (PDOException $e) {
    // Evitamos imprimir $e->getMessage() en producción para no revelar datos de la BD
   // error_log("Error de conexión: " . $e->getMessage());
  //  die("Error interno del servidor. Por favor, intenta más tarde.");

die("Error real de BD: " . $e->getMessage());
}
?>