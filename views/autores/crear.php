<?php
/** @var string $titulo */
/** @var \Model\Autores $autor */
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
    <h2><?= htmlspecialchars($titulo) ?></h2>

    <form method="POST" action="/autores/crear">
        <div class="form-grid">
            <div class="campo">
                <label for="nombre">Nombre</label>
                <input 
                    type="text" 
                    id="nombre" 
                    name="nombre" 
                    value="<?= htmlspecialchars($autor->nombre ?? '') ?>" 
                    required
                >
            </div>

            <div class="campo">
                <label for="apellido">Apellido</label>
                <input 
                    type="text" 
                    id="apellido" 
                    name="apellido" 
                    value="<?= htmlspecialchars($autor->apellido ?? '') ?>" 
                    required
                >
            </div>

            <div class="campo">
                <label for="nacionalidad">Nacionalidad</label>
                <input 
                    type="text" 
                    id="nacionalidad" 
                    name="nacionalidad" 
                    value="<?= htmlspecialchars($autor->nacionalidad ?? '') ?>"
                >
            </div>

            <div class="campo">
    <label for="fecha_nacimiento">Fecha de nacimiento</label>
    <input 
        type="date" 
        id="fecha_nacimiento" 
        name="fecha_nacimiento" 
        value="<?= htmlspecialchars($autor->fecha_nacimiento ?? '') ?>"
        max="<?= date('Y-m-d') ?>" 
    >
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
                    <?= ($autor->activo ?? 1) ? 'checked' : '' ?>
                >
                <label for="activo_1">Activo</label>

                <input 
                    type="radio" 
                    id="activo_0" 
                    name="activo" 
                    value="0" 
                    <?= isset($autor->activo) && !$autor->activo ? 'checked' : '' ?>
                >
                <label for="activo_0">Inactivo</label>
            </div>
        </div>

        <div class="acciones-form">
            <button type="submit" class="btn-guardar">Guardar</button>
            <a href="/autores" class="btn-cancelar">Cancelar</a>
        </div>
    </form>
</div>