<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario_id']) || (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin')) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// 1. Obtener las citas
$sql_citas = "SELECT c.id, c.fecha_cita, c.hora_cita, c.estado, s.nombre AS servicio_nombre 
              FROM citas c 
              JOIN servicios s ON c.servicio_id = s.id 
              WHERE c.usuario_id = :usuario_id 
              ORDER BY c.fecha_cita DESC";
$stmt_citas = $pdo->prepare($sql_citas);
$stmt_citas->execute([':usuario_id' => $usuario_id]);
$mis_citas = $stmt_citas->fetchAll();

// 2. Lógica de Galería por Cita (NUEVO)
$cita_seleccionada = isset($_GET['ver_cita']) ? $_GET['ver_cita'] : null;
$fotos_privadas = [];
$cita_estado = '';
$cita_nombre = '';

if ($cita_seleccionada) {
    foreach ($mis_citas as $cita) {
        if ($cita['id'] == $cita_seleccionada) {
            $cita_estado = $cita['estado'];
            $cita_nombre = $cita['servicio_nombre'];
            break;
        }
    }
    // Cargar solo las fotos de ESTE trabajo específico
    $sql_fotos = "SELECT id, url_imagen, seleccionada FROM galerias_clientes WHERE cita_id = :cita_id AND usuario_id = :usuario_id ORDER BY fecha_subida DESC";
    $stmt_fotos = $pdo->prepare($sql_fotos);
    $stmt_fotos->execute([':cita_id' => $cita_seleccionada, ':usuario_id' => $usuario_id]);
    $fotos_privadas = $stmt_fotos->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Panel - GES Estudio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="inicio.php">GES EStudio</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="inicio.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="servicios.php">Servicios</a></li>
                    <li class="nav-item"><a class="nav-link" href="portafolio.php">Portafolio</a></li>
                    <li class="nav-item"><a class="nav-link" href="contacto.php">Contacto</a></li>
                    <li class="nav-item"><a class="nav-link active" href="panel_cliente.php">Mi Panel</a></li>
                    <li class="nav-item"><a class="nav-link" href="perfil.php">Mi Cuenta</a></li>
                    <li class="nav-item ms-2 align-self-center"><a class="btn btn-outline-danger btn-sm" href="logout.php">Salir</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container my-5" style="min-height: 70vh;">
        <h2 class="mb-2">Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></h2>
        
        <?php if (isset($_GET['estado']) && $_GET['estado'] == 'actualizado'): ?>
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                ¡Gracias por confirmar! El estado de tu servicio ha sido actualizado.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- SECCIÓN 1: Estado del Servicio -->
        <div class="card shadow-sm mt-4 mb-5 border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Mis Servicios Contratados</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Servicio</th>
                                <th>Fecha Contratada</th>
                                <th>Estado Actual</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($mis_citas)): ?>
                                <tr><td colspan="4" class="text-center py-4">No tienes servicios contratados.</td></tr>
                            <?php else: ?>
                                <?php foreach($mis_citas as $cita): ?>
                                    <tr>
                                        <td class="ps-3 fw-bold"><?php echo htmlspecialchars($cita['servicio_nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($cita['fecha_cita'] . ' ' . $cita['hora_cita']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $cita['estado'] == 'pendiente' ? 'warning text-dark' : 'success'; ?>">
                                                <?php echo ucfirst(htmlspecialchars($cita['estado'])); ?>
                                            </span>
                                        </td>
                                        <td class="pe-3">
                                            <div class="d-flex gap-2">
                                                <!-- NUEVO BOTÓN: Ver Galería de este trabajo -->
                                                <a href="panel_cliente.php?ver_cita=<?php echo $cita['id']; ?>#galeria-seccion" class="btn btn-sm btn-info text-white shadow-sm">Ver Fotos</a>
                                                
                                                <?php if($cita['estado'] == 'pendiente'): ?>
                                                    <form action="actualizar_estado.php" method="POST" class="m-0">
                                                        <input type="hidden" name="cita_id" value="<?php echo $cita['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-success shadow-sm">Aceptar Trabajo</button>
                                                    </form>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-secondary" disabled>Trabajo Finalizado</button>
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

        <hr class="my-5">

        <!-- SECCIÓN 2: Selección de Fotografías -->
        <div id="galeria-seccion">
            <h4 class="mb-3">Selección de Fotografías</h4>
            
            <?php if (isset($_GET['exito']) && $_GET['exito'] == '1'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    ¡Tu selección ha sido guardada exitosamente! El estudio ha sido notificado.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (!$cita_seleccionada): ?>
                <div class="alert alert-secondary text-center mt-4 p-4 shadow-sm border-0">
                    <p class="mb-0 fs-5">Selecciona el botón <strong>"Ver Fotos"</strong> en alguno de tus servicios arriba para cargar su galería correspondiente.</p>
                </div>
            <?php elseif (empty($fotos_privadas)): ?>
                <div class="alert alert-info text-center mt-4 p-4 shadow-sm border-0">
                    <p class="mb-0">El estudio aún no ha subido fotografías para el servicio <strong><?php echo htmlspecialchars($cita_nombre); ?></strong>.</p>
                </div>
            <?php else: ?>
                <p class="text-muted mb-4">Mostrando fotos de: <strong><?php echo htmlspecialchars($cita_nombre); ?></strong></p>
                <form action="procesar_seleccion.php" method="POST">
                    <input type="hidden" name="cita_id" value="<?php echo $cita_seleccionada; ?>">
                    <div class="row g-4 mb-4">
                        <?php foreach ($fotos_privadas as $foto): ?>
                            <div class="col-md-4 col-lg-3">
                                <div class="card h-100 shadow-sm <?php echo $foto['seleccionada'] ? 'border-success border-2' : 'border-0'; ?>">
                                    <img src="<?php echo htmlspecialchars($foto['url_imagen']); ?>" class="card-img-top" alt="Foto de sesión" style="object-fit: cover; height: 200px;">
                                    <div class="card-body text-center bg-light">
                                        <div class="form-check d-flex justify-content-center align-items-center gap-2">
                                            <input class="form-check-input fs-5 mt-0" type="checkbox" name="fotos_seleccionadas[]" value="<?php echo $foto['id']; ?>" id="foto_<?php echo $foto['id']; ?>" <?php echo $foto['seleccionada'] ? 'checked' : ''; ?> <?php echo $cita_estado == 'finalizado' ? 'disabled' : ''; ?>>
                                            <label class="form-check-label fw-bold" for="foto_<?php echo $foto['id']; ?>">Seleccionar</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="d-flex justify-content-end bg-white sticky-bottom py-3 border-top">
                        <?php if ($cita_estado == 'pendiente'): ?>
                            <button type="submit" class="btn btn-primary btn-lg px-5 shadow">Confirmar Selección de Fotos</button>
                        <?php else: ?>
                            <span class="text-muted fw-bold align-self-center me-3">Trabajo finalizado. Ya no puedes modificar tu selección.</span>
                            <button type="button" class="btn btn-secondary btn-lg px-5 shadow" disabled>Selección Cerrada</button>
                        <?php endif; ?>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </main>

    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2026 GES EStudio Fotográfico. Todos los derechos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>