<?php

declare(strict_types=1);

namespace Controllers;

use MVC\Router;
use Model\Autores;
use Classes\Auth;

class AutorController
{
    private const POR_PAGINA = 7;

    /** Lista los autores del catálogo global, con búsqueda y paginación */
    public static function index(Router $router): void
    {
        Auth::isAuth();

        $busqueda = trim($_GET['q'] ?? $_GET['busqueda'] ?? '');
        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));
        $estado = $_GET['estado'] ?? '1';

        // 1. Obtener el total de registros del catálogo global
        $totalRegistros = Autores::total($busqueda, $estado);

        // 2. Calcular el total de páginas
        $totalPaginas = (int) ceil($totalRegistros / self::POR_PAGINA);

        // 3. Obtener la lista de autores
        $autores = Autores::listar($busqueda, $pagina, self::POR_PAGINA, $estado);

        $datos = [
            'titulo'       => 'Autores',
            'autores'      => $autores,
            'busqueda'     => $busqueda,
            'pagina'       => $pagina,
            'totalPaginas' => $totalPaginas,
            'estado'       => $estado
        ];

        if (self::esAjax()) {
            extract($datos);
            require __DIR__ . '/../views/autores/_tabla.php';
            return;
        }

        $router->render('autores/index', $datos);
    }

    /** Muestra el formulario de creación y procesa el guardado */
    public static function crear(Router $router): void
    {
        Auth::isAuth();

        $autor = new Autores();
        $alertas = [];
        $esAjax = self::esAjax();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $_POST;

            $autor->sincronizar($datos);
            $alertas = $autor->validar();

            if (empty($alertas)) {
                if ($autor->guardar()) {
                    if ($esAjax) {
                        self::responderJson([
                            'exito'       => true,
                            'ok'          => true,
                            'mensaje'     => 'Autor creado correctamente',
                            'redireccion' => '/autores?exito=1'
                        ]);
                    }

                    header('Location: /autores?exito=1');
                    exit;
                }
            }

            if ($esAjax) {
                self::responderJson([
                    'exito'   => false,
                    'ok'      => false,
                    'alertas' => $alertas,
                    'errores' => $alertas
                ], 422);
            }
        }

        $router->render('autores/crear', [
            'titulo'  => 'Nuevo Autor',
            'autor'   => $autor,
            'alertas' => $alertas,
            'errores' => $alertas
        ]);
    }

    /** Edita un autor existente en el catálogo global */
    public static function editar(Router $router): void
    {
        Auth::isAuth();

        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
        $autor = Autores::find($id);
        $alertas = [];
        $esAjax = self::esAjax();

        // Validar existencia
        if (!$autor) {
            if ($esAjax) {
                self::responderJson([
                    'exito'   => false,
                    'ok'      => false,
                    'mensaje' => 'Autor no encontrado'
                ], 404);
            }

            header('Location: /autores');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $_POST;

            $autor->sincronizar($datos);
            $alertas = $autor->validar();

            if (empty($alertas)) {
                if ($autor->guardar()) {
                    if ($esAjax) {
                        self::responderJson([
                            'exito'       => true,
                            'ok'          => true,
                            'mensaje'     => 'Autor actualizado correctamente',
                            'redireccion' => '/autores?exito=2'
                        ]);
                    }

                    header('Location: /autores?exito=2');
                    exit;
                }
            }

            if ($esAjax) {
                self::responderJson([
                    'exito'   => false,
                    'ok'      => false,
                    'alertas' => $alertas,
                    'errores' => $alertas
                ], 422);
            }
        }

        $router->render('autores/editar', [
            'titulo'  => 'Editar Autor',
            'autor'   => $autor,
            'alertas' => $alertas,
            'errores' => $alertas
        ]);
    }

    /** Da de baja lógica a un autor por ID */
    public static function baja(): void
    {
        Auth::isAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /autores');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $autor = Autores::find($id);
        $esAjax = self::esAjax();

        if (!$autor) {
            if ($esAjax) {
                self::responderJson([
                    'exito'   => false,
                    'ok'      => false,
                    'mensaje' => 'El autor no existe o ya fue eliminado'
                ], 404);
            }

            header('Location: /autores');
            exit;
        }

        $resultado = $autor->baja();

        if ($esAjax) {
            self::responderJson([
                'exito'   => (bool) $resultado,
                'ok'      => (bool) $resultado,
                'mensaje' => $resultado ? 'Autor dado de baja correctamente' : 'No se pudo realizar la baja'
            ], $resultado ? 200 : 500);
        }

        header('Location: /autores?exito=3');
        exit;
    }

    /** Reactiva un autor inactivo */
    public static function alta(): void
    {
        Auth::isAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /autores');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $autor = Autores::find($id);
        $esAjax = self::esAjax();

        if (!$autor) {
            if ($esAjax) {
                self::responderJson([
                    'exito'   => false,
                    'ok'      => false,
                    'mensaje' => 'El autor no existe o fue eliminado'
                ], 404);
            }

            header('Location: /autores');
            exit;
        }

        $resultado = $autor->alta();

        if ($esAjax) {
            self::responderJson([
                'exito'   => (bool) $resultado,
                'ok'      => (bool) $resultado,
                'mensaje' => $resultado ? 'Autor activado correctamente' : 'No se pudo activar el autor'
            ], $resultado ? 200 : 500);
        }

        header('Location: /autores?exito=4');
        exit;
    }

    /** Muestra el listado de autores inactivos */
    public static function bajas(Router $router): void
    {
        Auth::isAuth();

        $busqueda = trim($_GET['q'] ?? $_GET['busqueda'] ?? '');
        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));

        $totalRegistros = Autores::total($busqueda, '0');
        $totalPaginas = (int) ceil($totalRegistros / self::POR_PAGINA);

        $autores = Autores::listar($busqueda, $pagina, self::POR_PAGINA, '0');

        $datos = [
            'titulo'       => 'Autores Inactivos',
            'autores'      => $autores,
            'busqueda'     => $busqueda,
            'pagina'       => $pagina,
            'totalPaginas' => $totalPaginas
        ];

        if (self::esAjax()) {
            extract($datos);
            require __DIR__ . '/../views/autores/_tabla_bajas.php';
            return;
        }

        $router->render('autores/bajas', $datos);
    }

    // =========================================================================
    // MÉTODOS AUXILIARES
    // =========================================================================

    private static function esAjax(): bool
    {
        return isset($_GET['ajax']) || (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');
    }

    private static function responderJson(array $data, int $codigoEstado = 200): never
    {
        header('Content-Type: application/json');
        http_response_code($codigoEstado);
        echo json_encode($data);
        exit;
    }
}