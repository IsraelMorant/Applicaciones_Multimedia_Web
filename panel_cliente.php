<?php
session_start();
require 'conexion.php';

// Validar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Obtener las citas del usuario logueado
$sql = "SELECT c.fecha_cita, c.hora_cita, c.estado, s.nombre AS servicio_nombre 
        FROM citas c 
        JOIN servicios s ON c.servicio_id = s.id 
        WHERE c.usuario_id = :usuario_id 
        ORDER BY c.fecha_cita DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([':usuario_id' => $_SESSION['usuario_id']]);
$mis_citas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Panel - Estudio Fotográfico</title>
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
    <!-- Contenido Principal -->
    <main class="container my-5" style="min-height: 70vh;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Mi Panel - Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?></h2>
            <a href="agendar_cita.php" class="btn btn-primary">Nueva Cita</a>
        </div>
        
        <div class="card shadow-sm">
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
                                <tr><td colspan="4" class="text-center py-4">No tienes citas programadas.</td></tr>
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
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2026 GES EStudio Fotográfico. Todos los derechos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>