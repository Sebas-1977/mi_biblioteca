<?php
/** @var string $titulo */
/** @var \Model\Generos $genero */
/** @var array $errores */
?>

<?php if (!empty($errores)): ?>
    <div class="alertas-contenedor">
        <?php foreach ($errores as $error): ?>
            <div class="alerta alerta-error"><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="form-card">
    <h2>Crear Género</h2>

    <form method="POST" action="/generos/crear">
        <div class="form-grid">
            <div class="campo">
                <label for="nombre">Nombre</label>
                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    value="<?= htmlspecialchars($genero->nombre ?? '') ?>"
                    required
                >
            </div>

            <div class="campo campo-full">
                <label for="descripcion">Descripción</label>
                <textarea
                    id="descripcion"
                    name="descripcion"
                    rows="4"
                    placeholder="Escribe una breve descripción sobre este género literario..."
                ><?= htmlspecialchars($genero->descripcion ?? '') ?></textarea>
            </div>
        </div>

        <div class="campo">
            <label>Estado</label>
            <div class="estado-selector">
                <input 
                    type="radio" 
                    id="activo_1" 
                    name="activo" 
                    value="1" 
                    <?= ($genero->activo ?? 1) ? 'checked' : '' ?>
                >
                <label for="activo_1">Activo</label>

                <input 
                    type="radio" 
                    id="activo_0" 
                    name="activo" 
                    value="0" 
                    <?= isset($genero->activo) && !$genero->activo ? 'checked' : '' ?>
                >
                <label for="activo_0">Inactivo</label>
            </div>
        </div>

        <div class="acciones-form">
            <button type="submit" class="btn-guardar">Guardar</button>
            <a href="/generos" class="btn-cancelar">Cancelar</a>
        </div>
    </form>
</div>