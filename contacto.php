<?php
session_start();
require 'conexion.php';

// Variables por defecto 
$nombre_cliente = "";
$email_cliente = "";
$es_cliente = false;

// Si hay un usuario logueado y NO es administrador, obtenemos sus datos
if (isset($_SESSION['usuario_id']) && (!isset($_SESSION['rol']) || $_SESSION['rol'] === 'cliente')) {
    $es_cliente = true;
    $stmt = $pdo->prepare("SELECT nombre, email FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $_SESSION['usuario_id']]);
    $user_data = $stmt->fetch();
    
    if ($user_data) {
        $nombre_cliente = $user_data['nombre'];
        $email_cliente = $user_data['email'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto - GES Estudio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Barra de Navegación Dinámica -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="inicio.php">GES EStudio</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="inicio.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="servicios.php">Servicios</a></li>
                    <li class="nav-item"><a class="nav-link" href="portafolio.php">Portafolio</a></li>
                    <li class="nav-item"><a class="nav-link active" href="contacto.php">Contacto</a></li>
                    
                    <?php if(isset($_SESSION['usuario_id'])): ?>
                        <?php if(isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                            <li class="nav-item"><a class="nav-link" href="panel_admin.php">Panel Admin</a></li>
                        <?php else: ?>
                            <li class="nav-item"><a class="nav-link" href="panel_cliente.php">Mi Panel</a></li>
                        <?php endif; ?>
                        
                        <li class="nav-item"><a class="nav-link" href="perfil.php">Mi Cuenta</a></li>
                        <li class="nav-item ms-2 align-self-center"><a class="btn btn-outline-danger btn-sm" href="logout.php">Salir</a></li>
                    <?php else: ?>
                        <li class="nav-item ms-2 align-self-center"><a class="btn btn-outline-light" href="login.php">Acceder</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container my-5" style="min-height: 70vh;">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2 class="text-center mb-4">¿Tienes alguna duda?</h2>

                <?php if(isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                    <!-- Vista para el Administrador -->
                    <div class="alert alert-info text-center shadow-sm p-5">
                        <h4 class="alert-heading">Modo Administrador Activo</h4>
                        <p>Como administrador, el formulario de contacto está deshabilitado para ti. No es posible enviarte mensajes a ti mismo.</p>
                        <hr>
                        <p class="mb-0">Para leer los mensajes que han enviado tus clientes, dirígete a tu panel de administración.</p>
                        <a href="panel_admin.php" class="btn btn-primary mt-3">Ir al Panel Admin</a>
                    </div>
                <?php else: ?>
                    <!-- Vista para Clientes y Visitantes -->
                    <form action="procesar_contacto.php" method="POST" class="card p-4 shadow-sm">
                        <div class="mb-3">
                            <label class="form-label">Nombre completo</label>
                            <!-- Si es cliente, inyectamos el valor y ponemos readonly bg-light para que parezca bloqueado -->
                            <input type="text" name="nombre" class="form-control" 
                                   value="<?php echo htmlspecialchars($nombre_cliente); ?>" 
                                   <?php echo $es_cliente ? 'readonly class="form-control bg-light"' : 'required'; ?>>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" name="email" class="form-control" 
                                   value="<?php echo htmlspecialchars($email_cliente); ?>" 
                                   <?php echo $es_cliente ? 'readonly class="form-control bg-light"' : 'required'; ?>>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mensaje</label>
                            <textarea name="mensaje" class="form-control" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Enviar Mensaje</button>
                    </form>
                <?php endif; ?>

            </div>
        </div>
    </main>

    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2026 GES EStudio Fotográfico. Todos los derechos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>