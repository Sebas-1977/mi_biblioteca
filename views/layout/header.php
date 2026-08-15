<?php
/**
 * Header y Navbar principal
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$urlActual = $_SERVER['PATH_INFO'] ?? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$isAdminPath = ($urlActual !== '/');

// Verificación de sesión
$isAuth = isset($_SESSION['login']) && $_SESSION['login'] === true;
$nombreUsuario = $_SESSION['nombre'] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? 'Biblioteca Personal' ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="/css/style.css">
</head>

<body>

    <!-- Accesibilidad: Enlace para saltar directamente al contenido -->
    <a href="#contenido-principal" class="sr-only sr-only-focusable">
        Saltar al contenido principal
    </a>

    <header class="header">
        <nav class="navbar" aria-label="Navegación principal">
            <div class="navbar__brand">
                <span class="navbar__icon" aria-hidden="true">&#9412;</span>
                <a href="/" class="navbar__title">Biblioteca</a>
            </div>

            <ul class="navbar__menu">
                <?php if ($isAuth) : ?>
                    <!-- Menú para usuarios autenticados -->
                    <?php if ($isAdminPath) : ?>
                        <li>
                            <?php $esActivo = str_contains($urlActual, '/libros'); ?>
                            <a href="/libros" 
                               class="navbar__link <?= $esActivo ? 'navbar__link--active' : '' ?>"
                               <?= $esActivo ? 'aria-current="page"' : '' ?>>
                                Libros
                            </a>
                        </li>
                        <li>
                            <?php $esActivo = str_contains($urlActual, '/autores'); ?>
                            <a href="/autores" 
                               class="navbar__link <?= $esActivo ? 'navbar__link--active' : '' ?>"
                               <?= $esActivo ? 'aria-current="page"' : '' ?>>
                                Autores
                            </a>
                        </li>
                        <li>
                            <?php $esActivo = str_contains($urlActual, '/generos'); ?>
                            <a href="/generos" 
                               class="navbar__link <?= $esActivo ? 'navbar__link--active' : '' ?>"
                               <?= $esActivo ? 'aria-current="page"' : '' ?>>
                                Géneros
                            </a>
                        </li>
                        <li style="margin-left: 1rem; border-left: 1px solid var(--border-color); padding-left: 1rem;">
                            <a href="/" class="navbar__link" style="color: var(--primary); font-weight: 500;">
                                Ver Sitio Público
                            </a>
                        </li>
                    <?php else : ?>
                        <li>
                            <a href="/" class="navbar__link navbar__link--active" aria-current="page">
                                Catálogo Público
                            </a>
                        </li>
                        <li style="margin-left: 1rem; border-left: 1px solid var(--border-color); padding-left: 1rem;">
                            <a href="/libros" class="navbar__link navbar__link--admin" style="font-weight: 500;">
                                Panel de Administración &rarr;
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Info Usuario y Logout -->
                    <li style="margin-left: 1rem; border-left: 1px solid var(--border-color); padding-left: 1rem; display: flex; align-items: center; gap: 0.75rem;">
                        <span style="font-size: 0.9rem; color: #4b5563;">
                            Hola, <strong style="color: #111827;"><?= htmlspecialchars($nombreUsuario) ?></strong>
                        </span>
                        <a href="/logout" class="navbar__link" style="color: #ef4444; font-weight: 500;">
                            Cerrar Sesión
                        </a>
                    </li>

                <?php else : ?>
                    <!-- Menú para visitantes NO autenticados -->
                    <li>
                        <a href="/" class="navbar__link navbar__link--active" aria-current="page">
                            Catálogo Público
                        </a>
                    </li>
                    <li style="margin-left: 1.5rem; border-left: 1px solid var(--border-color); padding-left: 1.5rem;">
                        <a href="/login" class="navbar__link navbar__link--admin" style="font-weight: 500; display: inline-flex; align-items: center; gap: 0.35rem;">
                            Iniciar Sesión <span style="font-size: 1.1rem; line-height: 1;" aria-hidden="true">&rarr;</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main class="main" id="contenido-principal">
        <div class="container">