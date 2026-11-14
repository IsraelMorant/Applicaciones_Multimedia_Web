<?php
session_start();
require 'conexion.php';

// Candado estricto: Solo admins
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

// 2. Obtener solo los trabajos (citas) que están pendientes para subirles fotos
$sql_citas_pendientes = "SELECT c.id, c.usuario_id, u.nombre AS cliente_nombre, s.nombre AS servicio_nombre 
                         FROM citas c 
                         JOIN usuarios u ON c.usuario_id = u.id 
                         JOIN servicios s ON c.servicio_id = s.id 
                         WHERE c.estado = 'pendiente' 
                         ORDER BY c.fecha_cita DESC";
$stmt_citas_pendientes = $pdo->query($sql_citas_pendientes);
$citas_pendientes = $stmt_citas_pendientes->fetchAll();

// 3. NUEVO: Lógica para ver fotos aprobadas filtradas por trabajo
$cita_ver_aprobadas = isset($_GET['ver_cita']) ? $_GET['ver_cita'] : null;
$fotos_seleccionadas = [];
$info_cita_aprobada = null;

if ($cita_ver_aprobadas) {
    // Obtener información de la cita que estamos viendo
    $stmt_info = $pdo->prepare("SELECT c.id, u.nombre AS cliente_nombre, s.nombre AS servicio_nombre FROM citas c JOIN usuarios u ON c.usuario_id = u.id JOIN servicios s ON c.servicio_id = s.id WHERE c.id = :id");
    $stmt_info->execute([':id' => $cita_ver_aprobadas]);
    $info_cita_aprobada = $stmt_info->fetch();

    // Obtener solo las fotos seleccionadas de esa cita
    $sql_selecciones = "SELECT id, url_imagen, fecha_subida FROM galerias_clientes WHERE cita_id = :cita_id AND seleccionada = TRUE ORDER BY fecha_subida DESC";
    $stmt_selecciones = $pdo->prepare($sql_selecciones);
    $stmt_selecciones->execute([':cita_id' => $cita_ver_aprobadas]);
    $fotos_seleccionadas = $stmt_selecciones->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración - GES Estudio</title>
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
            <div class="alert alert-success alert-dismissible fade show" role="alert">Foto agregada al portafolio público exitosamente.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
        <?php endif; ?>
        <?php if (isset($_GET['galeria']) && $_GET['galeria'] == 'exito'): ?>
            <div class="alert alert-primary alert-dismissible fade show" role="alert">Foto enviada al trabajo del cliente exitosamente.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
        <?php endif; ?>

        <div class="row">
            <!-- Tabla de Citas -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-dark text-white"><strong>Todas las Citas</strong></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
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
                                                    <div class="d-flex gap-2">
                                                        <!-- NUEVO BOTÓN: Ver Aprobadas -->
                                                        <a href="panel_admin.php?ver_cita=<?php echo $cita['id']; ?>#seccion-aprobadas" class="btn btn-sm btn-info text-white shadow-sm" title="Ver fotos que el cliente eligió">Ver aprobadas</a>
                                                        
                                                        <?php if($cita['estado'] == 'pendiente'): ?>
                                                            <button type="button" class="btn btn-sm btn-outline-primary btn-asignar shadow-sm" data-cita-id="<?php echo $cita['id']; ?>">
                                                                Subir fotos
                                                            </button>
                                                        <?php else: ?>
                                                            <button type="button" class="btn btn-sm btn-secondary shadow-sm" disabled>Completado</button>
                                                        <?php endif; ?>
                                                    </div>
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

            <!-- Formulario de Portafolio Público -->
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
                    <div class="card-header bg-primary text-white"><strong>Subir Foto a Trabajo Específico</strong></div>
                    <div class="card-body">
                        <form action="procesar_galeria_cliente.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Seleccionar Trabajo Pendiente</label>
                                <select name="cita_id" id="select_cita_destino" class="form-select" required>
                                    <option value="">Elegir trabajo...</option>
                                    <?php foreach ($citas_pendientes as $cita_pen): ?>
                                        <option value="<?php echo $cita_pen['id']; ?>">
                                            Cita #<?php echo $cita_pen['id']; ?> - <?php echo htmlspecialchars($cita_pen['cliente_nombre'] . ' (' . $cita_pen['servicio_nombre'] . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Subir Fotografía en Bruto</label>
                                <input type="file" name="imagen" class="form-control" accept="image/jpeg, image/png" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Enviar Foto al Trabajo</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100 border-0 bg-light">
                    <div class="card-body text-center d-flex flex-column justify-content-center">
                        <h5 class="text-muted">Instrucciones de Flujo</h5>
                        <p class="text-muted small">Haz clic en "Subir fotos" directamente en la tabla de citas de arriba para auto-seleccionar el trabajo. Sube aquí las fotografías sin editar para que las revisen.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- NUEVA SECCIÓN: Fotografías aprobadas con filtro -->
        <div id="seccion-aprobadas">
            <h3 class="mb-4">2. Fotografías aprobadas para edición</h3>
            
            <?php if (!$cita_ver_aprobadas): ?>
                <div class="alert alert-secondary text-center shadow-sm p-4">
                    <p class="mb-0 fs-5">Selecciona el botón <strong>"Ver aprobadas"</strong> en la tabla de citas para cargar las fotos que el cliente eligió para ese proyecto.</p>
                </div>
            <?php else: ?>
                <div class="alert alert-info shadow-sm mb-4">
                    Mostrando fotografías aprobadas para el trabajo <strong>#<?php echo $info_cita_aprobada['id']; ?> - <?php echo htmlspecialchars($info_cita_aprobada['cliente_nombre']); ?> (<?php echo htmlspecialchars($info_cita_aprobada['servicio_nombre']); ?>)</strong>.
                    <a href="panel_admin.php#seccion-aprobadas" class="btn btn-sm btn-outline-secondary float-end">Cerrar vista</a>
                </div>

                <div class="row g-4 mb-5">
                    <?php if (empty($fotos_seleccionadas)): ?>
                        <div class="col-12">
                            <div class="alert alert-warning text-center shadow-sm">
                                El cliente aún no ha aprobado ninguna fotografía para este trabajo, o la galería está vacía.
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($fotos_seleccionadas as $foto): ?>
                            <div class="col-md-3 col-sm-6">
                                <div class="card h-100 shadow-sm border-success">
                                    <img src="<?php echo htmlspecialchars($foto['url_imagen']); ?>" class="card-img-top" style="object-fit: cover; height: 200px;">
                                    <div class="card-body p-3 text-center bg-light">
                                        <span class="badge bg-success">Aprobada para edición</span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
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
            const selectCita = document.getElementById('select_cita_destino');
            const formGaleria = document.getElementById('form-galeria-privada');

            botonesAsignar.forEach(boton => {
                boton.addEventListener('click', function() {
                    const citaId = this.getAttribute('data-cita-id');
                    selectCita.value = citaId;
                    formGaleria.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    selectCita.classList.add('resaltar-select');
                    setTimeout(() => {
                        selectCita.classList.remove('resaltar-select');
                    }, 1500);
                });
            });
        });
    </script>
</body>
</html>