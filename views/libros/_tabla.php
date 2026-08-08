<?php
/** @var \Model\Libros $libros */
?>

<div class="tabla-card">

<table>

    <thead>
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Autor</th>
            <th>Género</th>
            <th>Año</th>
            <th>Páginas</th>
            <th>Estado</th>
            <th>Portada</th>
            <th>Activo</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($libros as $libro): ?>
        <tr>
            <td>
                <?= (int) $libro->id ?>
            </td>
            <td>
                <?= htmlspecialchars($libro->titulo) ?>
            </td>
            <td>
                <?php if (!empty($libro->autor_nombre)): ?>
                    <?= htmlspecialchars(
                        $libro->autor_nombre . ' ' . $libro->autor_apellido
                    ) ?>
                <?php else: ?>
                    Sin autor
                <?php endif; ?>
            </td>
            <td>
                <?= htmlspecialchars(
                    $libro->genero_nombre ?? 'Sin género'
                ) ?>
            </td>
            <td>
                <?= $libro->anio
                    ? (int)$libro->anio
                    : '-'
                ?>
            </td>
            <td>
                <?= $libro->paginas
                    ? (int)$libro->paginas
                    : '-'
                ?>
            </td>
            <td>
                <span class="estado estado-<?= $libro->estado ?>">
                    <?= htmlspecialchars(
                        ucfirst(
                            str_replace(
                                '_',
                                ' ',
                                $libro->estado
                            )
                        )
                    ) ?>
                </span>
            </td>
            <td>
                <?php if (!empty($libro->portada)): ?>
                    <img
                        src="<?= htmlspecialchars($libro->portada) ?>"
                        alt="Portada <?= htmlspecialchars($libro->titulo) ?>"
                        width="45"
                    >
                <?php else: ?>
                    Sin imagen
                <?php endif; ?>
            </td>
            <td>
                <?php if ($libro->activo): ?>
                    <span class="badge badge-activo">
                        Activo
                    </span>
                <?php else: ?>
                    <span class="badge badge-inactivo">
                        Inactivo
                    </span>
                <?php endif; ?>
        </td>
        <td class="acciones">
            <a href="/libros/editar?id=<?= (int)$libro->id ?>"class="btn-editar">Editar</a>
            
            <?php if ((int)$libro->activo === 1): ?>
            <button type="button" class="btn-baja" data-id="<?= (int)$libro->id ?>">Baja</button>
            <?php else: ?>   
                <!-- Si activo es 0 -> Botón de Alta -->   
            <button type="button" class="btn-alta" data-id="<?= $libro->id; ?>">Alta</button>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
    <?php if (empty($libros)): ?>
        <tr>
            <td colspan="10" class="sin-datos">
                No hay libros cargados.
            </td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
</div>