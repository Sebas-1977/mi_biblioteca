<?php

namespace Classes;

class Auth
{
    public static function init(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Verifica si hay sesión activa
    public static function isAuth(): void
    {
        self::init();
        if (!isset($_SESSION['login']) || !$_SESSION['login']) {
            header('Location: /login');
            exit;
        }
    }

    // Verifica si el usuario es administrador
    public static function isAdmin(): void
    {
        self::init();
        if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
            header('Location: /');
            exit;
        }
    }

    // Retorna los datos del usuario logueado
    public static function user(): ?array
    {
        self::init();
        return $_SESSION['usuario'] ?? null;
    }

    // En Classes/Auth.php

    // Redirige al panel si el usuario YA está autenticado
    public static function isGuest(): void
    {
        self::init();
        if (isset($_SESSION['login']) && $_SESSION['login']) {
            header('Location: /libros');
            exit;
        }
    }
}