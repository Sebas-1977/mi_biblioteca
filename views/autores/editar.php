<?php
// 1. Primero siempre la protección de autenticación
if (!isset($_SESSION['login']) || !$_SESSION['login']) {
    header('Location: /login');
    exit;
}
/** @var string $titulo */
/** @var \Model\Autores $autor */
/** @var array $alertas */

// Inicializamos $alertas si no está definida
$alertas = $alertas ?? [];
?>

<?php if (!empty($alertas)): ?>
    <div class="alertas-contenedor" role="alert" aria-live="polite">
        <?php foreach ($alertas as $tipo => $mensajes): ?>
            <?php foreach ($mensajes as $mensaje): ?>
                <div class="alerta alerta-<?= htmlspecialchars($tipo); ?>">
                    <span><?= htmlspecialchars($mensaje); ?></span>
                    <button type="button" class="btn-cerrar-alerta" onclick="desvanecerElemento(this.closest('.alerta'))">&times;</button>
                </div>
            <?php endforeach; ?>
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
                    value="<?= htmlspecialchars($autor->nombre ?? '') ?>"
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
                    value="<?= htmlspecialchars($autor->apellido ?? '') ?>"
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
        </fieldset>

        <div class="acciones-form">
            <button type="submit" class="btn-guardar">Actualizar</button>
            <a href="/autores" class="btn-cancelar">Cancelar</a>
        </div>
    </form>
</div>