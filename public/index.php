<?php

require_once __DIR__ . '/../includes/app.php';

use Controllers\PaginaController;
use Controllers\LoginController;
use Controllers\LibroController;
use Controllers\AutorController;
use Controllers\GeneroController;
use MVC\Router;

$router = new Router();

// ─────────────────────────────────────────
// SITIO PÚBLICO
// ─────────────────────────────────────────

$router->get('/', [PaginaController::class, 'index']); 

// ─────────────────────────────────────────
// AUTENTICACIÓN
// ─────────────────────────────────────────

$router->get('/login', [LoginController::class, 'login']);
$router->post('/login', [LoginController::class, 'login']);
$router->get('/logout', [LoginController::class, 'logout']);

$router->get('/crear', [LoginController::class, 'crear']);
$router->post('/crear', [LoginController::class, 'crear']);

$router->get('/olvide', [LoginController::class, 'olvide']);
$router->post('/olvide', [LoginController::class, 'olvide']);

$router->get('/reestablecer', [LoginController::class, 'reestablecer']);
$router->post('/reestablecer', [LoginController::class, 'reestablecer']);

$router->get('/mensaje', [LoginController::class, 'mensaje']);
$router->get('/confirmar', [LoginController::class, 'confirmar']);

// ─────────────────────────────────────────
// PANEL ADMINISTRACIÓN (LIBROS)
// ─────────────────────────────────────────

$router->get('/libros', [LibroController::class, 'index']);
$router->get('/libros/crear', [LibroController::class, 'crear']);
$router->post('/libros/crear', [LibroController::class, 'crear']);
$router->get('/libros/editar', [LibroController::class, 'editar']);
$router->post('/libros/editar', [LibroController::class, 'editar']);
$router->post('/libros/baja', [LibroController::class, 'baja']);
$router->post('/libros/alta', [LibroController::class, 'alta']);
$router->get('/libros/bajas', [LibroController::class, 'bajas']);

// ─────────────────────────────────────────
// AUTORES Y GÉNEROS
// ─────────────────────────────────────────

$router->get('/autores', [AutorController::class, 'index']);
$router->get('/autores/crear', [AutorController::class, 'crear']);
$router->post('/autores/crear', [AutorController::class, 'crear']);
$router->get('/autores/editar', [AutorController::class, 'editar']);
$router->post('/autores/editar', [AutorController::class, 'editar']);
$router->post('/autores/baja', [AutorController::class, 'baja']);
$router->post('/autores/alta', [AutorController::class, 'alta']);
$router->get('/autores/bajas', [AutorController::class, 'bajas']);

$router->get('/generos', [GeneroController::class, 'index']);
$router->get('/generos/crear', [GeneroController::class, 'crear']);
$router->post('/generos/crear', [GeneroController::class, 'crear']);
$router->get('/generos/editar', [GeneroController::class, 'editar']);
$router->post('/generos/editar', [GeneroController::class, 'editar']);
$router->post('/generos/baja', [GeneroController::class, 'baja']);
$router->post('/generos/alta', [GeneroController::class, 'alta']);
$router->get('/generos/bajas', [GeneroController::class, 'bajas']);

$router->comprobarRutas();