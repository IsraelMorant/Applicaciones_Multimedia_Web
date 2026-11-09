document.addEventListener("DOMContentLoaded", () => {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const galleryItems = document.querySelectorAll('#galeria > div');

    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            // 1. Obtener la categoría a filtrar
            const filterValue = button.getAttribute('data-filter');

            // 2. Actualizar estilos de los botones (Activo vs Inactivo)
            filterButtons.forEach(btn => {
                btn.classList.remove('btn-dark');
                btn.classList.add('btn-outline-dark');
            });
            button.classList.remove('btn-outline-dark');
            button.classList.add('btn-dark');

            // 3. Mostrar/Ocultar elementos de la galería
            galleryItems.forEach(item => {
                if (filterValue === 'all' || item.classList.contains(`item-${filterValue}`)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
});