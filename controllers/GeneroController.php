<?php

declare(strict_types=1);

namespace Controllers;

use Model\Generos;
use MVC\Router;
use Classes\Auth;

class GeneroController
{
    private const POR_PAGINA = 7;

    public static function index(Router $router): void
    {
        Auth::isAuth();

        $busqueda = trim($_GET['q'] ?? $_GET['busqueda'] ?? '');
        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));
        $estado = $_GET['estado'] ?? '1';

        // Consultas al catálogo global (se removió $usuarioId)
        $totalRegistros = Generos::total($busqueda, $estado);
        $totalPaginas = (int) ceil($totalRegistros / self::POR_PAGINA);
        
        $generos = Generos::listar($busqueda, $pagina, self::POR_PAGINA, $estado);

        $datos = [
            'titulo'       => 'Géneros',
            'generos'      => $generos,
            'busqueda'     => $busqueda,
            'pagina'       => $pagina,
            'totalPaginas' => $totalPaginas,
            'estado'       => $estado
        ];

        if (self::esAjax()) {
            extract($datos);
            require __DIR__ . '/../views/generos/_tabla.php';
            return;
        }

        $router->render('generos/index', $datos);
    }

    public static function crear(Router $router): void
    {
        Auth::isAuth();

        $genero = new Generos();
        $alertas = [];
        $esAjax = self::esAjax();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datosPost = self::obtenerDatosPost();
            $genero->sincronizar($datosPost);
            
            // Se eliminó la asignación de $genero->usuario_id
            $alertas = $genero->validar();

            if (empty($alertas)) {
                $resultado = $genero->guardar();

                if ($resultado) {
                    if ($esAjax) {
                        self::jsonResponse([
                            'exito'       => true,
                            'ok'          => true,
                            'mensaje'     => 'Género creado correctamente',
                            'redireccion' => '/generos?exito=1'
                        ], 200);
                    }

                    header('Location: /generos?exito=1');
                    exit;
                }
            }

            if ($esAjax) {
                self::jsonResponse([
                    'exito'   => false,
                    'ok'      => false,
                    'alertas' => $alertas,
                    'errores' => $alertas
                ], 422);
            }
        }

        $router->render('generos/crear', [
            'titulo'  => 'Nuevo Género',
            'genero'  => $genero,
            'alertas' => $alertas,
            'errores' => $alertas
        ]);
    }

    public static function editar(Router $router): void
    {
        Auth::isAuth();

        $datosPost = $_SERVER['REQUEST_METHOD'] === 'POST' ? self::obtenerDatosPost() : [];
        $id = (int) ($_GET['id'] ?? $datosPost['id'] ?? 0);

        $genero = Generos::find($id);
        $alertas = [];
        $esAjax = self::esAjax();

        if (!$genero) {
            if ($esAjax) {
                self::jsonResponse([
                    'exito'   => false,
                    'ok'      => false,
                    'mensaje' => 'Género no encontrado'
                ], 404);
            }

            header('Location: /generos');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $genero->sincronizar($datosPost);
            $alertas = $genero->validar();

            if (empty($alertas)) {
                $resultado = $genero->guardar();

                if ($resultado) {
                    if ($esAjax) {
                        self::jsonResponse([
                            'exito'       => true,
                            'ok'          => true,
                            'mensaje'     => 'Género actualizado correctamente',
                            'redireccion' => '/generos?exito=2'
                        ], 200);
                    }

                    header('Location: /generos?exito=2');
                    exit;
                }
            }

            if ($esAjax) {
                self::jsonResponse([
                    'exito'   => false,
                    'ok'      => false,
                    'alertas' => $alertas,
                    'errores' => $alertas
                ], 422);
            }
        }

        $router->render('generos/editar', [
            'titulo'  => 'Editar Género',
            'genero'  => $genero,
            'alertas' => $alertas,
            'errores' => $alertas
        ]);
    }

    public static function baja(): void
    {
        Auth::isAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /generos');
            exit;
        }

        $datosPost = self::obtenerDatosPost();
        $id = (int) ($datosPost['id'] ?? 0);
        $genero = Generos::find($id);
        $esAjax = self::esAjax();

        if (!$genero) {
            if ($esAjax) {
                self::jsonResponse([
                    'exito'   => false,
                    'ok'      => false,
                    'mensaje' => 'El género no existe o ya fue eliminado'
                ], 404);
            }

            header('Location: /generos');
            exit;
        }

        $resultado = $genero->baja();

        if ($esAjax) {
            self::jsonResponse([
                'exito'   => (bool) $resultado,
                'ok'      => (bool) $resultado,
                'mensaje' => $resultado ? 'Género dado de baja correctamente' : 'No se pudo realizar la baja'
            ], $resultado ? 200 : 500);
        }

        header('Location: /generos?exito=3');
        exit;
    }

    public static function alta(): void
    {
        Auth::isAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /generos');
            exit;
        }

        $datosPost = self::obtenerDatosPost();
        $id = (int) ($datosPost['id'] ?? 0);
        $genero = Generos::find($id);
        $esAjax = self::esAjax();

        if (!$genero) {
            if ($esAjax) {
                self::jsonResponse([
                    'exito'   => false,
                    'ok'      => false,
                    'mensaje' => 'El género no existe o fue eliminado'
                ], 404);
            }

            header('Location: /generos');
            exit;
        }

        $resultado = $genero->alta();

        if ($esAjax) {
            self::jsonResponse([
                'exito'   => (bool) $resultado,
                'ok'      => (bool) $resultado,
                'mensaje' => $resultado ? 'Género activado correctamente' : 'No se pudo activar el género'
            ], $resultado ? 200 : 500);
        }

        header('Location: /generos?exito=4');
        exit;
    }

    public static function bajas(Router $router): void
    {
        Auth::isAuth();

        $busqueda = trim($_GET['q'] ?? $_GET['busqueda'] ?? '');
        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));

        $totalRegistros = Generos::total($busqueda, '0');
        $totalPaginas = (int) ceil($totalRegistros / self::POR_PAGINA);

        $generos = Generos::listar($busqueda, $pagina, self::POR_PAGINA, '0');

        $datos = [
            'titulo'       => 'Géneros Inactivos',
            'generos'      => $generos,
            'busqueda'     => $busqueda,
            'pagina'       => $pagina,
            'totalPaginas' => $totalPaginas
        ];

        if (self::esAjax()) {
            extract($datos);
            require __DIR__ . '/../views/generos/_tabla_bajas.php';
            return;
        }

        $router->render('generos/bajas', $datos);
    }

    // --- MÉTODOS AUXILIARES ---

    private static function jsonResponse(array $data, int $statusCode = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    private static function obtenerDatosPost(): array
    {
        $input = json_decode(file_get_contents('php://input'), true);
        return is_array($input) ? array_merge($_POST, $input) : $_POST;
    }

    private static function esAjax(): bool
    {
        return isset($_GET['ajax']) 
            || (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest')
            || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
    }
}