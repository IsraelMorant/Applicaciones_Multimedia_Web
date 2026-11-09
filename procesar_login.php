<?php
// Iniciar el sistema de sesiones de PHP
session_start();
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password_ingresada = $_POST['password'];

    // Buscar al usuario por su correo
    $sql = "SELECT id, nombre, password, rol FROM usuarios WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);
    
    $usuario = $stmt->fetch();

    // Validar si el usuario existe y la contraseña es correcta
    if ($usuario && password_verify($password_ingresada, $usuario['password'])) {
        // Generar las variables de sesión
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['rol'] = $usuario['rol'];

        // Redireccionar dependiendo del rol
        if ($usuario['rol'] == 'admin') {
            header("Location: panel_admin.php");
        } else {
            header("Location: panel_cliente.php");
        }
        exit;
    } else {
        // Credenciales incorrectas
        header("Location: login.php?error=credenciales");
        exit;
    }
}
?>