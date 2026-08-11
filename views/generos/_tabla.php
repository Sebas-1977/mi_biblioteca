<?php
/** @var \Model\Generos[] $generos */
/** @var string $busqueda */
/** @var int $totalPaginas */
/** @var int $pagina */

$busqueda = $busqueda ?? '';
$totalPaginas = $totalPaginas ?? 1;
$pagina = $pagina ?? 1;
?>

<div class="tabla-card">
    <table>
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Nombre</th>
                <th scope="col">Descripción</th>
                <th scope="col">Activo</th>
                <th scope="col">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($generos as $genero): ?>
                <tr>
                    <td><?= (int) $genero->id ?></td>
                    <td><?= htmlspecialchars($genero->nombre) ?></td>
                    <td>
                        <?= !empty($genero->descripcion) 
                            ? htmlspecialchars($genero->descripcion) 
                            : 'Sin descripción' 
                        ?>
                    </td>
                    <td>
                        <?php if ($genero->activo): ?>
                            <span class="badge badge-activo">Activo</span>
                        <?php else: ?>
                            <span class="badge badge-inactivo">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td class="acciones">
                        <a href="/generos/editar?id=<?= (int)$genero->id ?>" 
                           class="btn-editar" 
                           aria-label="Editar género: <?= htmlspecialchars($genero->nombre) ?>">
                            Editar
                        </a>
                        
                        <?php if ((int)$genero->activo === 1): ?>
                            <button type="button" 
                                    class="btn-baja" 
                                    data-id="<?= (int)$genero->id ?>" 
                                    aria-label="Dar de baja género: <?= htmlspecialchars($genero->nombre) ?>">
                                Baja
                            </button>
                        <?php else: ?>   
                            <button type="button" 
                                    class="btn-alta" 
                                    data-id="<?= (int)$genero->id ?>" 
                                    aria-label="Dar de alta género: <?= htmlspecialchars($genero->nombre) ?>">
                                Alta
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if (empty($generos)): ?>
                <tr>
                    <td colspan="5" class="sin-datos">
                        <?= $busqueda !== '' 
                            ? 'No se encontraron géneros para "' . htmlspecialchars($busqueda) . '".' 
                            : 'No hay géneros cargados.' ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($totalPaginas > 1): ?>
<nav class="paginacion" aria-label="Paginación de géneros">
    <?php if ($pagina > 1): ?>
        <a href="#" class="pagina-btn" data-pagina="<?= $pagina - 1 ?>" aria-label="Página anterior">&laquo; Anterior</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
        <a href="#" 
           class="pagina-btn <?= $i === $pagina ? 'activa' : '' ?>" 
           data-pagina="<?= $i ?>"
           <?= $i === $pagina ? 'aria-current="page"' : '' ?>
           aria-label="Página <?= $i ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>

    <?php if ($pagina < $totalPaginas): ?>
        <a href="#" class="pagina-btn" data-pagina="<?= $pagina + 1 ?>" aria-label="Página siguiente">Siguiente &raquo;</a>
    <?php endif; ?>
</nav>
<?php endif; ?>