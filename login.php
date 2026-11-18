<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GES Estudio</title>
    <!-- Bootstrap 5 CSS  -->
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
                    <li class="nav-item"><a class="nav-link" href="contacto.php">Contacto</a></li>
                    
  <?php if(isset($_SESSION['usuario_id'])): ?>
    <!-- Validación de Rol para el Panel -->
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
    <!-- Contenedor Principal  -->
    <main class="container my-5" style="min-height: 70vh;">
     <div class="row justify-content-center">
    <div class="col-md-5">
        <h2 class="text-center mb-4">Iniciar Sesión</h2>
        <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show text-center shadow-sm" role="alert">
        <strong>Error de acceso:</strong> El correo o la contraseña son incorrectos. Por favor, verifica tus datos.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
        <form action="procesar_login.php" method="POST" class="card p-4 shadow-sm">
            <div class="mb-3">
                <label for="email" class="form-label">Correo electrónico</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Entrar</button>
            <div class="text-center mt-3">
                <a href="registro.php" class="text-decoration-none">¿No tienes cuenta? Regístrate aquí</a>
            </div>
        </form>
    </div>
</div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2026 GES EStudio Fotográfico. Todos los derechos reservados.</p>
    </footer>

    <!-- Bootstrap JS (Necesario para que el menú funcione en teléfonos) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>