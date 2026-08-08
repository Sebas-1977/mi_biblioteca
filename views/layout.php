<?php
/**
 * Layout Contenedor
 * @var string $contenido
 */

include __DIR__ . '/layout/header.php';

echo $contenido ?? '';

include __DIR__ . '/layout/footer.php';