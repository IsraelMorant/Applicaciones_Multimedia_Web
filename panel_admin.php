<?php
session_start();
require 'conexion.php';

// Candado estricto: Solo admins
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
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración - GES Estudio</title>
    <!-- Bootstrap 5 CSS -->
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
                        <li class="nav-item"><a class="nav-link active" href="panel_admin.php">Mi Panel</a></li>
                        <li class="nav-item"><a class="nav-link" href="perfil.php">Mi Cuenta</a></li>
                        <li class="nav-item"><a class="nav-link btn btn-outline-danger ms-2 btn-sm" href="logout.php">Salir</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link btn btn-outline-light ms-2" href="login.php">Acceder</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenedor Principal -->
    <main class="container my-5" style="min-height: 70vh;">
        <h2 class="mb-4">Administración General</h2>

        <!-- Alerta de éxito al subir una foto -->
        <?php if (isset($_GET['foto']) && $_GET['foto'] == 'guardada'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Foto agregada al portafolio exitosamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Columna Izquierda: Tabla de Citas (Ocupa 8 de 12 espacios) -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm h-100">
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
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($todas_las_citas)): ?>
                                        <tr><td colspan="4" class="text-center">No hay citas registradas.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($todas_las_citas as $cita): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($cita['cliente_nombre']); ?></td>
                                                <td><?php echo htmlspecialchars($cita['servicio_nombre']); ?></td>
                                                <td><?php echo htmlspecialchars($cita['fecha_cita'] . ' ' . $cita['hora_cita']); ?></td>
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

            <!-- Columna Derecha: Formulario de Portafolio (Ocupa 4 de 12 espacios) -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-success text-white"><strong>Agregar Foto</strong></div>
                    <div class="card-body">
                      <form action="procesar_portafolio.php" method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label class="form-label">Título de la foto</label>
        <input type="text" name="titulo" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Categoría</label>
        <select name="categoria" class="form-select" required>
            <option value="bodas">Bodas</option>
            <option value="retratos">Retratos</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Subir Imagen desde PC (JPG/PNG)</label>
        <input type="file" name="imagen" class="form-control" accept="image/jpeg, image/png" required>
    </div>
    <button type="submit" class="btn btn-success w-100">Subir al Portafolio</button>
</form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2026 GES EStudio Fotográfico. Todos los derechos reservados.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>