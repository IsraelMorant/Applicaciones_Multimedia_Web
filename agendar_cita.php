<?php
session_start();
require 'conexion.php';

// 1. Verificar que el usuario haya iniciado sesión (si no, al login)
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// 2. CANDADO DE ROL: Si el usuario es admin, bloquear acceso y regresarlo a su panel
if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') {
    header("Location: panel_admin.php?error=admin_reserva");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GES Estudio</title>
    <!-- Bootstrap 5 CSS (Cumple con CSS y Diseño Responsivo) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tu archivo CSS personalizado para detalles específicos -->
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
    <!-- Quitamos 'nav-link' del botón de Salir -->
    <li class="nav-item ms-2 align-self-center"><a class="btn btn-outline-danger btn-sm" href="logout.php">Salir</a></li>
<?php else: ?>
    <!-- Quitamos 'nav-link' del botón de Acceder -->
    <li class="nav-item ms-2 align-self-center"><a class="btn btn-outline-light" href="login.php">Acceder</a></li>
<?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Contenedor Principal (Aquí cambiará el contenido en cada una de las 10 páginas) -->
    <main class="container my-5" style="min-height: 70vh;">
<div class="row justify-content-center">
    <div class="col-md-8">
        <h2 class="mb-4">Reservar Nueva Sesión</h2>
        <form action="procesar_cita.php" method="POST" class="card p-4 shadow-sm">
            <div class="mb-3">
                <label for="servicio" class="form-label">Selecciona el Servicio</label>
                <select class="form-select" id="servicio" name="servicio_id" required>
                    <option value="">Elegir...</option>
                    <option value="1">Boda Esencial</option>
                    <option value="2">XV Años Premium</option>
                    <option value="3">Retrato Profesional</option>
                </select>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="fecha" class="form-label">Fecha deseada</label>
                    <input type="date" class="form-control" id="fecha" name="fecha" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="hora" class="form-label">Hora</label>
                    <input type="time" class="form-control" id="hora" name="hora" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="notas" class="form-label">Notas adicionales (Ej. locación deseada)</label>
                <textarea class="form-control" id="notas" name="notas" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100">Confirmar Reserva</button>
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