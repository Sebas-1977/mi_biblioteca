document.addEventListener('DOMContentLoaded', () => {

    // =========================================================================
    // 1. AUTO-DESVANECER ALERTAS PHP Y LIMPIEZA DE URL (CREAR / EDITAR)
    // =========================================================================
    const alertasEstaticas = document.querySelectorAll('.alerta-exito');
    
    alertasEstaticas.forEach(alerta => {
        setTimeout(() => desvanecerElemento(alerta), 3500);
    });

    // Limpia los parámetros ?exito= o ?error= de la barra de direcciones sin recargar la página.
    // Esto evita que la alerta vuelva a aparecer si el usuario presiona F5 / Refrescar.
    if (window.history.replaceState && (window.location.search.includes('exito=') || window.location.search.includes('error='))) {
        const urlLimpia = window.location.protocol + "//" + window.location.host + window.location.pathname;
        window.history.replaceState({ path: urlLimpia }, '', urlLimpia);
    }

    // =========================================================================
    // 2. CAPTURA Y VALIDACIÓN DE ELEMENTOS DE TABLA
    // =========================================================================
    const contenedorTabla = document.getElementById('contenedor-tabla') || document.querySelector('[data-modulo]');
    
    // Si no estamos en una vista de listado/tabla, cortamos la ejecución
    if (!contenedorTabla) return;

    // Selector flexible para el buscador (compatible con ID o clase BEM)
    const buscador = document.getElementById('buscador') || 
                     document.querySelector('.buscador input') || 
                     document.querySelector('.buscador__input');
                     
    const indicador = document.getElementById('buscando');
    const tabs = document.querySelectorAll('.tab-item');

    const modulo = contenedorTabla.dataset.modulo; 
    let timeout = null;

    // Obtener estado activo inicial
    const tabActivaInicial = document.querySelector('.tab-item.active');
    let estadoActual = tabActivaInicial ? (tabActivaInicial.dataset.estado || '1') : '1';

    // =========================================================================
    // 3. PETICIÓN AJAX PARA TABLAS (BÚSQUEDA Y PAGINACIÓN)
    // =========================================================================
    async function cargarTabla(pagina = 1) {
        const query = buscador ? buscador.value.trim() : '';
        
        if (indicador) indicador.style.display = 'inline';
        contenedorTabla.setAttribute('aria-busy', 'true');

        try {
            const url = `/${modulo}?ajax=1&busqueda=${encodeURIComponent(query)}&pagina=${pagina}&estado=${estadoActual}`;

            const respuesta = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!respuesta.ok) throw new Error(`Error HTTP: ${respuesta.status}`);

            const html = await respuesta.text();
            contenedorTabla.innerHTML = html;

        } catch (error) {
            console.error(`Error al cargar la tabla de ${modulo}:`, error);
            mostrarAlerta('No se pudo actualizar el listado.', 'error');
        } finally {
            if (indicador) indicador.style.display = 'none';
            contenedorTabla.removeAttribute('aria-busy');
        }
    }

    // =========================================================================
    // 4. LISTENERS BÚSQUEDA Y TABS
    // =========================================================================
    if (buscador) {
        buscador.addEventListener('input', () => {
            clearTimeout(timeout);
            timeout = setTimeout(() => cargarTabla(1), 350);
        });
    }

    if (tabs.length > 0) {
        tabs.forEach(tab => {
            tab.addEventListener('click', (e) => {
                const tabClickeada = e.currentTarget;

                tabs.forEach(t => t.classList.remove('active'));
                tabClickeada.classList.add('active');

                estadoActual = tabClickeada.dataset.estado || '1';
                cargarTabla(1);
            });
        });
    }

    // =========================================================================
    // 5. DELEGACIÓN DE EVENTOS (PAGINACIÓN, BAJA Y ALTA)
    // =========================================================================
    contenedorTabla.addEventListener('click', async (e) => {

        // A) Paginación
        const linkPagina = e.target.closest('.pagina-btn, .pagination a, .paginacion a');
        if (linkPagina) {
            e.preventDefault();
            const urlParams = new URLSearchParams(linkPagina.search);
            const numPagina = linkPagina.dataset.pagina || urlParams.get('pagina') || 1;
            cargarTabla(numPagina);
            return;
        }

        // B) Alta / Baja AJAX
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

                const respuesta = await fetch(`/${modulo}/${accion}`, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });

                const data = await respuesta.json();

                if (data.ok || data.success) {
                    mostrarAlerta(data.mensaje || `Registro procesado correctamente.`, 'exito');

                    if (estadoActual === 'todos') {
                        cargarTabla();
                    } else if (fila) {
                        fila.remove();
                    }
                } else {
                    mostrarAlerta(data.mensaje || `No se pudo procesar la acción de ${accion}.`, 'error');
                }

            } catch (error) {
                console.error(`Error al ejecutar ${accion} en ${modulo}:`, error);
                mostrarAlerta('Ocurrió un error al procesar la solicitud.', 'error');
            }
        }
    });

}); // <--- FIN DE DOMContentLoaded

// =========================================================================
// FUNCIONES GLOBALES (ACCESIBLES DESDE CUALQUIER LUGAR)
// =========================================================================
function obtenerOCrearContenedorAlertas() {
    let contenedor = document.querySelector('.alertas-contenedor') || document.querySelector('.contenedor-alertas');
    
    if (!contenedor) {
        contenedor = document.createElement('DIV');
        contenedor.className = 'alertas-contenedor';
        contenedor.setAttribute('aria-live', 'polite');
        document.body.appendChild(contenedor);
    }
    return contenedor;
}

function mostrarAlerta(mensaje, tipo = 'exito') {
    const contenedor = obtenerOCrearContenedorAlertas();

    const alerta = document.createElement('DIV');
    alerta.className = `alerta alerta-${tipo}`;
    alerta.setAttribute('role', 'alert');

    // Span para el texto del mensaje
    const texto = document.createElement('SPAN');
    texto.textContent = mensaje;
    alerta.appendChild(texto);

    if (tipo === 'error') {
        // Para alertas de ERROR: Crear el botón de cierre "X" y NO autodesvanecer
        const btnCerrar = document.createElement('BUTTON');
        btnCerrar.type = 'button';
        btnCerrar.className = 'btn-cerrar-alerta';
        btnCerrar.innerHTML = '&times;';
        btnCerrar.addEventListener('click', () => desvanecerElemento(alerta));
        alerta.appendChild(btnCerrar);
    } else {
        // Para alertas de ÉXITO: Autodesvanecer tras 3.5 segundos
        setTimeout(() => desvanecerElemento(alerta), 3500);
    }

    contenedor.appendChild(alerta);
}

function desvanecerElemento(elem) {
    if (!elem) return;
    
    elem.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
    elem.style.opacity = '0';
    elem.style.transform = 'translateY(-15px) scale(0.95)';

    setTimeout(() => {
        elem.remove();
    }, 400);
}