<?php
/** @var \Model\Libros $libro */
/** @var \Model\Generos $generos */
/** @var \Model\Autores $autores */
?>
<div class="form-card">
    <h2>Crear Libro</h2>
    <form method="POST" action="/libros/crear" enctype="multipart/form-data">
        <div class="form-grid">
            <div class="campo">
                <label for="titulo">
                    Título
                </label>
                <input
                    type="text"
                    id="titulo"
                    name="titulo"
                    value="<?= htmlspecialchars($libro->titulo ?? '') ?>"
                    required
                >
            </div>
            <div class="campo">
                <label for="autor_id">
                    Autor
                </label>
                <select name="autor_id" id="autor_id" required>
                    <option value="">
                        Seleccionar autor
                    </option>
                    <?php foreach ($autores as $autor): ?>
                        <option
                            value="<?= $autor->id ?>"
                            <?= ($libro->autor_id ?? '') == $autor->id ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars(
                                $autor->nombre . ' ' . $autor->apellido
                            ) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo">
                <label for="genero_id">
                    Género
                </label>
                <select name="genero_id" id="genero_id" required>
                    <option value="">
                        Seleccionar género
                    </option>
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
                <label for="anio">
                    Año
                </label>
                <input
                    type="number"
                    id="anio"
                    name="anio"
                    value="<?= htmlspecialchars($libro->anio ?? '') ?>"
                >
            </div>
            <div class="campo">
                <label for="paginas">
                    Páginas
                </label>
                <input
                    type="number"
                    id="paginas"
                    name="paginas"
                    value="<?= htmlspecialchars($libro->paginas ?? '') ?>"
                >
            </div>
            <div class="campo">
                <label for="estado">
                    Estado
                </label>
                <select name="estado" id="estado">
                    <option value="pendiente">
                        Pendiente
                    </option>
                    <option value="en_progreso">
                        En progreso
                    </option>
                    <option value="leido">
                        Leido
                    </option>
                </select>
            </div>
            <div class="campo">
                <label for="portada">
                    Portada
                </label>
                <input
                    type="file"
                    id="portada"
                    name="portada"
                    accept="image/jpeg,image/png,image/webp,image/gif"
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
</div>

        <div class="acciones-form">
            <button 
                type="submit"
                class="btn-guardar"
            >
                Guardar
            </button>
            <a 
                href="/libros"
                class="btn-cancelar"
            >
                Cancelar
            </a>
        </div>
    </form>
</div>