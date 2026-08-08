<?php
/** @var \Model\Autores[] $autores */
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
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Nacionalidad</th>
                <th>Fecha Nac.</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
        <?php foreach ($autores as $autor): ?>
            <tr>
                <td><?= (int) $autor->id ?></td>

                <td><?= htmlspecialchars($autor->nombre) ?></td>

                <td><?= htmlspecialchars($autor->apellido) ?></td>

                <td><?= htmlspecialchars($autor->nacionalidad ?? 'Sin especificar') ?></td>

                <td>
                    <?= $autor->fecha_nacimiento 
                        ? date('d/m/Y', strtotime($autor->fecha_nacimiento)) 
                        : '-' 
                    ?>
                </td>

                <td>
                    <?php if ($autor->activo): ?>
                        <span class="badge badge-activo">Activo</span>
                    <?php else: ?>
                        <span class="badge badge-inactivo">Inactivo</span>
                    <?php endif; ?>
                </td>

                <td class="acciones">
                    <a href="/autores/editar?id=<?= (int) $autor->id ?>" class="btn-editar">Editar</a>
                    <button type="button" class="btn-baja" data-id="<?= (int) $autor->id ?>">Baja</button>
                </td>
            </tr>
        <?php endforeach; ?>

        <?php if (empty($autores)): ?>
            <tr>
                <td colspan="7" class="sin-datos">
                    <?= $busqueda !== ''
                        ? 'No se encontraron autores para "' . htmlspecialchars($busqueda) . '".'
                        : 'No hay autores cargados todavía.' ?>
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($totalPaginas > 1): ?>
<div class="paginacion">
    <?php if ($pagina > 1): ?>
        <a href="#" class="pagina-btn" data-pagina="<?= $pagina - 1 ?>">&laquo; Anterior</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
        <a href="#"
           class="pagina-btn <?= $i === $pagina ? 'activa' : '' ?>"
           data-pagina="<?= $i ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>

    <?php if ($pagina < $totalPaginas): ?>
        <a href="#" class="pagina-btn" data-pagina="<?= $pagina + 1 ?>">Siguiente &raquo;</a>
    <?php endif; ?>
</div>
<?php endif; ?>