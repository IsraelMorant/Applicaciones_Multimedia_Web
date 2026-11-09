<?php
session_start();
require 'conexion.php';

// Seguridad: verificar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nuevo_nombre = trim($_POST['nombre']);
    $nueva_password = $_POST['nueva_password'];
    $usuario_id = $_SESSION['usuario_id'];

    try {
        // Si el campo de contraseña está vacío, solo actualizamos el nombre
        if (empty($nueva_password)) {
            $sql = "UPDATE usuarios SET nombre = :nombre WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nombre' => $nuevo_nombre,
                ':id' => $usuario_id
            ]);
        } else {
            // Si hay texto, encriptamos la nueva contraseña y actualizamos ambas cosas
            $password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET nombre = :nombre, password = :password WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nombre' => $nuevo_nombre,
                ':password' => $password_hash,
                ':id' => $usuario_id
            ]);
        }

        // Actualizamos la variable de sesión para que el nombre nuevo aparezca de inmediato en la barra superior
        $_SESSION['nombre'] = $nuevo_nombre;

        // Redirigir de vuelta al perfil
        header("Location: perfil.php?actualizado=exito");
        exit;

    } catch (PDOException $e) {
        // En caso de error
        header("Location: perfil.php?error=db");
        exit;
    }
}
?>