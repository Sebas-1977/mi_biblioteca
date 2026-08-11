<?php
/** @var string $titulo */
/** @var \Model\Autores $autor */
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
    <h2><?= htmlspecialchars($titulo ?? 'Editar Autor') ?></h2>

    <form method="POST" action="/autores/editar?id=<?= (int) $autor->id ?>" novalidate>
        <input type="hidden" name="id" value="<?= (int) $autor->id ?>">

        <div class="form-grid">
            <div class="campo">
                <label for="nombre">
                    Nombre <span class="requerido" aria-hidden="true">*</span>
                </label>
                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    value="<?= htmlspecialchars($autor->nombre) ?>"
                    required
                    aria-required="true"
                >
            </div>

            <div class="campo">
                <label for="apellido">
                    Apellido <span class="requerido" aria-hidden="true">*</span>
                </label>
                <input
                    type="text"
                    id="apellido"
                    name="apellido"
                    value="<?= htmlspecialchars($autor->apellido) ?>"
                    required
                    aria-required="true"
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

        <fieldset class="campo campo-fieldset">
            <legend class="label-legend">Estado del registro</legend>
            <div class="estado-selector">
                <input
                    type="radio"
                    id="activo_1"
                    name="activo"
                    value="1"
                    <?= $autor->activo ? 'checked' : '' ?>
                >
                <label for="activo_1">Activo</label>

                <input
                    type="radio"
                    id="activo_0"
                    name="activo"
                    value="0"
                    <?= !$autor->activo ? 'checked' : '' ?>
                >
                <label for="activo_0">Inactivo</label>
            </div>
        </fieldset>

        <div class="acciones-form">
            <button type="submit" class="btn-guardar">Actualizar</button>
            <a href="/autores" class="btn-cancelar">Cancelar</a>
        </div>
    </form>
</div>