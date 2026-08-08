<?php

declare(strict_types=1);

namespace Controllers;

use Model\Libros;
use Model\Autores;
use Model\Generos;
use MVC\Router;

class LibroController
{
    private const POR_PAGINA = 7;
    // Ruta absoluta donde se guardarán las imágenes en el servidor
    private const CARPETA_PORTADAS = __DIR__ . '/../public/img/portadas/';

    public static function index(Router $router): void
{
    $busqueda = trim($_GET['q'] ?? $_GET['busqueda'] ?? '');
    $pagina = max(1, (int) ($_GET['pagina'] ?? 1));
    
    // Capturamos el estado activo ('1', '0' o 'todos'). Por defecto '1'
    $estado = $_GET['estado'] ?? '1';

    $libros = Libros::listar(
        $busqueda,
        $pagina,
        self::POR_PAGINA,
        $estado
    );

    $datos = [
        'titulo'   => 'Libros',
        'libros'   => $libros,
        'busqueda' => $busqueda,
        'pagina'   => $pagina,
        'estado'   => $estado
    ];

    $esAjax = isset($_GET['ajax']) || (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');

    if ($esAjax) {
        extract($datos);
        require __DIR__ . '/../views/libros/_tabla.php';
        return;
    }

    $router->render('libros/index', $datos);
}

    public static function crear(Router $router): void
{
    $libro = new Libros();
    $errores = [];

    // Detectamos si la petición es AJAX
    $esAjax = isset($_GET['ajax']) || (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $datos = $_POST;

        // Procesar la imagen subida si existe
        $nombreImagen = self::procesarPortada($_FILES['portada'] ?? null);
        if ($nombreImagen) {
            $datos['portada'] = '/img/portadas/' . $nombreImagen;
        }

        // Sincronizamos el objeto con los datos procesados
        $libro->sincronizar($datos);
        $errores = $libro->validar();

        if (empty($errores)) {
            $resultado = $libro->guardar();

            if ($resultado) {
                // RESPUESTA EXITOSA PARA AJAX
                if ($esAjax) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'exito' => true,
                        'mensaje' => 'Libro creado correctamente',
                        'redireccion' => '/libros'
                    ]);
                    exit;
                }

                // RESPUESTA TRADICIONAL
                header('Location: /libros');
                exit;
            }
        }

        // Si hay errores de validación y se llegó a subir una imagen, la eliminamos
        if (!empty($libro->portada)) {
            self::eliminarArchivoPortada($libro->portada);
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

    // Obtenemos los autores y géneros para poblar los selectores en el formulario
    $autores = Autores::all();
    $generos = Generos::all();

    // Carga de la vista normal (GET o fallback)
    $router->render('libros/crear', [
        'titulo'  => 'Nuevo Libro',
        'libro'   => $libro,
        'autores' => $autores,
        'generos' => $generos,
        'errores' => $errores
    ]);
}

    public static function editar(Router $router): void
    {
        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
        $libro = Libros::find($id);
        $errores = [];

        // Detectamos si la petición proviene de AJAX
        $esAjax = isset($_GET['ajax']) || (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');

        // 1. Si el registro no existe
        if (!$libro) {
            if ($esAjax) {
                header('Content-Type: application/json');
                http_response_code(404);
                echo json_encode([
                    'ok' => false,
                    'mensaje' => 'Libro no encontrado'
                ]);
                exit;
            }

            // Si es navegación tradicional, redirigimos
            header('Location: /libros');
            exit;
        }

        // 2. Procesamiento del Formulario (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $_POST;
            $imagenAnterior = $libro->portada;

            // Procesar si subieron una nueva portada
            $nombreImagen = self::procesarPortada($_FILES['portada'] ?? null);

            if ($nombreImagen) {
                $datos['portada'] = '/img/portadas/' . $nombreImagen;
            } else {
                // Conservar la portada existente si no se seleccionó un archivo nuevo
                $datos['portada'] = $imagenAnterior;
            }

            $libro->sincronizar($datos);
            $errores = $libro->validar();

            if (empty($errores)) {
                $libro->guardar();

                // Si se guardó correctamente y se subió una nueva imagen, eliminamos la anterior
                if ($nombreImagen && !empty($imagenAnterior) && $nombreImagen !== $imagenAnterior) {
                    self::eliminarArchivoPortada($imagenAnterior);
                }

                // Respuesta Éxito para AJAX
                if ($esAjax) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'ok' => true,
                        'mensaje' => 'Libro actualizado correctamente',
                        'redireccion' => '/libros'
                    ]);
                    exit;
                }

                // Respuesta Éxito Tradicional
                header('Location: /libros?exito=1');
                exit;
            }

            // Si fallaron las validaciones y habíamos subido un archivo nuevo, lo borramos para no dejar basura
            if ($nombreImagen) {
                self::eliminarArchivoPortada($datos['portada']);
            }

            // Respuesta Errores de Validación para AJAX
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
        $autores = Autores::all();
        $generos = Generos::all();

        $router->render('libros/editar', [
            'titulo'  => 'Editar Libro',
            'libro'   => $libro,
            'autores' => $autores,
            'generos' => $generos,
            'errores' => $errores
        ]);
    }

    // --- MÉTODOS AUXILIARES PARA MANEJO DE ARCHIVOS ---

    /**
     * Procesa, valida y guarda la portada enviada por $_FILES.
     * Devuelve el nombre único asignado al archivo o null si no se subió uno válido.
     */
    private static function procesarPortada(?array $file): ?string
    {
        // Verificar si se subió un archivo sin errores
        if (!$file || $file['error'] !== UPLOAD_ERR_OK || empty($file['tmp_name'])) {
            return null;
        }

        // 1. Validar el tipo de archivo (MIME) por seguridad
        $mimeType = mime_content_type($file['tmp_name']);
        $extensionesPermitidas = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp'
        ];

        if (!array_key_exists($mimeType, $extensionesPermitidas)) {
            return null; // O puedes retornar false/arrojar excepción si quieres manejar un error específico
        }

        // 2. Crear la carpeta si no existe
        if (!is_dir(self::CARPETA_PORTADAS)) {
            mkdir(self::CARPETA_PORTADAS, 0755, true);
        }

        // 3. Generar un nombre único para evitar colisiones
        $extension = $extensionesPermitidas[$mimeType];
        $nombreUnico = md5((string) uniqid((string) rand(), true)) . '.' . $extension;
        $destino = self::CARPETA_PORTADAS . $nombreUnico;

        // 4. Mover el archivo subido de la carpeta temporal a la ruta final
        if (move_uploaded_file($file['tmp_name'], $destino)) {
            
    return $nombreUnico;

        }

        return null;
    }

    /**
     * Borra el archivo de la imagen del servidor si existe.
     */
    private static function eliminarArchivoPortada(string $nombreArchivo): void
    {
        $nombreArchivo = basename($nombreArchivo);

    $ruta = self::CARPETA_PORTADAS . $nombreArchivo;
        if (file_exists($ruta) && is_file($ruta)) {
            unlink($ruta);
        }
    }

    public static function baja(): void
    {
        // 1. Seguridad: Solo permitimos peticiones POST para dar de baja
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /libros');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $libro = Libros::find($id);

        // Detectamos si la petición proviene de AJAX
        $esAjax = isset($_GET['ajax']) || (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');

        // 2. Si el registro no existe
        if (!$libro) {
            if ($esAjax) {
                header('Content-Type: application/json');
                http_response_code(404);
                echo json_encode([
                    'ok' => false,
                    'mensaje' => 'El libro no existe o ya fue eliminado'
                ]);
                exit;
            }

            header('Location: /libros');
            exit;
        }

        // 3. Ejecutamos la baja (Soft Delete o cambio de estado activo = 0)
        $resultado = $libro->baja();

        // 4. Respuesta para AJAX
        if ($esAjax) {
            header('Content-Type: application/json');
            http_response_code($resultado ? 200 : 500);
            echo json_encode([
                'ok' => (bool) $resultado,
                'mensaje' => $resultado ? 'Libro dado de baja correctamente' : 'No se pudo realizar la baja'
            ]);
            exit;
        }

        // 5. Respuesta para navegación tradicional HTML
        header('Location: /libros');
        exit;
    }

    public static function alta(): void
    {
        // 1. Seguridad: Solo permitimos POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /libros');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $libro = Libros::find($id);

        // Detectamos si la petición proviene de AJAX
        $esAjax = isset($_GET['ajax']) || (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');

        // 2. Si el registro no existe
        if (!$libro) {
            if ($esAjax) {
                header('Content-Type: application/json');
                http_response_code(404);
                echo json_encode([
                    'ok' => false,
                    'mensaje' => 'El libro no existe o fue eliminado'
                ]);
                exit;
            }

            header('Location: /libros');
            exit;
        }

        // 3. Ejecutamos el alta (activo = 1)
        $resultado = $libro->alta();

        // 4. Respuesta para AJAX
        if ($esAjax) {
            header('Content-Type: application/json');
            http_response_code($resultado ? 200 : 500);
            echo json_encode([
                'ok' => (bool) $resultado,
                'mensaje' => $resultado ? 'Libro activado correctamente' : 'No se pudo activar el libro'
            ]);
            exit;
        }

        // 5. Respuesta para navegación tradicional HTML
        header('Location: /libros');
        exit;
    }

    /**
 * Muestra el listado de libros inactivos
 */
public static function bajas(Router $router): void
{
    $busqueda = trim($_GET['q'] ?? $_GET['busqueda'] ?? '');
    $pagina = max(1, (int) ($_GET['pagina'] ?? 1));

    // Pasamos el estado inactivo (0) a tu método listar
    $libros = Libros::listar(
        $busqueda,
        $pagina,
        self::POR_PAGINA,
        0 // Indica que recupere los inactivos (activo = 0)
    );

    $datos = [
        'titulo'   => 'Libros Inactivos',
        'libros'   => $libros,
        'busqueda' => $busqueda,
        'pagina'   => $pagina
    ];

    // Detección de petición AJAX
    $esAjax = isset($_GET['ajax']) || (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');

    if ($esAjax) {
        extract($datos);
        require __DIR__ . '/../views/libros/_tabla_bajas.php';
        return;
    }

    $router->render('libros/bajas', $datos);
}
}