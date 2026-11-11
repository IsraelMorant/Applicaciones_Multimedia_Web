<?php
session_start();
require 'conexion.php';

// Candado estricto: Solo admins
if (!isset($_SESSION['usuario_id']) \vert{}\vert{}$_SESSION['rol'] !== 'admin') {
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
$todas_las_citas =$stmt->fetchAll();

// NUEVO: Obtener la lista de clientes para poder asignarles fotos
$sql_clientes = "SELECT id, nombre, email FROM usuarios WHERE rol = 'cliente' ORDER BY nombre ASC";
$stmt_clientes = $pdo->query($sql_clientes);
$clientes =$stmt_clientes->fetchAll();
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
                        <?php if(isset($_SESSION['rol']) &&$_SESSION['rol'] === 'admin'): ?>
                            <li class="nav-item"><a class="nav-link active" href="panel_admin.php">Panel Admin</a></li>
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
        <h2 class="mb-4">Administración General</h2>

        <!-- Alertas de éxito -->
        <?php if (isset($_GET['foto']) &&$_GET['foto'] == 'guardada'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Foto agregada al portafolio público exitosamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['galeria']) &&$_GET['galeria'] == 'exito'): ?>
            <div class="alert alert-primary alert-dismissible fade show" role="alert">
                Foto enviada a la galería privada del cliente exitosamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Tabla de Citas (Columna Izquierda) -->
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
                                        <?php foreach ($todas_las_citas as$cita): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($cita['cliente_nombre']); ?></td>
                                                <td><?php echo htmlspecialchars($cita['servicio_nombre']); ?></td>
                                                <td><?php echo htmlspecialchars($cita['fecha_cita'] . ' ' .$cita['hora_cita']); ?></td>
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

            <!-- Formulario de Portafolio Público (Columna Derecha Arriba) -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-success text-white"><strong>Agregar a Portafolio Público</strong></div>
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
                                <label class="form-label">Subir Imagen (JPG/PNG)</label>
                                <input type="file" name="imagen" class="form-control" accept="image/jpeg, image/png" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Subir a Portafolio</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- NUEVA SECCIÓN: Asignar fotos a clientes específicos -->
        <h3 class="mt-5 mb-4">Gestión de Clientes (Selección de Fotografías)</h3>
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white"><strong>Subir Foto a Galería de Cliente</strong></div>
                    <div class="card-body">
                        <form action="procesar_galeria_cliente.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Seleccionar Cliente</label>
                                <select name="usuario_id" class="form-select" required>
                                    <option value="">Elegir cliente...</option>
                                    <?php foreach ($clientes as$cliente): ?>
                                        <option value="<?php echo $cliente['id']; ?>">
                                            <?php echo htmlspecialchars($cliente['nombre']) . ' (' . htmlspecialchars($cliente['email']) . ')'; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Subir Fotografía en Bruto</label>
                                <input type="file" name="imagen" class="form-control" accept="image/jpeg, image/png" required>
                                <div class="form-text">Esta foto solo la podrá ver el cliente seleccionado en su panel privado.</div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Enviar Foto al Cliente</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100 border-0 bg-light">
                    <div class="card-body text-center d-flex flex-column justify-content-center">
                        <h5 class="text-muted">Instrucciones de Flujo</h5>
                        <p class="text-muted small">Sube aquí las fotografías sin editar. El cliente podrá verlas en su panel, seleccionar sus favoritas y confirmar la selección para que procedas a editarlas, tal como indica la rúbrica del proyecto.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2026 GES EStudio Fotográfico. Todos los derechos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>