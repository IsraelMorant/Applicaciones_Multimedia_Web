<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Obtener todas las citas del sistema
$sql = "SELECT c.id, c.fecha_cita, c.hora_cita, c.estado, 
               u.nombre AS cliente_nombre, s.nombre AS servicio_nombre 
        FROM citas c 
        JOIN usuarios u ON c.usuario_id = u.id
        JOIN servicios s ON c.servicio_id = s.id 
        ORDER BY c.fecha_cita DESC";
$stmt = $pdo->query($sql);
$todas_las_citas = $stmt->fetchAll();
?>
<!-- ... Tu header y navbar aquí ... -->
<div class="container my-5">
    <h2>Administración General</h2>
    <div class="card shadow-sm mb-4">
        <div class="card-header"><strong>Todas las Citas</strong></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Servicio</th>
                            <th>Fecha / Hora</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($todas_las_citas as $cita): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($cita['cliente_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($cita['servicio_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($cita['fecha_cita'] . ' ' . $cita['hora_cita']); ?></td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        <?php echo ucfirst(htmlspecialchars($cita['estado'])); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

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
            <a class="navbar-brand" href="inicio.php">EstudioFoto</a>
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