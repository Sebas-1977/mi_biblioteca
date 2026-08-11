<?php
/** 
 * @var string $titulo 
 * @var string $busqueda 
 * @var string $estado 
 */
?>

<div class="header-acciones">
    <h1><?= htmlspecialchars($titulo) ?></h1>
</div>

<!-- TABS DE FILTRADO POR ESTADO -->
<div class="tabs-estado" role="tablist" aria-label="Filtrar libros por estado">
    <button type="button" 
            role="tab"
            class="tab-item <?= ($estado ?? '1') === '1' ? 'active' : ''; ?>" 
            data-estado="1"
            aria-selected="<?= ($estado ?? '1') === '1' ? 'true' : 'false'; ?>">
        Activos
    </button>
    <button type="button" 
            role="tab"
            class="tab-item <?= ($estado ?? '1') === '0' ? 'active' : ''; ?>" 
            data-estado="0"
            aria-selected="<?= ($estado ?? '1') === '0' ? 'true' : 'false'; ?>">
        Inactivos
    </button>
    <button type="button" 
            role="tab"
            class="tab-item <?= ($estado ?? '1') === 'todos' ? 'active' : ''; ?>" 
            data-estado="todos"
            aria-selected="<?= ($estado ?? '1') === 'todos' ? 'true' : 'false'; ?>">
        Todos
    </button>
</div>

<!-- BARRA DE BÚSQUEDA Y ACCIÓN -->
<div class="toolbar">
    <div class="buscador">
        <label for="buscador" class="sr-only">Buscar libros</label>
        <input 
            type="search" 
            id="buscador" 
            value="<?= htmlspecialchars($busqueda ?? ''); ?>" 
            placeholder="Buscar por título, autor o género..."
            autocomplete="off"
        >
        <span id="buscando" style="display:none;" aria-live="polite">Buscando...</span>
    </div>
    <a href="/libros/crear" class="btn-nuevo">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span>Nuevo libro</span>
    </a>
</div>

<?php if (isset($_GET['exito'])): ?>
    <div class="alertas-contenedor">
        <div class="alerta alerta-exito" role="alert" aria-live="polite">
            <?php 
                switch ($_GET['exito']) {
                    case '1': echo 'Libro creado correctamente.'; break;
                    case '2': echo 'Libro actualizado correctamente.'; break;
                    case '3': echo 'Libro dado de baja correctamente.'; break;
                    case '4': echo 'Libro activado correctamente.'; break;
                }
            ?>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alertas-contenedor">
        <div class="alerta alerta-error" role="alert">
            <span><?php echo htmlspecialchars($error); ?></span>
            <button type="button" class="btn-cerrar-alerta" onclick="desvanecerElemento(this.parentElement)">&times;</button>
        </div>
    </div>
<?php endif; ?>

<!-- CONTENEDOR TABLA PARCIAL -->
<div id="contenedor-tabla" data-modulo="libros">
    <?php include __DIR__ . '/_tabla.php'; ?>
</div>

