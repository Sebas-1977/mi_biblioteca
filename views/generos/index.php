<?php
/** @var string $titulo */
?>

<h1><?= htmlspecialchars($titulo) ?></h1>


<div class="toolbar">
    <div class="buscador">
        <input type="text" placeholder="Buscar por nombre o descripción...">
    </div>
    
    <a href="/generos/crear" class="btn-nuevo">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span>Nuevo género</span>
    </a>
</div>


<div id="contenedor-tabla" data-modulo="generos">
    <?php include __DIR__ . '/_tabla.php'; ?>
</div>
<script src="/js/tabla-ajax.js"></script>