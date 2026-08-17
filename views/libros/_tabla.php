<?php
// 1. Primero siempre la protección de autenticación
if (!isset($_SESSION['login']) || !$_SESSION['login']) {
    header('Location: /login');
    exit;
}
/** @var \Model\Libros[] $libros */
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
                <th scope="col">Título</th>
                <th scope="col">Autor</th>
                <th scope="col">Género</th>
                <th scope="col">Año</th>
                <th scope="col">Páginas</th>
                <th scope="col">Estado</th>
                <th scope="col">Portada</th>
                <th scope="col">Activo</th>
                <th scope="col">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($libros as $libro): ?>
                <tr>
                    <td><?= (int) $libro->id ?></td>
                    <td><?= htmlspecialchars($libro->titulo) ?></td>
                    <td>
                        <?php if (!empty($libro->autor_nombre)): ?>
                            <?= htmlspecialchars($libro->autor_nombre . ' ' . $libro->autor_apellido) ?>
                        <?php else: ?>
                            Sin autor
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($libro->genero_nombre ?? 'Sin género') ?></td>
                    <td><?= $libro->anio ? (int)$libro->anio : '-' ?></td>
                    <td><?= $libro->paginas ? (int)$libro->paginas : '-' ?></td>
                    <td>
                        <span class="estado estado-<?= htmlspecialchars($libro->estado) ?>">
                            <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $libro->estado))) ?>
                        </span>
                    </td>
                    <td>
                        <?php if (!empty($libro->portada)): ?>
                            <img src="<?= htmlspecialchars($libro->portada) ?>" 
                                 alt="Portada de <?= htmlspecialchars($libro->titulo) ?>" 
                                 width="45"
                                 onerror="this.onerror=null; this.src='/img/gemini-svg.svg';"
                                 >
                        <?php else: ?>
                            Sin imagen
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($libro->activo): ?>
                            <span class="badge badge-activo">Activo</span>
                        <?php else: ?>
                            <span class="badge badge-inactivo">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td class="acciones">
                        <a href="/libros/editar?id=<?= (int)$libro->id ?>" 
                           class="btn-editar" 
                           aria-label="Editar libro: <?= htmlspecialchars($libro->titulo) ?>">
                            Editar
                        </a>
                        
                        <?php if ((int)$libro->activo === 1): ?>
                            <button type="button" 
                                    class="btn-baja" 
                                    data-id="<?= (int)$libro->id ?>" 
                                    aria-label="Dar de baja libro: <?= htmlspecialchars($libro->titulo) ?>">
                                Baja
                            </button>
                        <?php else: ?>   
                            <button type="button" 
                                    class="btn-alta" 
                                    data-id="<?= (int)$libro->id ?>" 
                                    aria-label="Dar de alta libro: <?= htmlspecialchars($libro->titulo) ?>">
                                Alta
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if (empty($libros)): ?>
                <tr>
                    <td colspan="10" class="sin-datos">
                        <?= $busqueda !== '' 
                            ? 'No se encontraron libros para "' . htmlspecialchars($busqueda) . '".' 
                            : 'No hay libros cargados.' ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($totalPaginas > 1): ?>
<nav class="paginacion" aria-label="Paginación de libros">
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