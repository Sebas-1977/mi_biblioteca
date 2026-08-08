document.addEventListener('DOMContentLoaded', function () {
    const buscador = document.getElementById('buscador');
    const resultado = document.getElementById('resultado-autores');
    const indicador = document.getElementById('buscando');

    let timeoutId = null;

    function buscarAutores(query, pagina = 1) {
        if (indicador) indicador.style.display = 'inline';

        fetch(`/autores?q=${encodeURIComponent(query)}&pagina=${pagina}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(response => response.text())
            .then(html => {
                if (resultado) resultado.innerHTML = html;
                if (indicador) indicador.style.display = 'none';
            })
            .catch(error => {
                console.error('Error al buscar autores:', error);
                if (indicador) indicador.style.display = 'none';
            });
    }

    // Búsqueda en tiempo real con debounce
    if (buscador) {
        buscador.addEventListener('input', function () {
            clearTimeout(timeoutId);
            const query = buscador.value.trim();

            timeoutId = setTimeout(() => {
                buscarAutores(query, 1); // siempre vuelve a la página 1
            }, 350);
        });
    }

    // Delegación de eventos para la paginación Y el botón de Baja
    if (resultado) {
        resultado.addEventListener('click', function (e) {
            
            // 1. Manejo de Paginación
            const link = e.target.closest('.pagina-btn');
            if (link) {
                e.preventDefault();
                const pagina = link.dataset.pagina;
                const query = buscador ? buscador.value.trim() : '';
                buscarAutores(query, pagina);
                return;
            }

            // Manejo del Botón de Baja
const btnBaja = e.target.closest('.btn-baja');
if (btnBaja) {
    e.preventDefault();
    const id = btnBaja.dataset.id;
    const fila = btnBaja.closest('tr'); // Capturamos la fila en la tabla

    if (confirm('¿Estás seguro de que deseas dar de baja este autor?')) {
        const formData = new FormData();
        formData.append('id', id);

        fetch('/autores/baja', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(response => response.json()) // Esperamos la respuesta JSON de PHP
        .then(data => {
            if (data.success) {
                // Opción A: Transición suave y eliminación inmediata de la fila
                fila.style.transition = 'all 0.3s ease';
                fila.style.opacity = '0';
                
                setTimeout(() => {
                    fila.remove();
                }, 100);

            } else {
                alert('No se pudo procesar la baja.');
            }
        })
        .catch(error => {
            console.error('Error al dar de baja:', error);
        });
    }
}
        });
    }
});