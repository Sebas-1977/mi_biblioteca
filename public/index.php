<?php

require_once __DIR__ . '/../includes/app.php';


use Controllers\LibroController;
use Controllers\AutorController;
use Controllers\GeneroController;
use MVC\Router;

$router = new Router();

// ─────────────────────────────────────────
// LIBROS
// ─────────────────────────────────────────

$router->get('/libros', [LibroController::class,'index']);
$router->get('/libros/crear', [LibroController::class,'crear']);
$router->post('/libros/crear', [LibroController::class,'crear']);
$router->get('/libros/editar', [LibroController::class,'editar']);
$router->post('/libros/editar', [LibroController::class,'editar']);
$router->post('/libros/baja', [LibroController::class,'baja']);
$router->post('/libros/alta', [LibroController::class,'alta']);
$router->get('/libros/bajas', [LibroController::class,'bajas']);

// ─────────────────────────────────────────
// AUTORES
// ─────────────────────────────────────────
$router->get('/autores',         [AutorController::class, 'index']);
$router->get('/autores/crear',   [AutorController::class, 'crear']);
$router->post('/autores/crear',  [AutorController::class, 'crear']);
$router->get('/autores/editar',  [AutorController::class, 'editar']);
$router->post('/autores/editar', [AutorController::class, 'editar']);
$router->post('/autores/baja',   [AutorController::class, 'baja']);
$router->post('/autores/alta',   [AutorController::class, 'alta']);
$router->get('/autores/bajas',   [AutorController::class, 'bajas']);

// ─────────────────────────────────────────
// GÉNEROS
// ─────────────────────────────────────────
$router->get('/generos',         [GeneroController::class, 'index']);
$router->get('/generos/crear',   [GeneroController::class, 'crear']);
$router->post('/generos/crear',  [GeneroController::class, 'crear']);
$router->get('/generos/editar',  [GeneroController::class, 'editar']);
$router->post('/generos/editar', [GeneroController::class, 'editar']);
$router->post('/generos/baja',   [GeneroController::class, 'baja']);
$router->post('/generos/alta',   [GeneroController::class, 'alta']);
$router->get('/generos/bajas',   [GeneroController::class, 'bajas']);

$router->comprobarRutas();
