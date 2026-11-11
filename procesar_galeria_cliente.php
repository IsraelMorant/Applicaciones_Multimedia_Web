<?php
session_start();
require 'conexion.php';

// Validar que sea admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_POST['usuario_id'];

    // Verificar si se subió un archivo sin errores
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        
        $imagen_tmp = $_FILES['imagen']['tmp_name'];
        $tipo_mime = mime_content_type($imagen_tmp);
        
        // Convertir la imagen física a texto Base64
        $datos_imagen = file_get_contents($imagen_tmp);
        $base64 = base64_encode($datos_imagen);
        
        // Crear la sintaxis de URL de datos (Data URI)
        $url_imagen = 'data:' . $tipo_mime . ';base64,' . $base64;

        try {
            $sql = "INSERT INTO galerias_clientes (usuario_id, url_imagen) VALUES (:usuario_id, :url_imagen)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':usuario_id' => $usuario_id,
                ':url_imagen' => $url_imagen
            ]);

            // Redirigir con éxito
            header("Location: panel_admin.php?galeria=exito");
            exit;
        } catch (PDOException $e) {
            header("Location: panel_admin.php?error=db");
            exit;
        }
    } else {
        header("Location: panel_admin.php?error=archivo");
        exit;
    }
}
?>