<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Obtener las citas del usuario logueado uniendo la tabla servicios para obtener el nombre
$sql = "SELECT c.fecha_cita, c.hora_cita, c.estado, s.nombre AS servicio_nombre 
        FROM citas c 
        JOIN servicios s ON c.servicio_id = s.id 
        WHERE c.usuario_id = :usuario_id 
        ORDER BY c.fecha_cita DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([':usuario_id' => $_SESSION['usuario_id']]);
$mis_citas = $stmt->fetchAll();
?>

<!-- ... Tu header y navbar aquí ... -->

<div class="container my-5">
    <h2>Mi Panel - Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?></h2>
    
    <div class="card shadow-sm mt-4">
        <div class="card-header"><strong>Mis Citas Próximas</strong></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Servicio</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($mis_citas)): ?>
                            <tr><td colspan="4" class="text-center">No tienes citas programadas.</td></tr>
                        <?php else: ?>
                            <?php foreach ($mis_citas as $cita): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($cita['servicio_nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($cita['fecha_cita']); ?></td>
                                    <td><?php echo htmlspecialchars($cita['hora_cita']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $cita['estado'] == 'pendiente' ? 'warning' : 'success'; ?> text-dark">
                                            <?php echo ucfirst(htmlspecialchars($cita['estado'])); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
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
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Mi Panel</h2>
    <a href="agendar_cita.php" class="btn btn-primary">Nueva Cita</a>
</div>
<div class="card shadow-sm">
    <div class="card-header bg-white"><strong>Mis Citas Próximas</strong></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Servicio</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Retrato Profesional</td>
                        <td>20/11/2026</td>
                        <td>10:00 AM</td>
                        <td><span class="badge bg-warning text-dark">Pendiente</span></td>
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