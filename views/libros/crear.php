<?php
if (!isset($_SESSION['login']) || !$_SESSION['login']) {
    header('Location: /login');
    exit;
}

/** @var string $titulo */
/** @var \Model\Libros $libro */
/** @var \Model\Generos[] $generos */
/** @var \Model\Autores[] $autores */
/** @var array $alertas */

$alertas = $alertas ?? [];
?>

<?php if (!empty($alertas)): ?>
    <div class="alertas-contenedor" role="alert" aria-live="polite">
        <?php foreach ($alertas as $tipo => $mensajes): ?>
            <?php foreach ($mensajes as $mensaje): ?>
                <div class="alerta alerta-<?= htmlspecialchars((string) $tipo); ?>">
                    <span><?= htmlspecialchars((string) $mensaje); ?></span>
                    <button type="button" class="btn-cerrar-alerta" onclick="desvanecerElemento(this.closest('.alerta'))">&times;</button>
                </div>
            <?php endforeach; ?>
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

            <!-- SELECCIÓN DE AUTOR (CATÁLOGO GLOBAL) -->
            <div class="campo">
                <label for="autor_id">
                    Autor <span class="requerido" aria-hidden="true">*</span>
                </label>
                <?php if (empty($autores)): ?>
                    <p class="alerta info">
                        No hay autores registrados. <a href="/autores/crear" class="enlace-crear">Crear un autor</a>
                    </p>
                <?php else: ?>
                    <select name="autor_id" id="autor_id" required aria-required="true">
                        <option value="">Seleccionar autor</option>
                        <?php foreach ($autores as $autor): ?>
                            <option
                                value="<?= htmlspecialchars((string) $autor->id) ?>"
                                <?= (string) ($libro->autor_id ?? '') === (string) $autor->id ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars(trim(($autor->nombre ?? '') . ' ' . ($autor->apellido ?? ''))) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>

            <!-- SELECCIÓN DE GÉNERO -->
            <div class="campo">
                <label for="genero_id">
                    Género <span class="requerido" aria-hidden="true">*</span>
                </label>
                <?php if (empty($generos)): ?>
                    <p class="alerta info">
                        Aún no tienes géneros. <a href="/generos/crear" class="enlace-crear">Crear primer género</a>
                    </p>
                <?php else: ?>
                    <select name="genero_id" id="genero_id" required aria-required="true">
                        <option value="">Seleccionar género</option>
                        <?php foreach ($generos as $genero): ?>
                            <option
                                value="<?= htmlspecialchars((string) $genero->id) ?>"
                                <?= (string) ($libro->genero_id ?? '') === (string) $genero->id ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($genero->nombre ?? '') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>

            <div class="campo">
                <label for="anio">Año</label>
                <input
                    type="number"
                    id="anio"
                    name="anio"
                    min="0"
                    max="<?= date('Y') ?>"
                    value="<?= htmlspecialchars((string) ($libro->anio ?? '')) ?>"
                    placeholder="Ej: 1967"
                >
            </div>

            <div class="campo">
                <label for="paginas">Páginas</label>
                <input
                    type="number"
                    id="paginas"
                    name="paginas"
                    min="1"
                    value="<?= htmlspecialchars((string) ($libro->paginas ?? '')) ?>"
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
                    <?= (int) ($libro->activo ?? 1) === 1 ? 'checked' : '' ?>
                >
                <label for="activo_1">Activo</label>

                <input 
                    type="radio" 
                    id="activo_0" 
                    name="activo" 
                    value="0" 
                    <?= isset($libro->activo) && (int) $libro->activo === 0 ? 'checked' : '' ?>
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