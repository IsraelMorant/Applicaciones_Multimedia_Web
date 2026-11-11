<?php
session_start();
require 'conexion.php';

// Validar que el usuario haya iniciado sesión y no sea admin
if (!isset($_SESSION['usuario_id']) || (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin')) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Paso 1: Restablecer todas las fotos del cliente a "no seleccionadas" (FALSE)
    // Esto es necesario por si el cliente desmarcó una foto que antes había seleccionado
    $sql_reset = "UPDATE galerias_clientes SET seleccionada = FALSE WHERE usuario_id = :usuario_id";
    $stmt_reset = $pdo->prepare($sql_reset);
    $stmt_reset->execute([':usuario_id' => $usuario_id]);

    // Paso 2: Marcar como TRUE solo las fotos que el cliente envió en el formulario
    if (!empty($_POST['fotos_seleccionadas'])) {
        $ids_seleccionados = $_POST['fotos_seleccionadas'];
        
        // Crear un array de placeholders (?, ?, ?) basado en cuántas fotos eligió
        $placeholders = str_repeat('?,', count($ids_seleccionados) - 1) . '?';
        
        // Preparar la consulta asegurando que solo actualiza las fotos del cliente actual
        $sql_update = "UPDATE galerias_clientes SET seleccionada = TRUE WHERE id IN ($placeholders) AND usuario_id = ?";
        
        $stmt_update = $pdo->prepare($sql_update);
        
        // Combinar los IDs de las fotos con el ID del usuario al final del arreglo
        $params = $ids_seleccionados;
        $params[] = $usuario_id;
        
        $stmt_update->execute($params);
    }

    // Redirigir de vuelta al panel con mensaje de éxito
    header("Location: panel_cliente.php?exito=1");
    exit;
} else {
    header("Location: panel_cliente.php");
    exit;
}
?>