<?php
/** @var \Model\Libros $libro */
/** @var \Model\Generos $generos */
/** @var \Model\Autores $autores */
?>
<div class="form-card">
<h2>
    Editar Libro
</h2>

<!-- views/libros/editar.php -->

<?php if (!empty($errores)): ?>
    <div class="alertas-contenedor">
        <?php foreach ($errores as $error): ?>
            <div class="alerta alerta-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" action="/libros/editar?id=<?= (int) $libro->id ?>" enctype="multipart/form-data">
<input 
    type="hidden"
    name="id"
    value="<?= $libro->id ?>"
>
<div class="form-grid">
<div class="campo">
<label>
Título
</label>
<input
    type="text"
    name="titulo"
    value="<?= htmlspecialchars($libro->titulo) ?>"
    required
>
</div>
<div class="campo">
<label>
Autor
</label>
<select name="autor_id">
<?php foreach ($autores as $autor): ?>
<option
value="<?= $autor->id ?>"
<?= $libro->autor_id == $autor->id ? 'selected':'' ?>
>
<?= htmlspecialchars(
    $autor->nombre . ' ' . $autor->apellido
) ?>
</option>
<?php endforeach; ?>
</select>
</div>
<div class="campo">
<label>
Género
</label>
<select name="genero_id">
<?php foreach ($generos as $genero): ?>
<option
value="<?= $genero->id ?>"
<?= $libro->genero_id == $genero->id ? 'selected':'' ?>
>
<?= htmlspecialchars($genero->nombre) ?>
</option>
<?php endforeach; ?>
</select>
</div>
<div class="campo">
<label>
Año
</label>
<input
type="number"
name="anio"
value="<?= $libro->anio ?>"
>
</div>
<div class="campo">
<label>
Páginas
</label>
<input
type="number"
name="paginas"
value="<?= $libro->paginas ?>"
>
</div>
<div class="campo">
<label>
Estado
</label>
<select name="estado">
<option value="pendiente"
<?= $libro->estado === 'pendiente' ? 'selected':'' ?>
>
Pendiente
</option>
<option value="en_progreso"
<?= $libro->estado === 'en_progreso' ? 'selected':'' ?>
>
En Progreso
</option>
<option value="leido"
<?= $libro->estado === 'leido' ? 'selected':'' ?>
>
Leído
</option>
</select>
</div>
<div class="campo">
<label>
Nueva portada
</label>
<input
type="file"
name="portada"
accept="image/jpeg,image/png,image/webp,image/gif"
>
</div>
</div>
<?php if(!empty($libro->portada)): ?>
<!-- ✅ CORRECTO -->
<div class="preview-portada">
    <?php if ($libro->portada): ?>
        <img src="<?php echo htmlspecialchars($libro->portada); ?>" alt="Portada actual">
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="campo">
    <label>Estado</label>
    <div class="estado-selector">
        <input 
            type="radio" 
            id="activo_1" 
            name="activo" 
            value="1" 
            <?= $libro->activo ? 'checked' : '' ?>
        >
        <label for="activo_1">Activo</label>

        <input 
            type="radio" 
            id="activo_0" 
            name="activo" 
            value="0" 
            <?= !$libro->activo ? 'checked' : '' ?>
        >
        <label for="activo_0">Inactivo</label>
    </div>
</div>

<div class="acciones-form">
<button
type="submit"
class="btn-guardar"
>
Actualizar
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