<!-- views/templates/alertas.php -->
<?php if (!empty($alertas)): ?>
    <div class="contenedor-alertas" id="alertas">
        <?php foreach ($alertas as $tipo => $mensajes): ?>
            <?php foreach ($mensajes as $mensaje): ?>
                <div class="alerta alerta-<?php echo htmlspecialchars($tipo); ?>" role="alert">
                    <span><?php echo htmlspecialchars($mensaje); ?></span>
                    <button type="button" class="btn-cerrar-alerta" onclick="this.parentElement.remove()" aria-label="Cerrar alerta">&times;</button>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>