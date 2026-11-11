<?php
session_start();
require 'conexion.php';

// Obtener todas las fotos del portafolio
try {
    $sql = "SELECT categoria, url_imagen, titulo FROM portafolio ORDER BY id DESC";
    $stmt = $pdo->query($sql);
    $fotos = $stmt->fetchAll();
} catch (PDOException $e) {
    // Si la tabla no existe o hay error, evitamos que la página se rompa
    $fotos = []; 
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GES Estudio - Portafolio</title>
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
                    <li class="nav-item"><a class="nav-link active" href="portafolio.php">Portafolio</a></li>
                    <li class="nav-item"><a class="nav-link" href="contacto.php">Contacto</a></li>
                    
                    <?php if(isset($_SESSION['usuario_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="panel_cliente.php">Mi Panel</a></li>
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
        <h2 class="text-center mb-4">Nuestro Trabajo</h2>
        
        <!-- Botones de filtro enlazados con data-filter -->
        <div class="text-center mb-4">
            <button class="btn btn-dark filter-btn active" data-filter="todos">Todos</button>
            <button class="btn btn-outline-dark filter-btn" data-filter="bodas">Bodas</button>
            <button class="btn btn-outline-dark filter-btn" data-filter="retratos">Retratos</button>
        </div>
        
        <!-- Galería Dinámica -->
        <div class="row g-4" id="galeria">
            <?php if (empty($fotos)): ?>
                <div class="col-12 text-center text-muted">
                    <p>Aún no hay fotos en el portafolio.</p>
                </div>
            <?php else: ?>
                <?php foreach ($fotos as $foto): ?>
                    <!-- La clase 'portfolio-item' y la categoría impresa dinámicamente conectan con el JS -->
                    <div class="col-md-4 portfolio-item <?php echo strtolower(trim(htmlspecialchars($foto['categoria']))); ?>">
                        <div class="card border-0 shadow-sm h-100">
                            <img src="<?php echo htmlspecialchars($foto['url_imagen']); ?>" 
                                 class="card-img-top rounded" 
                                 alt="<?php echo htmlspecialchars($foto['titulo']); ?>"
                                 style="object-fit: cover; height: 300px;">
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2026 GES EStudio Fotográfico. Todos los derechos reservados.</p>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
document.addEventListener("DOMContentLoaded", function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const portfolioItems = document.querySelectorAll('.portfolio-item');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Quitar el color oscuro de todos los botones
            filterBtns.forEach(b => {
                b.classList.remove('btn-dark', 'active');
                b.classList.add('btn-outline-dark');
            });
            
            // Pintar de oscuro el botón presionado
            btn.classList.remove('btn-outline-dark');
            btn.classList.add('btn-dark', 'active');

            // Obtener la categoría del botón y forzarla a minúsculas
            const filterValue = btn.getAttribute('data-filter').toLowerCase().trim();

            // Mostrar u ocultar imágenes
            portfolioItems.forEach(item => {
                // Comparamos si es 'todos' o si la imagen tiene la clase de la categoría
                if (filterValue === 'todos' || filterValue === 'all' || item.classList.contains(filterValue)) {
                    item.style.display = 'block'; 
                } else {
                    item.style.display = 'none'; 
                }
            });
        });
    });
});
</script>
</body>
</html>