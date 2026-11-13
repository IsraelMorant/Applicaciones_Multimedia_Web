<?php
session_start();
require 'conexion.php';

// Validar que el usuario sea un cliente logueado
if (!isset($_SESSION['usuario_id']) || (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin')) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cita_id'])) {
    $cita_id = $_POST['cita_id'];
    $usuario_id = $_SESSION['usuario_id'];

    try {
        // Actualizamos el estado asegurándonos de que la cita pertenezca a este cliente
        $sql = "UPDATE citas SET estado = 'finalizado' WHERE id = :cita_id AND usuario_id = :usuario_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':cita_id' => $cita_id,
            ':usuario_id' => $usuario_id
        ]);
        
        header("Location: panel_cliente.php?estado=actualizado");
        exit;
    } catch (PDOException $e) {
        header("Location: panel_cliente.php?error=db");
        exit;
    }
} else {
    header("Location: panel_cliente.php");
    exit;
}
?>