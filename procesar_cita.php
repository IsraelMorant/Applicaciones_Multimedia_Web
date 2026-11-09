<?php
session_start();
require 'conexion.php';

// Validar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_SESSION['usuario_id'];
    $servicio_id = $_POST['servicio_id'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $notas = $_POST['notas'];

    try {
        $sql = "INSERT INTO citas (usuario_id, servicio_id, fecha_cita, hora_cita, notas_cliente) 
                VALUES (:usuario_id, :servicio_id, :fecha, :hora, :notas)";
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':servicio_id' => $servicio_id,
            ':fecha' => $fecha,
            ':hora' => $hora,
            ':notas' => $notas
        ]);

        header("Location: panel_cliente.php?reserva=exitosa");
        exit;
    } catch (PDOException $e) {
        // En producción puedes cambiar esto por un redireccionamiento de error
        die("Error al guardar la cita: " . $e->getMessage());
    }
}
?>