<?php
session_start();
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
                        <!-- Menú para usuarios logueados -->
                        <li class="nav-item"><a class="nav-link active" href="panel_cliente.php">Mi Panel</a></li>
                        <li class="nav-item"><a class="nav-link" href="perfil.php">Mi Cuenta</a></li>
                        <li class="nav-item"><a class="nav-link btn btn-outline-danger ms-2 btn-sm" href="logout.php">Salir</a></li>
                    <?php else: ?>
                        <!-- Menú para visitantes -->
                        <li class="nav-item"><a class="nav-link btn btn-outline-light ms-2" href="login.php">Acceder</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Contenedor Principal (Aquí cambiará el contenido en cada una de las 10 páginas) -->
    <main class="container my-5" style="min-height: 70vh;">
     <h2 class="text-center mb-4">Nuestro Trabajo</h2>
<!-- Botones para futuro filtro JS -->
<div class="text-center mb-4">
    <button class="btn btn-dark filter-btn" data-filter="all">Todos</button>
    <button class="btn btn-outline-dark filter-btn" data-filter="bodas">Bodas</button>
    <button class="btn btn-outline-dark filter-btn" data-filter="retratos">Retratos</button>
</div>
<div class="row g-3" id="galeria">
    <div class="col-md-4 item-bodas"><img src="https://via.placeholder.com/400x400" class="img-fluid rounded" alt="Boda 1"></div>
    <div class="col-md-4 item-retratos"><img src="https://via.placeholder.com/400x400" class="img-fluid rounded" alt="Retrato 1"></div>
    <div class="col-md-4 item-bodas"><img src="https://via.placeholder.com/400x400" class="img-fluid rounded" alt="Boda 2"></div>
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