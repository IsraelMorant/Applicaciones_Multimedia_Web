<?php
session_start(); // Retomar la sesión actual
session_unset(); // Limpiar todas las variables
session_destroy(); // Destruir la sesión por completo

// Redirigir al inicio
header("Location: index.php");
exit;
?>