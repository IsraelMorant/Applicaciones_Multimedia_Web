<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GES Estudio </title>
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
                        <li class="nav-item"><a class="nav-link btn btn-outline-danger ms-2 btn-sm" href="logout.php">Salir</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link btn btn-outline-light ms-2" href="login.php">Acceder</a></li>
                    <?php endif; ?>

                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenedor Principal (Aquí cambiará el contenido en cada una de las 10 páginas) -->
    <main class="container my-5" style="min-height: 70vh;">
      <h2 class="text-center mb-5">Nuestros Paquetes</h2>
<div class="row g-4">
    <!-- Paquete 1 -->
    <div class="col-md-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
                <h4 class="card-title">Boda Esencial</h4>
                <h2 class="card-price text-primary">$8,500 <small class="text-muted fs-6">MXN</small></h2>
                <p class="card-text mt-3">Cobertura de 6 horas, 100 fotos editadas y galería digital.</p>
                <a href="agendar_cita.php?servicio=boda" class="btn btn-outline-primary w-100">Agendar</a>
            </div>
        </div>
    </div>
    <!-- Paquete 2 -->
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-primary">
            <div class="card-body text-center">
                <div class="badge bg-primary mb-2">Más Popular</div>
                <h4 class="card-title">XV Años Premium</h4>
                <h2 class="card-price text-primary">$12,000 <small class="text-muted fs-6">MXN</small></h2>
                <p class="card-text mt-3">Sesión previa, cobertura total, photobook impreso y dron.</p>
                <a href="agendar_cita.php?servicio=xv" class="btn btn-primary w-100">Agendar</a>
            </div>
        </div>
    </div>
    <!-- Paquete 3 -->
    <div class="col-md-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
                <h4 class="card-title">Retrato Profesional</h4>
                <h2 class="card-price text-primary">$1,200 <small class="text-muted fs-6">MXN</small></h2>
                <p class="card-text mt-3">Sesión en estudio de 1 hora, 10 fotos con retoque fino.</p>
                <a href="agendar_cita.php?servicio=retrato" class="btn btn-outline-primary w-100">Agendar</a>
            </div>
        </div>
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