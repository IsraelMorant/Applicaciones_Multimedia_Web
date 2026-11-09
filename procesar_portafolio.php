<?php
session_start();
require 'conexion.php';

// Doble validación de seguridad antes de procesar datos
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = trim($_POST['titulo']);
    $categoria = trim($_POST['categoria']);
    $url_imagen = trim($_POST['url_imagen']);

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
}
?>