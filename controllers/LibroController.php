<?php

declare(strict_types=1);

namespace Controllers;

use Model\Libros;
use Model\Autores;
use Model\Generos;
use Classes\Auth;
use MVC\Router;

class LibroController
{
    private const POR_PAGINA = 5;
    private const CARPETA_PORTADAS = __DIR__ . '/../public/img/portadas/';

    public static function index(Router $router): void
    {
        Auth::isAuth();
        $usuarioId = (int) ($_SESSION['id'] ?? 0);

        $busqueda = trim($_GET['q'] ?? $_GET['busqueda'] ?? '');
        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));
        $estado = $_GET['estado'] ?? '1';

        $totalRegistros = Libros::total($busqueda, $estado, $usuarioId);
        $totalPaginas = (int) ceil($totalRegistros / self::POR_PAGINA);

        $libros = Libros::listar(
            $busqueda,
            $pagina,
            self::POR_PAGINA,
            $estado,
            $usuarioId
        );

        $datos = [
            'titulo'       => 'Libros',
            'libros'       => $libros,
            'busqueda'     => $busqueda,
            'pagina'       => $pagina,
            'totalPaginas' => $totalPaginas,
            'estado'       => $estado
        ];

        if (self::esAjax()) {
            extract($datos);
            require __DIR__ . '/../views/libros/_tabla.php';
            return;
        }

        $router->render('libros/index', $datos);
    }

    public static function crear(Router $router): void
    {
        Auth::isAuth();
        $usuarioId = (int) ($_SESSION['id'] ?? 0);

        $libro = new Libros();
        $alertas = [];
        $esAjax = self::esAjax();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $_POST;
            $datos['usuario_id'] = $usuarioId;

            // procesarPortada() ya devuelve la ruta local (/img/portadas/xxx.jpg) 
            // o la URL HTTPS de Cloudinary (https://res.cloudinary.com/...)
            $portadaProcesada = self::procesarPortada($_FILES['portada'] ?? null);
            if ($portadaProcesada) {
                $datos['portada'] = $portadaProcesada;
            }

            $libro->sincronizar($datos);
            $alertas = $libro->validar();

            if (empty($alertas)) {
                if ($libro->guardar()) {
                    if ($esAjax) {
                        self::responderJson([
                            'exito'       => true,
                            'ok'          => true,
                            'mensaje'     => 'Libro creado correctamente',
                            'redireccion' => '/libros?exito=1'
                        ]);
                    }

                    header('Location: /libros?exito=1');
                    exit;
                }
            } else {
                    // Si hubo error de validación y se llegó a subir la imagen, se borra
                if ($portadaProcesada && !empty($datos['portada'])) {
                    self::eliminarArchivoPortada($datos['portada']);
                    $libro->portada = '';
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

        // Carga el catálogo global de autores activos
        $autores = Autores::where('activo', 1);
        $generos = Generos::where('activo', 1);

        $router->render('libros/crear', [
            'titulo'  => 'Nuevo Libro',
            'libro'   => $libro,
            'autores' => $autores,
            'generos' => $generos,
            'alertas' => $alertas,
            'errores' => $alertas
        ]);
    }

    public static function editar(Router $router): void
    {
        Auth::isAuth();
        $usuarioId = (int) ($_SESSION['id'] ?? 0);

        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
        $libro = Libros::find($id);
        $alertas = [];
        $esAjax = self::esAjax();

        if (!$libro || (int) $libro->usuario_id !== $usuarioId) {
            if ($esAjax) {
                self::responderJson([
                    'exito'   => false,
                    'ok'      => false,
                    'mensaje' => 'Libro no encontrado'
                ], 404);
            }

            header('Location: /libros');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $_POST;
            $datos['usuario_id'] = $usuarioId;
            $imagenAnterior = $libro->portada;

            $portadaProcesada = self::procesarPortada($_FILES['portada'] ?? null);

            if ($portadaProcesada) {
                $datos['portada'] = $portadaProcesada;
            } else {
                $datos['portada'] = $imagenAnterior;
            }

            $libro->sincronizar($datos);
            $alertas = $libro->validar();

            if (empty($alertas)) {
                if ($libro->guardar()) {
                    // Si se subió una nueva portada y había una anterior diferente, elimina la vieja
                    if ($portadaProcesada && !empty($imagenAnterior) && $imagenAnterior !== $datos['portada']) {
                        self::eliminarArchivoPortada($imagenAnterior);
                    }

                    if ($esAjax) {
                        self::responderJson([
                            'exito'       => true,
                            'ok'          => true,
                            'mensaje'     => 'Libro actualizado correctamente',
                            'redireccion' => '/libros?exito=2'
                        ]);
                    }

                    header('Location: /libros?exito=2');
                    exit;
                }
            } else {
                // Si la validación falla y se subió un archivo nuevo, borra el archivo nuevo subido
                if ($portadaProcesada) {
                    self::eliminarArchivoPortada($datos['portada']);
                    $libro->portada = $imagenAnterior;
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

        // Carga el catálogo global de autores activos
        $autores = Autores::where('activo', 1);
        $generos = Generos::where('activo', 1);

        $router->render('libros/editar', [
            'titulo'  => 'Editar Libro',
            'libro'   => $libro,
            'autores' => $autores,
            'generos' => $generos,
            'alertas' => $alertas,
            'errores' => $alertas
        ]);
    }

    public static function baja(): void
    {
        Auth::isAuth();
        $usuarioId = (int) ($_SESSION['id'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /libros');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $libro = Libros::find($id);
        $esAjax = self::esAjax();

        if (!$libro || (int) $libro->usuario_id !== $usuarioId) {
            if ($esAjax) {
                self::responderJson([
                    'exito'   => false,
                    'ok'      => false,
                    'mensaje' => 'El libro no existe o no tienes permisos para modificarlo'
                ], 404);
            }

            header('Location: /libros');
            exit;
        }

        $resultado = $libro->baja();

        if ($esAjax) {
            self::responderJson([
                'exito'   => (bool) $resultado,
                'ok'      => (bool) $resultado,
                'mensaje' => $resultado ? 'Libro dado de baja correctamente' : 'No se pudo realizar la baja'
            ], $resultado ? 200 : 500);
        }

        header('Location: /libros?exito=3');
        exit;
    }

    public static function alta(): void
    {
        Auth::isAuth();
        $usuarioId = (int) ($_SESSION['id'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /libros');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $libro = Libros::find($id);
        $esAjax = self::esAjax();

        if (!$libro || (int) $libro->usuario_id !== $usuarioId) {
            if ($esAjax) {
                self::responderJson([
                    'exito'   => false,
                    'ok'      => false,
                    'mensaje' => 'El libro no existe o no tienes permisos para modificarlo'
                ], 404);
            }

            header('Location: /libros');
            exit;
        }

        $resultado = $libro->alta();

        if ($esAjax) {
            self::responderJson([
                'exito'   => (bool) $resultado,
                'ok'      => (bool) $resultado,
                'mensaje' => $resultado ? 'Libro activado correctamente' : 'No se pudo activar el libro'
            ], $resultado ? 200 : 500);
        }

        header('Location: /libros?exito=4');
        exit;
    }

    public static function bajas(Router $router): void
    {
        Auth::isAuth();
        $usuarioId = (int) ($_SESSION['id'] ?? 0);

        $busqueda = trim($_GET['q'] ?? $_GET['busqueda'] ?? '');
        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));

        $libros = Libros::listar(
            $busqueda,
            $pagina,
            self::POR_PAGINA,
            '0',
            $usuarioId
        );

        $datos = [
            'titulo'   => 'Libros Inactivos',
            'libros'   => $libros,
            'busqueda' => $busqueda,
            'pagina'   => $pagina
        ];

        if (self::esAjax()) {
            extract($datos);
            require __DIR__ . '/../views/libros/_tabla_bajas.php';
            return;
        }

        $router->render('libros/bajas', $datos);
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

        private static function procesarPortada(?array $file): ?string
    {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK || empty($file['tmp_name'])) {
            return null;
        }

        $mimeType = mime_content_type($file['tmp_name']);
        $extension = match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            default      => null
        };

        if (!$extension) {
            return null;
        }

        $driver = $_ENV['FS_DRIVER'] ?? 'local';

        // OPCIÓN 1: CLOUDINARY (Render / Producción)
        if ($driver === 'cloudinary') {
            try {
                $cloudinary = new \Cloudinary\Cloudinary([
                    'cloud' => [
                        'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'] ?? '',
                        'api_key'    => $_ENV['CLOUDINARY_API_KEY'] ?? '',
                        'api_secret' => $_ENV['CLOUDINARY_API_SECRET'] ?? '',
                    ],
                    'url' => ['secure' => true]
                ]);

                $respuesta = $cloudinary->uploadApi()->upload($file['tmp_name'], [
                    'folder' => 'portadas'
                ]);

                return $respuesta['secure_url'] ?? null;
            } catch (\Exception $e) {
                error_log("Error Cloudinary: " . $e->getMessage());
                return null;
            }
        }

        // OPCIÓN 2: LOCAL (Servidor local)
        if (!is_dir(self::CARPETA_PORTADAS)) {
            mkdir(self::CARPETA_PORTADAS, 0755, true);
        }

        $nombreUnico = bin2hex(random_bytes(16)) . '.' . $extension;
        $destino = self::CARPETA_PORTADAS . $nombreUnico;

        return move_uploaded_file($file['tmp_name'], $destino) ? '/img/portadas/' . $nombreUnico : null;
    }

    private static function eliminarArchivoPortada(?string $rutaOImagen): void
    {
        if (empty($rutaOImagen)) {
            return;
        }

        // Eliminar de Cloudinary si es un enlace remoto
        if (str_contains($rutaOImagen, 'cloudinary.com')) {
            try {
                $path = parse_url($rutaOImagen, PHP_URL_PATH);
                $partes = explode('/', $path);
                $nombreConExtension = end($partes);
                $publicId = 'portadas/' . pathinfo($nombreConExtension, PATHINFO_FILENAME);

                $cloudinary = new \Cloudinary\Cloudinary([
                    'cloud' => [
                        'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'] ?? '',
                        'api_key'    => $_ENV['CLOUDINARY_API_KEY'] ?? '',
                        'api_secret' => $_ENV['CLOUDINARY_API_SECRET'] ?? '',
                    ]
                ]);

                $cloudinary->uploadApi()->destroy($publicId);
            } catch (\Exception $e) {
                error_log("Error al eliminar de Cloudinary: " . $e->getMessage());
            }
            return;
        }

        // Eliminar de disco local
        $nombreArchivo = basename($rutaOImagen);
        if ($nombreArchivo === '.gitkeep') {
            return;
        }

        $rutaAbsoluta = self::CARPETA_PORTADAS . $nombreArchivo;
        if (file_exists($rutaAbsoluta) && is_file($rutaAbsoluta)) {
            unlink($rutaAbsoluta);
        }
    }
}