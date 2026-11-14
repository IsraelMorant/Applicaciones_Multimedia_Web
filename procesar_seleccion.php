<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario_id']) || (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin')) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cita_id'])) {
    $cita_id = $_POST['cita_id'];
    
    // 1. Restablecer fotos de ESTA cita en específico a FALSE
    $sql_reset = "UPDATE galerias_clientes SET seleccionada = FALSE WHERE usuario_id = :usuario_id AND cita_id = :cita_id";
    $stmt_reset = $pdo->prepare($sql_reset);
    $stmt_reset->execute([':usuario_id' => $usuario_id, ':cita_id' => $cita_id]);

    // 2. Marcar las fotos seleccionadas como TRUE
    if (!empty($_POST['fotos_seleccionadas'])) {
        $ids_seleccionados = $_POST['fotos_seleccionadas'];
        $placeholders = str_repeat('?,', count($ids_seleccionados) - 1) . '?';
        
        $sql_update = "UPDATE galerias_clientes SET seleccionada = TRUE WHERE id IN ($placeholders) AND usuario_id = ? AND cita_id = ?";
        $stmt_update = $pdo->prepare($sql_update);
        
        $params = $ids_seleccionados;
        $params[] = $usuario_id;
        $params[] = $cita_id;
        
        $stmt_update->execute($params);
    }

    // Redirigir de vuelta mostrando la misma galería
    header("Location: panel_cliente.php?ver_cita=" . $cita_id . "&exito=1#galeria-seccion");
    exit;
} else {
    header("Location: panel_cliente.php");
    exit;
}
?>