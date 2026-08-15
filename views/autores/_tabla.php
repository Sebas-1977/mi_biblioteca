<?php
// 1. Primero siempre la protección de autenticación
if (!isset($_SESSION['login']) || !$_SESSION['login']) {
    header('Location: /login');
    exit;
}
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
                <th scope="col">ID</th>
                <th scope="col">Nombre</th>
                <th scope="col">Apellido</th>
                <th scope="col">Nacionalidad</th>
                <th scope="col">Fecha Nac.</th>
                <th scope="col">Estado</th>
                <th scope="col">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($autores as $autor): ?>
                <?php $nombreCompleto = trim($autor->nombre . ' ' . $autor->apellido); ?>
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
                        <a href="/autores/editar?id=<?= (int)$autor->id ?>" 
                           class="btn-editar" 
                           aria-label="Editar autor: <?= htmlspecialchars($nombreCompleto) ?>">
                            Editar
                        </a>
                        
                        <?php if ((int)$autor->activo === 1): ?>
                            <button type="button" 
                                    class="btn-baja" 
                                    data-id="<?= (int)$autor->id ?>" 
                                    aria-label="Dar de baja autor: <?= htmlspecialchars($nombreCompleto) ?>">
                                Baja
                            </button>
                        <?php else: ?>   
                            <button type="button" 
                                    class="btn-alta" 
                                    data-id="<?= (int)$autor->id ?>" 
                                    aria-label="Dar de alta autor: <?= htmlspecialchars($nombreCompleto) ?>">
                                Alta
                            </button>
                        <?php endif; ?>
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
<nav class="paginacion" aria-label="Paginación de autores">
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