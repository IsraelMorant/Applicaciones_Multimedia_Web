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

    <!-- Barra de Navegación Responsiva -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">GES</a>
            <!-- Botón de menú para móviles -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="servicios.php">Servicios</a></li>
                    <li class="nav-item"><a class="nav-link" href="portafolio.php">Portafolio</a></li>
                    <li class="nav-item"><a class="nav-link" href="contacto.php">Contacto</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-outline-light ms-2" href="login.php">Acceder</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenedor Principal (Aquí cambiará el contenido en cada una de las 10 páginas) -->
    <main class="container my-5" style="min-height: 70vh;">
<h2 class="mb-4">Administración General</h2>
<div class="card shadow-sm mb-4">
    <div class="card-header bg-dark text-white"><strong>Todas las Citas</strong></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Servicio</th>
                        <th>Fecha/Hora</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Juan Pérez</td>
                        <td>Boda Esencial</td>
                        <td>15/12/2026 14:00</td>
                        <td><span class="badge bg-warning text-dark">Pendiente</span></td>
                        <td>
                            <button class="btn btn-sm btn-success">Aprobar</button>
                            <button class="btn btn-sm btn-danger">Rechazar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2026 Estudio Fotográfico. Todos los derechos reservados.</p>
    </footer>

    <!-- Bootstrap JS (Necesario para que el menú funcione en teléfonos) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>