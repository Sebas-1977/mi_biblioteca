<?php
/** 
 * @var string $titulo 
 * @var string $busqueda 
 * @var string $estado 
 */
?>

<!-- ENCABEZADO Y BOTÓN DE ACCIÓN -->
<div class="header-acciones">
    <h1><?= htmlspecialchars($titulo) ?></h1>
</div>

<!-- BARRA DE PESTAÑAS (TABS DE ESTADO) -->
<div class="tabs-estado">
    <button type="button" class="tab-item <?= ($estado ?? '1') === '1' ? 'active' : ''; ?>" data-estado="1">
        Activos
    </button>
    <button type="button" class="tab-item <?= ($estado ?? '1') === '0' ? 'active' : ''; ?>" data-estado="0">
        Inactivos
    </button>
    <button type="button" class="tab-item <?= ($estado ?? '1') === 'todos' ? 'active' : ''; ?>" data-estado="todos">
        Todos
    </button>
</div>

<!-- BARRA DE BÚSQUEDA -->
<div class="toolbar">
    <div class="buscador">
        <input 
            type="text" 
            id="buscador" 
            value="<?= htmlspecialchars($busqueda ?? ''); ?>" 
            placeholder="Buscar por título, autor o género..."
            autocomplete="off"
        >
        <span id="buscando" style="display:none;">Buscando...</span>
    </div>
    <a href="/libros/crear" class="btn-nuevo">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span>Nuevo libro</span>
    </a>
</div>

<!-- views/libros/index.php (o en tu header.php) -->

<?php if (isset($_GET['exito']) && $_GET['exito'] === '1'): ?>
    <div class="alerta alerta-exito">
        ¡La operación se realizó con éxito!
    </div>
<?php endif; ?>

<!-- CONTENEDOR DE LA TABLA PARCIAL -->
<div id="contenedor-tabla" data-modulo="libros">
    <?php include __DIR__ . '/_tabla.php'; ?>
</div>

<!-- JS ESPECÍFICO -->
<script src="/js/tabla-ajax.js"></script>