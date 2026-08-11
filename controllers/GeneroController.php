<?php

declare(strict_types=1);

namespace Controllers;

use Model\Generos;
use MVC\Router;

class GeneroController
{
    private const POR_PAGINA = 7;

    public static function index(Router $router): void
    {
        $busqueda = trim($_GET['q'] ?? $_GET['busqueda'] ?? '');
        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));
        $estado = $_GET['estado'] ?? '1'; // Capturamos estado igual que en libros

        // 1. Obtener el total de registros filtrados
        $totalRegistros = Generos::total($busqueda, $estado);

        // 2. Calcular el total de páginas
        $totalPaginas = (int) ceil($totalRegistros / self::POR_PAGINA);
        
        // Ejecuta la búsqueda paginada desde el modelo
        $generos = Generos::listar($busqueda, $pagina, self::POR_PAGINA, $estado);

        // Ejecuta la búsqueda paginada desde el modelo
        // $resultado = Generos::buscarPaginado($busqueda, $pagina, self::POR_PAGINA);

        $datos = [
        'titulo' => 'Géneros',
        'generos' => $generos,
        'busqueda' => $busqueda,
        'pagina' => $pagina,
        'totalPaginas' => $totalPaginas,
        'estado' => $estado
        ];

        // Petición AJAX: devuelve solo el fragmento de la tabla
        $esAjax = isset($_GET['ajax']) || (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');

        if ($esAjax) {
            extract($datos);
            include __DIR__ . '/../views/generos/_tabla.php';
            exit;
        }

        $router->render('generos/index', $datos);
    }

    public static function crear(Router $router): void
    {
        $genero = new Generos();
        $errores = [];

        // Detectamos si la petición es AJAX
        $esAjax = isset($_GET['ajax']) || (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $genero->sincronizar($_POST);
            $errores = $genero->validar();

            if (empty($errores)) {
                $resultado = $genero->guardar();

                if ($resultado) {
                    // RESPUESTA EXITOSA PARA AJAX
                    if ($esAjax) {
                        header('Content-Type: application/json');
                        echo json_encode([
                            'exito' => true,
                            'mensaje' => 'Género creado correctamente',
                            'redireccion' => '/generos?exito=1'
                        ]);
                        exit;
                    }

                    // RESPUESTA TRADICIONAL
                    header('Location: /generos?exito=1');
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
        $router->render('generos/crear', [
            'titulo'  => 'Nuevo Género',
            'genero'   => $genero,
            'errores' => $errores
        ]);
    }

   public static function editar(Router $router): void
    {
        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
        $genero = Generos::find($id);
        $errores = [];

        // Detectamos si la petición proviene de AJAX
        $esAjax = isset($_GET['ajax']) || (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');

        // 1. Si el registro no existe
        if (!$genero) {
            if ($esAjax) {
                header('Content-Type: application/json');
                http_response_code(404);
                echo json_encode([
                    'ok' => false,
                    'mensaje' => 'Género no encontrado'
                ]);
                exit;
            }

            // Si es navegación tradicional, redirigimos
            header('Location: /generos');
            exit;
        }

        // 2. Procesamiento del Formulario (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $genero->sincronizar($_POST);
            $errores = $genero->validar();

            if (empty($errores)) {
                $genero->guardar();

                // Respuesta Éxito
                if ($esAjax) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'ok' => true,
                        'mensaje' => 'Género actualizado correctamente',
                        'redireccion' => '/generos?exito=2'
                    ]);
                    exit;
                }

                header('Location: /generos?exito=2');
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
        $router->render('generos/editar', [
            'titulo'  => 'Editar Género',
            'genero'  => $genero,
            'errores' => $errores
        ]);
    }

    /** Da de baja un género por ID (Soporta AJAX y Formularios Tradicionales) */
    public static function baja(): void
    {
        // 1. Seguridad: Solo permitimos peticiones POST para dar de baja
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /generos');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $genero = Generos::find($id);

        // Detectamos si la petición proviene de AJAX
        $esAjax = isset($_GET['ajax']) || (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');

        // 2. Si el registro no existe
        if (!$genero) {
            if ($esAjax) {
                header('Content-Type: application/json');
                http_response_code(404);
                echo json_encode([
                    'ok' => false,
                    'mensaje' => 'El género no existe o ya fue eliminado'
                ]);
                exit;
            }

            header('Location: /generos');
            exit;
        }

        // 3. Ejecutamos la baja (Soft Delete o cambio de estado)
        $resultado = $genero->baja();

        // 4. Respuesta para AJAX
        if ($esAjax) {
            header('Content-Type: application/json');
            http_response_code($resultado ? 200 : 500);
            echo json_encode([
                'ok' => (bool) $resultado,
                'mensaje' => $resultado ? 'Género dado de baja correctamente' : 'No se pudo realizar la baja'
            ]);
            exit;
        }

        // 5. Respuesta para navegación tradicional HTML
        header('Location: /generos?exito=3');
        exit;
    }

    public static function alta(): void
    {
        // 1. Seguridad: Solo permitimos POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /generos');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $genero = Generos::find($id);

        // Detectamos si la petición proviene de AJAX
        $esAjax = isset($_GET['ajax']) || (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');

        // 2. Si el registro no existe
        if (!$genero) {
            if ($esAjax) {
                header('Content-Type: application/json');
                http_response_code(404);
                echo json_encode([
                    'ok' => false,
                    'mensaje' => 'El género no existe o fue eliminado'
                ]);
                exit;
            }

            header('Location: /generos');
            exit;
        }

        // 3. Ejecutamos el alta
        $resultado = $genero->alta();

        // 4. Respuesta para AJAX
        if ($esAjax) {
            header('Content-Type: application/json');
            http_response_code($resultado ? 200 : 500);
            echo json_encode([
                'ok' => (bool) $resultado,
                'mensaje' => $resultado ? 'Género activado correctamente' : 'No se pudo activar el género'
            ]);
            exit;
        }

        // 5. Respuesta para navegación tradicional HTML
        header('Location: /generos?exito=4');
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
        $generos = Generos::all(false); 

        $datos = [
            'titulo'   => 'Géneros Inactivos',
            'generos'  => $generos,
            'busqueda' => $busqueda
        ];

        // Detección de petición AJAX
        $esAjax = isset($_GET['ajax']) || (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');

        if ($esAjax) {
            extract($datos);
            require __DIR__ . '/../views/generos/_tabla_bajas.php'; // o _tabla.php según tu estructura
            return;
        }

        $router->render('generos/bajas', $datos);
    }
}