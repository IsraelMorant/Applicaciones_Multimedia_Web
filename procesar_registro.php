<?php
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Encriptar la contraseña antes de guardarla
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Preparamos la consulta para evitar Inyección SQL
        $sql = "INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :password)";
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':password' => $password_hash
        ]);

        // Redirigir al login con un mensaje de éxito
        header("Location: login.php?registro=exitoso");
        exit;
    } catch (PDOException $e) {
        // Si el correo ya existe, PDO arrojará un error por la restricción UNIQUE
        header("Location: registro.php?error=email_existente");
        exit;
    }
}
?>