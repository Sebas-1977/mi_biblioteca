<main class="contenedor seccion">
    <h1>Catálogo de Libros</h1>
    <p class="descripcion-pagina">Explora los libros disponibles en nuestra biblioteca virtual.</p>

    <?php if (empty($libros)): ?>
        <p class="alerta info">No hay libros disponibles en este momento.</p>
    <?php else: ?>
        <div class="grid-libros">
            <?php foreach ($libros as $libro): ?>
                <article class="tarjeta-libro">
                    <div class="portada-contenedor">
                        <?php if (!empty($libro->portada)): ?>
                            <img 
                                src="<?= htmlspecialchars((string) $libro->portada); ?>" 
                                alt="Portada de <?= htmlspecialchars((string) $libro->titulo); ?>"
                                loading="lazy"
                            >
                        <?php else: ?>
                            <div class="sin-portada">Sin imagen</div>
                        <?php endif; ?>
                    </div>

                    <div class="contenido-tarjeta">
                        <?php if (!empty($libro->genero_nombre)): ?>
                            <span class="badge-genero"><?= htmlspecialchars((string) $libro->genero_nombre); ?></span>
                        <?php endif; ?>

                        <h3><?= htmlspecialchars((string) $libro->titulo); ?></h3>

                        <?php 
                            $autorCompleto = trim(($libro->autor_nombre ?? '') . ' ' . ($libro->autor_apellido ?? ''));
                        ?>
                        <?php if ($autorCompleto !== ''): ?>
                            <p class="autor"><strong>Autor:</strong> <?= htmlspecialchars($autorCompleto); ?></p>
                        <?php endif; ?>

                        <p class="anio"><strong>Año:</strong> <?= htmlspecialchars((string) ($libro->anio ?? 'N/A')); ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>