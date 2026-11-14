<?php
session_start();
require 'conexion.php';

// Validar que sea admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cita_id'])) {
    $cita_id = $_POST['cita_id'];

    // 1. Obtener a qué usuario le pertenece esta cita para guardar la relación correcta
    $stmt_cita = $pdo->prepare("SELECT usuario_id FROM citas WHERE id = :cita_id");
    $stmt_cita->execute([':cita_id' => $cita_id]);
    $cita = $stmt_cita->fetch();
    
    if (!$cita) {
        header("Location: panel_admin.php?error=cita_invalida");
        exit;
    }
    
    $usuario_id = $cita['usuario_id'];

    // 2. Procesar y guardar la imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $imagen_tmp = $_FILES['imagen']['tmp_name'];
        $tipo_mime = mime_content_type($imagen_tmp);
        
        $datos_imagen = file_get_contents($imagen_tmp);
        $base64 = base64_encode($datos_imagen);
        $url_imagen = 'data:' . $tipo_mime . ';base64,' . $base64;

        try {
            $sql = "INSERT INTO galerias_clientes (usuario_id, cita_id, url_imagen) VALUES (:usuario_id, :cita_id, :url_imagen)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':usuario_id' => $usuario_id,
                ':cita_id' => $cita_id,
                ':url_imagen' => $url_imagen
            ]);

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
} else {
    header("Location: panel_admin.php");
    exit;
}
?>