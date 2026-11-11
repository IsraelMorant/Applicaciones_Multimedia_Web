<?php
session_start();
require 'conexion.php';

// Validar que el usuario haya iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Si es admin, lo mandamos a su panel
if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') {
    header("Location: panel_admin.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener las fotos privadas de este cliente en específico
$sql = "SELECT id, url_imagen, seleccionada FROM galerias_clientes WHERE usuario_id = :usuario_id ORDER BY fecha_subida DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([':usuario_id' => $usuario_id]);
$fotos_privadas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Panel - GES Estudio</title>
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
                    
                    <li class="nav-item"><a class="nav-link active" href="panel_cliente.php">Mi Panel</a></li>
                    <li class="nav-item"><a class="nav-link" href="perfil.php">Mi Cuenta</a></li>
                    <li class="nav-item ms-2 align-self-center"><a class="btn btn-outline-danger btn-sm" href="logout.php">Salir</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container my-5" style="min-height: 70vh;">
        <h2 class="mb-2">Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></h2>
        <p class="text-muted mb-4">Revisa tu galería privada y selecciona las fotografías que deseas que editemos.</p>

        <?php if (isset($_GET['exito']) && $_GET['exito'] == '1'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                ¡Tu selección ha sido guardada exitosamente! El estudio ha sido notificado.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (empty($fotos_privadas)): ?>
            <div class="alert alert-info text-center mt-5 p-5 shadow-sm">
                <h4>Tu galería está vacía</h4>
                <p>El estudio aún no ha subido las fotografías de tu sesión. Vuelve a revisar más tarde.</p>
            </div>
        <?php else: ?>
            <form action="procesar_seleccion.php" method="POST">
                <div class="row g-4 mb-4">
                    <?php foreach ($fotos_privadas as $foto): ?>
                        <div class="col-md-4 col-lg-3">
                            <div class="card h-100 shadow-sm <?php echo $foto['seleccionada'] ? 'border-success border-2' : 'border-0'; ?>">
                                <img src="<?php echo htmlspecialchars($foto['url_imagen']); ?>" 
                                     class="card-img-top" 
                                     alt="Foto de sesión" 
                                     style="object-fit: cover; height: 200px;">
                                <div class="card-body text-center bg-light">
                                    <div class="form-check d-flex justify-content-center align-items-center gap-2">
                                        <input class="form-check-input fs-5 mt-0" 
                                               type="checkbox" 
                                               name="fotos_seleccionadas[]" 
                                               value="<?php echo $foto['id']; ?>" 
                                               id="foto_<?php echo $foto['id']; ?>"
                                               <?php echo $foto['seleccionada'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-bold" for="foto_<?php echo $foto['id']; ?>">
                                            Seleccionar
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="d-flex justify-content-end bg-white sticky-bottom py-3 border-top">
                    <button type="submit" class="btn btn-success btn-lg px-5 shadow">Confirmar Selección</button>
                </div>
            </form>
        <?php endif; ?>
    </main>

    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2026 GES EStudio Fotográfico. Todos los derechos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>