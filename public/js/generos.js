document.addEventListener('DOMContentLoaded', () => {

    // =========================================================================
    // 1. CAPTURA DE ELEMENTOS DEL DOM
    // =========================================================================
    const buscador = document.getElementById('buscador') || document.querySelector('.buscador input');
    const resultado = document.getElementById('resultado-generos');
    const indicador = document.getElementById('buscando');

    let timeout = null; // Variable para controlar el debounce del buscador

    // =========================================================================
    // 2. FUNCIÓN PRINCIPAL PARA CARGAR LA TABLA VÍA AJAX
    // =========================================================================
    async function cargarGeneros(pagina = 1) {
        const busqueda = buscador ? buscador.value.trim() : '';

        // Mostramos el spinner / texto de "Cargando..." si existe
        if (indicador) indicador.style.display = 'inline';

        try {
            // Construimos la URL con los parámetros necesarios
            const url = `/generos?ajax=1&busqueda=${encodeURIComponent(busqueda)}&pagina=${pagina}`;

            const respuesta = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' // Informa a PHP que es una petición AJAX
                }
            });

            if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');

            // Recibimos la parcial HTML (_tabla.php) y la inyectamos
            const html = await respuesta.text();
            if (resultado) resultado.innerHTML = html;

        } catch (error) {
            console.error('Error cargando géneros:', error);
        } finally {
            // Ocultamos el indicador sin importar si fue éxito o error
            if (indicador) indicador.style.display = 'none';
        }
    }

    // =========================================================================
    // 3. EVENTO DE BÚSQUEDA EN TIEMPO REAL (DEBOUNCE)
    // =========================================================================
    if (buscador) {
        buscador.addEventListener('input', () => {
            // Cancelamos la ejecución anterior si el usuario sigue escribiendo
            clearTimeout(timeout);

            // Esperamos 350ms después de que el usuario deje de teclear
            timeout = setTimeout(() => {
                cargarGeneros(1); // Siempre reiniciamos a la página 1 al buscar
            }, 350);
        });
    }

    // =========================================================================
    // 4. DELEGACIÓN DE EVENTOS EN EL CONTENEDOR DE LA TABLA
    // Usamos delegación porque el contenido de 'resultado' cambia dinámicamente.
    // =========================================================================
    if (resultado) {
        resultado.addEventListener('click', async (e) => {

            // A) ACCIÓN: DAR DE BAJA
            const botonBaja = e.target.closest('.btn-baja');

            if (botonBaja) {
                e.preventDefault();

                const id = botonBaja.dataset.id;
                const filaTr = botonBaja.closest('tr'); // Seleccionamos la fila completa

                if (!confirm('¿Estás seguro de dar de baja este género?')) {
                    return;
                }

                try {
                    // Preparamos los datos POST
                    const formData = new FormData();
                    formData.append('id', id);

                    const respuesta = await fetch('/generos/baja', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest' // Para que el backend responda en JSON
                        },
                        body: formData
                    });

                    const data = await respuesta.json();

                    if (data.ok) {
                        // OPCIÓN A: Animación suave para eliminar la fila del DOM al instante
                        if (filaTr) {
                            if(filaTr) filaTr.remove();
                            // filaTr.style.transition = 'all 0.3s ease';
                            // filaTr.style.opacity = '0';
                            // filaTr.style.transform = 'translateX(15px)';

                            setTimeout(() => {
                                filaTr.remove(); // Remueve el elemento HTML
                            }, 300);
                        }

                        // OPCIÓN B: O si prefieres recalcular paginación completa, desmarcar esta línea:
                        // cargarGeneros();

                    } else {
                        alert(data.mensaje || 'No se pudo realizar la baja.');
                    }

                } catch (error) {
                    console.error('Error al procesar la baja:', error);
                    alert('Ocurrió un error en la comunicación con el servidor.');
                }

                return; // Salimos para no evaluar otros clics
            }

            // B) ACCIÓN: CLIC EN PAGINACIÓN (Opcional pero recomendado)
            const enlacePagina = e.target.closest('.pagination a, .paginacion a');
            if (enlacePagina) {
                e.preventDefault();
                
                // Extraemos el número de página del enlace (ej: ?pagina=2)
                const urlParams = new URLSearchParams(enlacePagina.search);
                const numPagina = urlParams.get('pagina') || 1;

                cargarGeneros(numPagina);
            }
        });
    }

});