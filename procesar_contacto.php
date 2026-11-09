<?php
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $mensaje = trim($_POST['mensaje']);

    try {
        $sql = "INSERT INTO mensajes (nombre, email, mensaje) VALUES (:nombre, :email, :mensaje)";
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':mensaje' => $mensaje
        ]);

        // Redirige de vuelta a la página de contacto con un mensaje de éxito
        header("Location: contacto.php?envio=exito");
        exit;
    } catch (PDOException $e) {
        header("Location: contacto.php?error=db");
        exit;
    }
}
?>