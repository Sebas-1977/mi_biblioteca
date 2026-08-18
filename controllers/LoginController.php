<?php

declare(strict_types=1);

namespace Controllers;

use Model\Usuarios;
use MVC\Router;
use Classes\Email;

class LoginController
{
    /**
     * Auxiliar para estandarizar las respuestas JSON (AJAX)
     */
    private static function respuestaJson(array $data, int $codigo = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($codigo);
        echo json_encode($data);
        exit;
    }

    /**
     * Detecta si la petición entrante es por AJAX
     */
    private static function esAjax(): bool
    {
        return isset($_GET['ajax']) || (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');
    }

    /**
     * Muestra e inicia sesión de usuario
     */
    public static function login(Router $router): void
    {
        // 1. Iniciamos la sesión para comprobar si hay alertas guardadas
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

            // 1. Escenario GET: Si viene de restablecer (o cualquier otra ruta con mensaje previo en sesión)
        if (isset($_SESSION['alertas'])) {
            foreach ($_SESSION['alertas'] as $tipo => $mensajes) {
                foreach ($mensajes as $mensaje) {
                    Usuarios::setAlerta($tipo, $mensaje);
                }
            }
            unset($_SESSION['alertas']); // Se destruye para que no reaparezca en F5
        }

        // 2. Escenario GET común (o tras importar de sesión): extraemos el estado actual
        $alertas = Usuarios::getAlertas();
        $esAjax = self::esAjax();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuarios($_POST);
            $alertas = $auth->validarLogin();

            if (empty($alertas)) {
                /** @var Usuarios|null $usuario */
                $usuario = Usuarios::firstWhere('email', $auth->email);

                if (!$usuario) {
                    Usuarios::setAlerta('error', 'El usuario no existe');
                } elseif ((int) $usuario->confirmado !== 1) {
                    Usuarios::setAlerta('error', 'Tu cuenta aún no ha sido confirmada. Revisa tu email');
                } elseif ($usuario->comprobarPassword($auth->password)) {
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }

                    $_SESSION['id'] = $usuario->id;
                    $_SESSION['nombre'] = $usuario->nombre;
                    $_SESSION['apellido'] = $usuario->apellido;
                    $_SESSION['email'] = $usuario->email;
                    $_SESSION['login'] = true;

                    if ($esAjax) {
                        self::respuestaJson([
                            'ok' => true,
                            'mensaje' => 'Inicio de sesión exitoso',
                            'redireccion' => '/libros'
                        ]);
                    }

                    header('Location: /libros');
                    exit;
                } else {
                    Usuarios::setAlerta('error', 'Password incorrecto');
                }
            }

            $alertas = Usuarios::getAlertas();

            if ($esAjax) {
                self::respuestaJson([
                    'ok' => false,
                    'alertas' => $alertas
                ], 422);
            }
        }

        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesión',
            'alertas' => $alertas
        ]);
    }

    /**
     * Cierra la sesión activa
     */
    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];
        session_destroy();

        header('Location: /');
        exit;
    }

    /**
     * Registro de un nuevo usuario
     */
    public static function crear(Router $router): void
    {
        $usuario = new Usuarios();
        $alertas = [];
        $esAjax = self::esAjax();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            if (empty($alertas)) {
                /** @var Usuarios|null $existeUsuario */
                $existeUsuario = Usuarios::firstWhere('email', $usuario->email);

                if ($existeUsuario) {
                    Usuarios::setAlerta('error', 'El email ya está registrado por otro usuario');
                } else {
                    $usuario->hashPassword();
                    $usuario->crearToken();

                    $resultado = $usuario->guardar();

                    if ($resultado) {
                        $email = new Email(
                            $usuario->email,
                            $usuario->nombre,
                            $usuario->apellido,
                            $usuario->token
                        );
                        $email->enviarConfirmacion();

                        if ($esAjax) {
                            self::respuestaJson([
                                'ok' => true,
                                'mensaje' => 'Registro exitoso. Te hemos enviado un email para confirmar tu cuenta',
                                'redireccion' => '/mensaje'
                            ]);
                        }

                        header('Location: /mensaje');
                        exit;
                    }
                }
            }

            $alertas = Usuarios::getAlertas();

            if ($esAjax) {
                self::respuestaJson([
                    'ok' => false,
                    'alertas' => $alertas
                ], 422);
            }
        }

        $router->render('auth/crear', [
            'titulo' => 'Crear Cuenta',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    /**
     * Confirmación de cuenta mediante Token recibida por email
     */
    public static function confirmar(Router $router): void
    {
        $token = trim($_GET['token'] ?? '');

        if ($token === '') {
            header('Location: /');
            exit;
        }

        /** @var Usuarios|null $usuario */
        $usuario = Usuarios::firstWhere('token', $token);

        if (!$usuario) {
            Usuarios::setAlerta('error', 'Token no válido o expirado');
        } else {
            $usuario->confirmado = 1;
            $usuario->token = null;
            $usuario->guardar();

            Usuarios::setAlerta('exito', 'Cuenta confirmada correctamente. Ya puedes iniciar sesión.');
        }

        $alertas = Usuarios::getAlertas();

        $router->render('auth/confirmar', [
            'titulo' => 'Confirmar Cuenta',
            'alertas' => $alertas,
            'confirmado' => empty($alertas['error'] ?? [])
        ]);
    }

    /**
     * Vista intermedia para avisar al usuario que revise su correo
     */
    public static function mensaje(Router $router): void
    {
        $router->render('auth/mensaje', [
            'titulo' => 'Cuenta Creada Exitosamente'
        ]);
    }

    /**
     * Solicitud de reestablecimiento de contraseña
     */
    public static function olvide(Router $router): void
    {
        $alertas = [];
        $esAjax = self::esAjax();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuarios($_POST);
            $alertas = $auth->validarEmail();

            if (empty($alertas)) {
                /** @var Usuarios|null $usuario */
                $usuario = Usuarios::firstWhere('email', $auth->email);

                if ($usuario && (int) $usuario->confirmado === 1) {
                    $usuario->crearToken();
                    $usuario->guardar();

                    $email = new Email(
                        $usuario->email,
                        $usuario->nombre,
                        $usuario->apellido,
                        $usuario->token
                    );
                    $email->enviarInstrucciones();

                    Usuarios::setAlerta('exito', 'Hemos enviado las instrucciones a tu email');
                } else {
                    Usuarios::setAlerta('error', 'El usuario no existe o no ha sido confirmado');
                }
            }

            $alertas = Usuarios::getAlertas();
            $esExito = !empty($alertas['exito'] ?? []);

            if ($esAjax) {
                self::respuestaJson([
                    'ok' => $esExito,
                    'mensaje' => $esExito ? $alertas['exito'][0] : null,
                    'alertas' => $alertas
                ], $esExito ? 200 : 422);
            }
        }

        $router->render('auth/olvide', [
            'titulo' => 'Olvidé mi Password',
            'alertas' => $alertas
        ]);
    }

    /**
     * Ingreso de nueva contraseña mediante Token de recuperación
     */
    public static function reestablecer(Router $router): void
    {
        $token = trim($_GET['token'] ?? '');
        $mostrarFormulario = true;

        if ($token === '') {
            header('Location: /');
            exit;
        }

        /** @var Usuarios|null $usuario */
        $usuario = Usuarios::firstWhere('token', $token);

        if (!$usuario) {
            Usuarios::setAlerta('error', 'Token no válido');
            $mostrarFormulario = false;
        }

        $esAjax = self::esAjax();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $usuario) {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarPasswordConfirmado();

            if (empty($alertas)) {
                $usuario->hashPassword();
                $usuario->token = null;

                if ($usuario->guardar()) {
                    // Verificamos si no hay una sesión activa antes de iniciarla
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }

                    Usuarios::setAlerta('exito', 'Password restablecido correctamente. Ya puedes iniciar sesión.');
                    $_SESSION['alertas'] = Usuarios::getAlertas();

                    if ($esAjax) {
                        self::respuestaJson([
                            'ok' => true,
                            'mensaje' => 'Password reestablecido con éxito',
                            'redireccion' => '/login'
                        ]);
                    }

                    header('Location: /login');
                    exit;
                }
            }

            $alertas = Usuarios::getAlertas();

            if ($esAjax) {
                self::respuestaJson([
                    'ok' => false,
                    'alertas' => $alertas
                ], 422);
            }
        }

        $router->render('auth/reestablecer', [
            'titulo' => 'Reestablecer Password',
            'alertas' => Usuarios::getAlertas(),
            'mostrarFormulario' => $mostrarFormulario
        ]);
    }

        /**
     * Lee los datos entrantes ya sea por $_POST o por JSON (fetch/AJAX)
     */
    private static function obtenerDatosPOST(): array
    {
        if (!empty($_POST)) {
            return $_POST;
        }

        $json = file_get_contents('php://input');
        $datos = json_decode($json, true);

        return is_array($datos) ? $datos : [];
    }
}