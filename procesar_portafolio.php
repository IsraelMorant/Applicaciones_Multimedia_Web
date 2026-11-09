<?php
session_start();
require 'conexion.php';

// Validar que sea admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = trim($_POST['titulo']);
    $categoria = trim($_POST['categoria']);

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
            $sql = "INSERT INTO portafolio (titulo, categoria, url_imagen) VALUES (:titulo, :categoria, :url_imagen)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':titulo' => $titulo,
                ':categoria' => $categoria,
                ':url_imagen' => $url_imagen
            ]);

            header("Location: panel_admin.php?foto=guardada");
            exit;
        } catch (PDOException $e) {
            header("Location: panel_admin.php?error=db");
            exit;
        }
    } else {
        // Redirigir si hubo un error al cargar el archivo
        header("Location: panel_admin.php?error=archivo");
        exit;
    }
}
?>