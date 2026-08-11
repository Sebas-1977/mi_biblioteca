<?php

declare(strict_types=1);

namespace Controllers;

use MVC\Router;
use Model\Autores;

class AutorController
{
    private const POR_PAGINA = 7;

    /** Lista los autores, con búsqueda y paginación */
    public static function index(Router $router): void
    {
        $busqueda = trim($_GET['q'] ?? $_GET['busqueda'] ?? '');
        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));
        $estado = $_GET['estado'] ?? '1'; // Capturamos estado igual que en libros

        // 1. Obtener el total de registros filtrados
        $totalRegistros = Autores::total($busqueda, $estado);

        // 2. Calcular el total de páginas
        $totalPaginas = (int) ceil($totalRegistros / self::POR_PAGINA);

        // Ejecuta la búsqueda paginada desde el modelo
        $autores = Autores::listar($busqueda, $pagina, self::POR_PAGINA, $estado);

        $datos = [
            'titulo'       => 'Autores',
            'autores'      => $autores,
            'busqueda'     => $busqueda,
            'pagina'       => $pagina,
            'totalPaginas' => $totalPaginas,
            'estado'   => $estado
        ];

        // Petición AJAX: devuelve solo el fragmento de la tabla
        $esAjax = isset($_GET['ajax']) || (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');

        if ($esAjax) {
            extract($datos);
            include __DIR__ . '/../views/autores/_tabla.php';
            exit;
        }

        $router->render('autores/index', $datos);
    }

    /** Muestra el formulario de creación y procesa el guardado */
    public static function crear(Router $router): void
    {
        $autor = new Autores();
        $errores = [];

        // Detectamos si la petición es AJAX
        $esAjax = isset($_GET['ajax']) || (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $autor->sincronizar($_POST);
            $errores = $autor->validar();

            if (empty($errores)) {
                $resultado = $autor->guardar();

                if ($resultado) {
                    // RESPUESTA EXITOSA PARA AJAX
                    if ($esAjax) {
                        header('Content-Type: application/json');
                        echo json_encode([
                            'exito' => true,
                            'mensaje' => 'Autor creado correctamente',
                            'redireccion' => '/autores?exito=1'
                        ]);
                        exit;
                    }

                    // RESPUESTA TRADICIONAL
                    header('Location: /autores?exito=1');
                    exit;
                }
            }

            // RESPUESTA DE ERRORES PARA AJAX
            if ($esAjax) {
                header('Content-Type: application/json');
                http_response_code(422); // Código 422: Unprocessable Entity
                echo json_encode([
                    'exito' => false,
                    'errores' => $errores
                ]);
                exit;
            }
        }

        // Carga de la vista normal (GET o fallback)
        $router->render('autores/crear', [
            'titulo'  => 'Nuevo Autor',
            'autor'   => $autor,
            'errores' => $errores
        ]);
    }

    public static function editar(Router $router): void
    {
        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
        $autor = Autores::find($id);
        $errores = [];

        // Detectamos si la petición proviene de AJAX
        $esAjax = isset($_GET['ajax']) || (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');

        // 1. Si el registro no existe
        if (!$autor) {
            if ($esAjax) {
                header('Content-Type: application/json');
                http_response_code(404);
                echo json_encode([
                    'ok' => false,
                    'mensaje' => 'Autor no encontrado'
                ]);
                exit;
            }

            // Si es navegación tradicional, redirigimos
            header('Location: /autores');
            exit;
        }

        // 2. Procesamiento del Formulario (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $autor->sincronizar($_POST);
            $errores = $autor->validar();

            if (empty($errores)) {
                $autor->guardar();

                // Respuesta Éxito
                if ($esAjax) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'ok' => true,
                        'mensaje' => 'Autor actualizado correctamente',
                        'redireccion' => '/autores?exito=2'
                    ]);
                    exit;
                }

                header('Location: /autores?exito=2');
                exit;
            }

            // Respuesta Errores de Validación
            if ($esAjax) {
                header('Content-Type: application/json');
                http_response_code(422); // Unprocessable Entity
                echo json_encode([
                    'ok' => false,
                    'errores' => $errores
                ]);
                exit;
            }
        }

        // 3. Renderizado de la Vista (Navegación normal GET)
        $router->render('autores/editar', [
            'titulo'  => 'Editar Autor',
            'autor'  => $autor,
            'errores' => $errores
        ]);
    }

    /** Da de baja un género por ID (Soporta AJAX y Formularios Tradicionales) */
    public static function baja(): void
    {
        // 1. Seguridad: Solo permitimos peticiones POST para dar de baja
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /autores');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $autor = Autores::find($id);

        // Detectamos si la petición proviene de AJAX
        $esAjax = isset($_GET['ajax']) || (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');

        // 2. Si el registro no existe
        if (!$autor) {
            if ($esAjax) {
                header('Content-Type: application/json');
                http_response_code(404);
                echo json_encode([
                    'ok' => false,
                    'mensaje' => 'El autor no existe o ya fue eliminado'
                ]);
                exit;
            }

            header('Location: /autores');
            exit;
        }

        // 3. Ejecutamos la baja (Soft Delete o cambio de estado)
        $resultado = $autor->baja();

        // 4. Respuesta para AJAX
        if ($esAjax) {
            header('Content-Type: application/json');
            http_response_code($resultado ? 200 : 500);
            echo json_encode([
                'ok' => (bool) $resultado,
                'mensaje' => $resultado ? 'Autor dado de baja correctamente' : 'No se pudo realizar la baja'
            ]);
            exit;
        }

        // 5. Respuesta para navegación tradicional HTML
        header('Location: /autores?exito=3');
        exit;
    }

    public static function alta(): void
    {
        // 1. Seguridad: Solo permitimos POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /autores');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $autor = Autores::find($id);

        // Detectamos si la petición proviene de AJAX
        $esAjax = isset($_GET['ajax']) || (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');

        // 2. Si el registro no existe
        if (!$autor) {
            if ($esAjax) {
                header('Content-Type: application/json');
                http_response_code(404);
                echo json_encode([
                    'ok' => false,
                    'mensaje' => 'El autor no existe o fue eliminado'
                ]);
                exit;
            }

            header('Location: /autores');
            exit;
        }

        // 3. Ejecutamos el alta
        $resultado = $autor->alta();

        // 4. Respuesta para AJAX
        if ($esAjax) {
            header('Content-Type: application/json');
            http_response_code($resultado ? 200 : 500);
            echo json_encode([
                'ok' => (bool) $resultado,
                'mensaje' => $resultado ? 'Autor activado correctamente' : 'No se pudo activar el autor'
            ]);
            exit;
        }

        // 5. Respuesta para navegación tradicional HTML
        header('Location: /autores?exito=4');
        exit;
    }

        /**
     * Muestra el listado de géneros inactivos
     */
    public static function bajas(Router $router): void
    {
        $busqueda = trim($_GET['q'] ?? $_GET['busqueda'] ?? '');
        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));

        // Si tu método de búsqueda o all() permite filtrar por inactivos (por ej. $activo = 0 o false)
        $autores = Autores::all(false); 

        $datos = [
            'titulo'   => 'Autores Inactivos',
            'generos'  => $autores,
            'busqueda' => $busqueda
        ];

        // Detección de petición AJAX
        $esAjax = isset($_GET['ajax']) || (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');

        if ($esAjax) {
            extract($datos);
            require __DIR__ . '/../views/autores/_tabla_bajas.php'; // o _tabla.php según tu estructura
            return;
        }

        $router->render('autores/bajas', $datos);
    }
}