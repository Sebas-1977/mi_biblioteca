<?php

declare(strict_types=1);

namespace Controllers;

use Model\Libros;
use MVC\Router;

class PaginaController
{
    /**
     * Muestra la portada y el catálogo público para visitantes
     */
    public static function index(Router $router): void
    {
        // 1. Obtener los libros del usuario admin
        $libros = Libros::obtenerPublicos(8);

        // 2. Renderizar la vista pasando la variable
        $router->render('paginas/index', [
            'titulo' => 'Bienvenido a la Biblioteca',
            'libros' => $libros
        ]);
    }
}