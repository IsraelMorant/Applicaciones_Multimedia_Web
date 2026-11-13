<?php
session_start();
require 'conexion.php';

// Candado estricto: Solo admins (Corregido con 'or' para evitar errores de sintaxis)
if (!isset($_SESSION['usuario_id']) or $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// 1. Obtener todas las citas del sistema 
$sql = "SELECT c.id, c.usuario_id, c.fecha_cita, c.hora_cita, c.estado, 
               u.nombre AS cliente_nombre, s.nombre AS servicio_nombre 
        FROM citas c 
        JOIN usuarios u ON c.usuario_id = u.id
        JOIN servicios s ON c.servicio_id = s.id 
        ORDER BY c.fecha_cita DESC";
$stmt = $pdo->query($sql);
$todas_las_citas = $stmt->fetchAll();

// 2. Obtener la lista de clientes
$sql_clientes = "SELECT id, nombre, email FROM usuarios WHERE rol = 'cliente' ORDER BY nombre ASC";
$stmt_clientes = $pdo->query($sql_clientes);
$clientes = $stmt_clientes->fetchAll();

// 3. Obtener las fotos que los clientes ya seleccionaron (TRUE)
$sql_selecciones = "SELECT g.id, g.url_imagen, g.fecha_subida, u.nombre AS cliente_nombre 
                    FROM galerias_clientes g 
                    JOIN usuarios u ON g.usuario_id = u.id 
                    WHERE g.seleccionada = TRUE 
                    ORDER BY g.fecha_subida DESC";
$stmt_selecciones = $pdo->query($sql_selecciones);
$fotos_seleccionadas = $stmt_selecciones->fetchAll();
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
    <style>
        .resaltar-select {
            box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
            border-color: #198754;
            transition: all 0.3s ease;
        }
    </style>
</head>
<body>

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
                        <?php if(isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
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

        <?php if (isset($_GET['foto']) && $_GET['foto'] == 'guardada'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Foto agregada al portafolio público exitosamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['galeria']) && $_GET['galeria'] == 'exito'): ?>
            <div class="alert alert-primary alert-dismissible fade show" role="alert">
                Foto enviada a la galería privada del cliente exitosamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-dark text-white"><strong>Todas las Citas</strong></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>ID Cita</th>
                                        <th>Cliente</th>
                                        <th>Servicio</th>
                                        <th>Fecha/Hora</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($todas_las_citas)): ?>
                                        <tr><td colspan="6" class="text-center">No hay citas registradas.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($todas_las_citas as $cita): ?>
                                            <tr>
                                                <td class="fw-bold text-muted">#<?php echo $cita['id']; ?></td>
                                                <td><?php echo htmlspecialchars($cita['cliente_nombre']); ?></td>
                                                <td><?php echo htmlspecialchars($cita['servicio_nombre']); ?></td>
                                                <td><?php echo htmlspecialchars($cita['fecha_cita'] . ' ' . $cita['hora_cita']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $cita['estado'] == 'pendiente' ? 'warning' : 'success'; ?> text-dark">
                                                        <?php echo ucfirst(htmlspecialchars($cita['estado'])); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if($cita['estado'] == 'pendiente'): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-primary btn-asignar" data-usuario-id="<?php echo $cita['usuario_id']; ?>">
                                                            Subir fotos
                                                        </button>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-sm btn-secondary" disabled>
                                                            Completado
                                                        </button>
                                                    <?php endif; ?>
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

        <hr class="my-5">

        <h3 class="mb-4">1. Enviar fotos a revisión</h3>
        <div class="row mb-5">
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm" id="form-galeria-privada">
                    <div class="card-header bg-primary text-white"><strong>Subir Foto a Galería de Cliente</strong></div>
                    <div class="card-body">
                        <form action="procesar_galeria_cliente.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Seleccionar Cliente</label>
                                <select name="usuario_id" id="select_cliente_destino" class="form-select" required>
                                    <option value="">Elegir cliente...</option>
                                    <?php foreach ($clientes as $cliente): ?>
                                        <option value="<?php echo $cliente['id']; ?>">
                                            ID: <?php echo $cliente['id']; ?> - <?php echo htmlspecialchars($cliente['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Subir Fotografía en Bruto</label>
                                <input type="file" name="imagen" class="form-control" accept="image/jpeg, image/png" required>
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
                        <p class="text-muted small">Haz clic en "Subir fotos" directamente en la tabla de citas de arriba para auto-seleccionar al cliente. Sube aquí las fotografías sin editar para que las revisen.</p>
                    </div>
                </div>
            </div>
        </div>

        <h3 class="mb-4">2. Fotografías seleccionadas por clientes</h3>
        <div class="row g-4 mb-5">
            <?php if (empty($fotos_seleccionadas)): ?>
                <div class="col-12">
                    <div class="alert alert-secondary text-center shadow-sm">
                        Aún no hay fotografías aprobadas por los clientes.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($fotos_seleccionadas as $foto): ?>
                    <div class="col-md-3 col-sm-6">
                        <div class="card h-100 shadow-sm border-success">
                            <img src="<?php echo htmlspecialchars($foto['url_imagen']); ?>" class="card-img-top" style="object-fit: cover; height: 200px;">
                            <div class="card-body p-3 text-center bg-light">
                                <span class="badge bg-success mb-2">Aprobada para edición</span>
                                <p class="mb-0 fw-bold text-dark">Cliente: <?php echo htmlspecialchars($foto['cliente_nombre']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2026 GES EStudio Fotográfico. Todos los derechos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const botonesAsignar = document.querySelectorAll('.btn-asignar');
            const selectCliente = document.getElementById('select_cliente_destino');
            const formGaleria = document.getElementById('form-galeria-privada');

            botonesAsignar.forEach(boton => {
                boton.addEventListener('click', function() {
                    const usuarioId = this.getAttribute('data-usuario-id');
                    selectCliente.value = usuarioId;
                    formGaleria.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    selectCliente.classList.add('resaltar-select');
                    setTimeout(() => {
                        selectCliente.classList.remove('resaltar-select');
                    }, 1500);
                });
            });
        });
    </script>
</body>
</html>