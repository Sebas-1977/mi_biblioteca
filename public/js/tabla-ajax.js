document.addEventListener('DOMContentLoaded', () => {

    // =========================================================================
    // 0. NOTIFICACIONES Y ALERTAS (AUTO-DESVANECIMIENTO Y CIERRE)
    // =========================================================================
    const alertas = document.querySelectorAll('.alerta');
    
    alertas.forEach(alerta => {
        // Desvanecer pasados 4 segundos (4000ms)
        setTimeout(() => {
            alerta.style.transition = 'opacity 0.5s ease, transform 0.5s ease, margin 0.5s ease';
            alerta.style.opacity = '0';
            alerta.style.transform = 'translateY(-10px)';

            setTimeout(() => {
                alerta.remove();
            }, 500);
        }, 4000);
    });

    // =========================================================================
    // 1. CAPTURA DE ELEMENTOS GENERALES
    // =========================================================================
    const buscador = document.getElementById('buscador') || document.querySelector('.buscador input');
    const contenedorTabla = document.getElementById('contenedor-tabla') || document.querySelector('[data-modulo]');
    const indicador = document.getElementById('buscando');
    const tabs = document.querySelectorAll('.tab-item');

    // Si no hay tabla en esta pantalla, no ejecutamos el resto del script de tablas
    if (!contenedorTabla) return;

    // Obtenemos el nombre del módulo automáticamente (ej: "autores", "generos", "libros")
    const modulo = contenedorTabla.dataset.modulo; 
    let timeout = null;

    // Detectar el estado inicial desde la pestaña activa del HTML (por defecto '1')
    const tabActivaInicial = document.querySelector('.tab-item.active');
    let estadoActual = tabActivaInicial ? (tabActivaInicial.dataset.estado || '1') : '1';

    // =========================================================================
    // 2. FUNCIÓN DE CARGA / BÚSQUEDA GENÉRICA (AJAX)
    // =========================================================================
    async function cargarTabla(pagina = 1) {
        const query = buscador ? buscador.value.trim() : '';
        if (indicador) indicador.style.display = 'inline';

        try {
            // Se incluyen todos los parámetros en la URL (búsqueda, página y estado)
            const url = `/${modulo}?ajax=1&busqueda=${encodeURIComponent(query)}&pagina=${pagina}&estado=${estadoActual}`;

            const respuesta = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!respuesta.ok) throw new Error(`Error HTTP: ${respuesta.status}`);

            const html = await respuesta.text();
            contenedorTabla.innerHTML = html;

        } catch (error) {
            console.error(`Error al cargar la tabla de ${modulo}:`, error);
        } finally {
            if (indicador) indicador.style.display = 'none';
        }
    }

    // =========================================================================
    // 3. EVENTO DE BÚSQUEDA (DEBOUNCE 350ms)
    // =========================================================================
    if (buscador) {
        buscador.addEventListener('input', () => {
            clearTimeout(timeout);
            timeout = setTimeout(() => cargarTabla(1), 350);
        });
    }

    // =========================================================================
    // 4. EVENTO DE PESTAÑAS / TABS (FILTRO POR ESTADO)
    // =========================================================================
    if (tabs.length > 0) {
        tabs.forEach(tab => {
            tab.addEventListener('click', (e) => {
                const tabClickeada = e.currentTarget;

                // Cambiar la clase activa
                tabs.forEach(t => t.classList.remove('active'));
                tabClickeada.classList.add('active');

                // Actualizar estado global y recargar la tabla desde la página 1
                estadoActual = tabClickeada.dataset.estado || '1';
                cargarTabla(1);
            });
        });
    }

    // =========================================================================
    // 5. DELEGACIÓN DE EVENTOS (PAGINACIÓN, BAJA Y ALTA)
    // =========================================================================
    contenedorTabla.addEventListener('click', async (e) => {

        // A) ACCIÓN: PAGINACIÓN
        const linkPagina = e.target.closest('.pagina-btn, .pagination a, .paginacion a');
        if (linkPagina) {
            e.preventDefault();
            const urlParams = new URLSearchParams(linkPagina.search);
            const numPagina = linkPagina.dataset.pagina || urlParams.get('pagina') || 1;
            cargarTabla(numPagina);
            return;
        }

        // B) ACCIONES: DAR DE BAJA / DAR DE ALTA
        const btnBaja = e.target.closest('.btn-baja');
        const btnAlta = e.target.closest('.btn-alta');

        if (btnBaja || btnAlta) {
            e.preventDefault();

            const esAlta = Boolean(btnAlta);
            const btnAccion = esAlta ? btnAlta : btnBaja;
            const accion = esAlta ? 'alta' : 'baja';
            
            const mensajeConfirmacion = esAlta 
                ? '¿Deseas activar/restaurar este registro?' 
                : '¿Estás seguro de que deseas dar de baja este registro?';

            const id = btnAccion.dataset.id;
            const fila = btnAccion.closest('tr');

            if (!confirm(mensajeConfirmacion)) return;

            try {
                const formData = new FormData();
                formData.append('id', id);

                // Endpoint dinámico POST: /libros/alta o /libros/baja
                const respuesta = await fetch(`/${modulo}/${accion}`, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });

                const data = await respuesta.json();

                if (data.ok || data.success) {
                    // Si estamos en la pestaña "Todos", recargamos la tabla para refrescar badges y botones
                    if (estadoActual === 'todos') {
                        cargarTabla();
                    } else if (fila) {
                        // Si estamos en la pestaña "Activos" o "Inactivos", quitamos la fila del DOM
                        fila.remove();
                    }
                } else {
                    alert(data.mensaje || `No se pudo procesar la acción de ${accion}.`);
                }

            } catch (error) {
                console.error(`Error al ejecutar ${accion} en ${modulo}:`, error);
                alert('Ocurrió un error en el servidor.');
            }
        }
    });

});