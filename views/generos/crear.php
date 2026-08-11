<?php
/** @var string $titulo */
/** @var \Model\Generos $genero */
/** @var array $errores */
$errores = $errores ?? [];
// Si viene una variable $error como string individual, la sumamos al array de errores
if (!empty($error) && is_string($error)) {
    $errores[] = $error;
}
?>

<?php if (!empty($errores)): ?>
    <div class="alertas-contenedor" role="alert" aria-live="polite">
        <?php foreach ($errores as $err): ?>
            <div class="alerta alerta-error">
                <span><?= htmlspecialchars($err); ?></span>
                <button type="button" class="btn-cerrar-alerta" onclick="desvanecerElemento(this.closest('.alerta'))">&times;</button>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="form-card">
    <h2><?= htmlspecialchars($titulo ?? 'Crear Género') ?></h2>

    <form method="POST" action="/generos/crear" novalidate>
        <div class="form-grid">
            <div class="campo">
                <label for="nombre">
                    Nombre <span class="requerido" aria-hidden="true">*</span>
                </label>
                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    value="<?= htmlspecialchars($genero->nombre ?? '') ?>"
                    required
                    aria-required="true"
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

        <fieldset class="campo campo-fieldset">
            <legend class="label-legend">Estado del registro</legend>
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
        </fieldset>

        <div class="acciones-form">
            <button type="submit" class="btn-guardar">Guardar</button>
            <a href="/generos" class="btn-cancelar">Cancelar</a>
        </div>
    </form>
</div>