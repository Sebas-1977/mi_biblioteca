document.addEventListener('DOMContentLoaded', () => {
    const buscador = document.getElementById('buscador');
    const resultado = document.getElementById('resultado-libros');
    const indicador = document.getElementById('buscando');

    let timeout = null;
    function cargarLibros(
        pagina = 1
    ) {
        const busqueda = buscador.value.trim();
        indicador.style.display = 'inline';
        fetch(
            `/libros?ajax=1&busqueda=${encodeURIComponent(busqueda)}&pagina=${pagina}`
        )
        .then(respuesta => respuesta.text())
        .then(html => {
            resultado.innerHTML = html;
            indicador.style.display = 'none';
        })
        .catch(error => {
            console.error(
                'Error cargando libros:',
                error
            );
            indicador.style.display = 'none';
        });
    }

    buscador.addEventListener(
        'input',
        () => {
            clearTimeout(timeout);
            timeout = setTimeout(
                () => {
                    cargarLibros(1);
                },
                350
            );
        }
    );
    resultado.addEventListener('click',e => {
         const boton = e.target.closest(
                '.btn-baja'
            );
            if (!boton) {
                return;
            }
            const id = boton.dataset.id;
            if (
                !confirm(
                    '¿Dar de baja este libro?'
                )
            ) {
                return;
            }
            fetch('/libros/baja',
                {
                    method:'POST',
                    headers:{
                        'Content-Type':
                        'application/x-www-form-urlencoded'
                    },
                    body:`id=${id}`
                }
            )
            .then(res => res.json())
            .then(data => {
                if(data.ok){
                    cargarLibros();
                }
            });
        }
    );
});