<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_SESSION['rol']) &&$_SESSION['rol'] === 'admin') {
    header("Location: panel_admin.php");
    exit;
}

$usuario_id =$_SESSION['usuario_id'];

// 1. Obtener las citas/servicios contratados por este cliente
$sql_citas = "SELECT c.id, c.fecha_cita, c.hora_cita, c.estado, s.nombre AS servicio_nombre 
              FROM citas c 
              JOIN servicios s ON c.servicio_id = s.id 
              WHERE c.usuario_id = :usuario_id 
              ORDER BY c.fecha_cita DESC";
$stmt_citas = $pdo->prepare($sql_citas);
$stmt_citas->execute([':usuario_id' =>$usuario_id]);
$mis_citas =$stmt_citas->fetchAll();

// NUEVA LÓGICA: Verificar si el cliente tiene al menos una cita pendiente para permitirle editar
$puede_editar = false;
foreach ($mis_citas as$cita) {
    if ($cita['estado'] === 'pendiente') {$puede_editar = true;
        break; 
    }
}

// 2. Obtener las fotos privadas de la galería
$sql_fotos = "SELECT id, url_imagen, seleccionada FROM galerias_clientes WHERE usuario_id = :usuario_id ORDER BY fecha_subida DESC";
$stmt_fotos = $pdo->prepare($sql_fotos);
$stmt_fotos->execute([':usuario_id' =>$usuario_id]);
$fotos_privadas =$stmt_fotos->fetchAll();
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
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
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
        
        <?php if (isset($_GET['estado']) &&$_GET['estado'] == 'actualizado'): ?>
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
                                <?php foreach($mis_citas as$cita): ?>
                                    <tr>
                                        <td class="ps-3 fw-bold"><?php echo htmlspecialchars($cita['servicio_nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($cita['fecha_cita'] . ' ' .$cita['hora_cita']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $cita['estado'] == 'pendiente' ? 'warning text-dark' : 'success'; ?>">
                                                <?php echo ucfirst(htmlspecialchars($cita['estado'])); ?>
                                            </span>
                                        </td>
                                        <td class="pe-3">
                                            <?php if($cita['estado'] == 'pendiente'): ?>
                                                <form action="actualizar_estado.php" method="POST" class="m-0">
                                                    <input type="hidden" name="cita_id" value="<?php echo $cita['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-success shadow-sm">Aceptar Trabajo</button>
                                                </form>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-secondary" disabled>Trabajo Finalizado</button>
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

        <hr class="my-5">

        <!-- SECCIÓN 2: Selección de Fotografías -->
        <h4 class="mb-3">Selección de Fotografías</h4>
        <p class="text-muted mb-4">Revisa tu galería privada y selecciona las fotografías que deseas que editemos.</p>

        <?php if (isset($_GET['exito']) &&$_GET['exito'] == '1'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                ¡Tu selección ha sido guardada exitosamente! El estudio ha sido notificado.
                <button type="button" class="btn-close" data-bs-dismiss="alert"