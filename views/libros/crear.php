<?php
/** @var string $titulo */
/** @var \Model\Libros $libro */
/** @var \Model\Generos[] $generos */
/** @var \Model\Autores[] $autores */
/** @var array $errores */

// Aseguramos que $errores sea un array o lo inicializamos
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
    <h2><?= htmlspecialchars($titulo ?? 'Crear Libro') ?></h2>

    <form method="POST" action="/libros/crear" enctype="multipart/form-data" novalidate>
        <div class="form-grid">
            <div class="campo">
                <label for="titulo">
                    Título <span class="requerido" aria-hidden="true">*</span>
                </label>
                <input
                    type="text"
                    id="titulo"
                    name="titulo"
                    value="<?= htmlspecialchars($libro->titulo ?? '') ?>"
                    required
                    aria-required="true"
                >
            </div>

            <div class="campo">
                <label for="autor_id">
                    Autor <span class="requerido" aria-hidden="true">*</span>
                </label>
                <select name="autor_id" id="autor_id" required aria-required="true">
                    <option value="">Seleccionar autor</option>
                    <?php foreach ($autores as $autor): ?>
                        <option
                            value="<?= $autor->id ?>"
                            <?= ($libro->autor_id ?? '') == $autor->id ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($autor->nombre . ' ' . $autor->apellido) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="genero_id">
                    Género <span class="requerido" aria-hidden="true">*</span>
                </label>
                <select name="genero_id" id="genero_id" required aria-required="true">
                    <option value="">Seleccionar género</option>
                    <?php foreach ($generos as $genero): ?>
                        <option
                            value="<?= $genero->id ?>"
                            <?= ($libro->genero_id ?? '') == $genero->id ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($genero->nombre) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="anio">Año</label>
                <input
                    type="number"
                    id="anio"
                    name="anio"
                    min="1000"
                    max="<?= date('Y') ?>"
                    value="<?= htmlspecialchars($libro->anio ?? '') ?>"
                >
            </div>

            <div class="campo">
                <label for="paginas">Páginas</label>
                <input
                    type="number"
                    id="paginas"
                    name="paginas"
                    min="1"
                    value="<?= htmlspecialchars($libro->paginas ?? '') ?>"
                >
            </div>

            <div class="campo">
                <label for="estado">Estado de lectura</label>
                <select name="estado" id="estado">
                    <option value="pendiente" <?= ($libro->estado ?? '') === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                    <option value="en_progreso" <?= ($libro->estado ?? '') === 'en_progreso' ? 'selected' : '' ?>>En progreso</option>
                    <option value="leido" <?= ($libro->estado ?? '') === 'leido' ? 'selected' : '' ?>>Leído</option>
                </select>
            </div>

            <div class="campo">
                <label for="portada">Portada</label>
                <input
                    type="file"
                    id="portada"
                    name="portada"
                    accept="image/jpeg,image/png,image/webp,image/gif"
                    aria-describedby="portada-ayuda"
                >
                <small id="portada-ayuda" class="texto-ayuda">Formatos admitidos: JPG, PNG, WEBP o GIF.</small>
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
                    <?= ($libro->activo ?? 1) ? 'checked' : '' ?>
                >
                <label for="activo_1">Activo</label>

                <input 
                    type="radio" 
                    id="activo_0" 
                    name="activo" 
                    value="0" 
                    <?= isset($libro->activo) && !$libro->activo ? 'checked' : '' ?>
                >
                <label for="activo_0">Inactivo</label>
            </div>
        </fieldset>

        <div class="acciones-form">
            <button type="submit" class="btn-guardar">Guardar</button>
            <a href="/libros" class="btn-cancelar">Cancelar</a>
        </div>
    </form>
</div>