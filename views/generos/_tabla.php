<?php
/** @var \Model\Generos[] $generos */
?>

<div class="tabla-card">

<table>

    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Activo</th>
            <th>Acciones</th>
        </tr>
    </thead>


    <tbody>

    <?php foreach ($generos as $genero): ?>

        <tr>

            <td>
                <?= (int) $genero->id ?>
            </td>


            <td>
                <?= htmlspecialchars($genero->nombre) ?>
            </td>


            <td>
                <?= !empty($genero->descripcion) 
                    ? htmlspecialchars($genero->descripcion) 
                    : 'Sin descripción' 
                ?>
            </td>


            <td>

                <?php if ($genero->activo): ?>

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

                <a
                    href="/generos/editar?id=<?= (int)$genero->id ?>"
                    class="btn-editar"
                >
                    Editar
                </a>


                <button
                    type="button"
                    class="btn-baja"
                    data-id="<?= (int)$genero->id ?>"
                >
                    Baja
                </button>

            </td>
        </tr>


    <?php endforeach; ?>



    <?php if (empty($generos)): ?>

        <tr>

            <td colspan="5" class="sin-datos">

                No hay géneros cargados.

            </td>

        </tr>

    <?php endif; ?>


    </tbody>

</table>

</div>