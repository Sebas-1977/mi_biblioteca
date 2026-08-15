<?php

declare(strict_types=1);

namespace Model;

use PDO;

class Libros extends ActiveRecord
{
    protected static string $tabla = 'libros';
    protected static string $campoBusqueda = 'titulo';

    protected static array $columnasDB = [
        'id',
        'titulo',
        'autor_id',
        'genero_id',
        'anio',
        'paginas',
        'estado',
        'portada',
        'activo',
        'usuario_id'
    ];

    public ?int $id = null;
    public string $titulo = '';
    public ?int $autor_id = null;
    public ?int $genero_id = null;
    public ?int $anio = null;
    public ?int $paginas = null;
    public string $estado = 'pendiente';
    public ?string $portada = null;
    public int $activo = 1;
    public ?int $usuario_id = null;

    public ?string $autor_nombre = null;
    public ?string $autor_apellido = null;
    public ?string $genero_nombre = null;

    public function __construct(array $args = [])
    {
        $this->id = isset($args['id']) && $args['id'] !== '' ? (int) $args['id'] : null;
        $this->titulo = trim($args['titulo'] ?? '');
        $this->autor_id = isset($args['autor_id']) && $args['autor_id'] !== '' ? (int) $args['autor_id'] : null;
        $this->genero_id = isset($args['genero_id']) && $args['genero_id'] !== '' ? (int) $args['genero_id'] : null;
        $this->anio = isset($args['anio']) && $args['anio'] !== '' ? (int) $args['anio'] : null;
        $this->paginas = isset($args['paginas']) && $args['paginas'] !== '' ? (int) $args['paginas'] : null;
        $this->estado = $args['estado'] ?? 'pendiente';
        $this->portada = $args['portada'] ?? null;
        $this->activo = isset($args['activo']) ? (int) $args['activo'] : 1;
        $this->usuario_id = isset($args['usuario_id']) && $args['usuario_id'] !== '' ? (int) $args['usuario_id'] : null;

        $this->autor_nombre = $args['autor_nombre'] ?? null;
        $this->autor_apellido = $args['autor_apellido'] ?? null;
        $this->genero_nombre = $args['genero_nombre'] ?? null;
    }

    public function validar(): array
    {
        static::$alertas = [];

        if (trim($this->titulo) === '') {
            self::setAlerta('error', 'El título es obligatorio');
        }

        if (!$this->autor_id) {
            self::setAlerta('error', 'El autor es obligatorio');
        }

        if (!$this->genero_id) {
            self::setAlerta('error', 'El género es obligatorio');
        }

        $anioActual = (int) date('Y');
        if ($this->anio !== null && ($this->anio < 0 || $this->anio > $anioActual)) {
            self::setAlerta('error', "El año debe estar entre 0 y {$anioActual}");
        }

        if ($this->paginas !== null && $this->paginas < 1) {
            self::setAlerta('error', 'La cantidad de páginas no es válida');
        }

        $estadosPermitidos = ['pendiente', 'en_progreso', 'leido'];
        if (!in_array($this->estado, $estadosPermitidos, true)) {
            self::setAlerta('error', 'Estado inválido');
        }

        return static::$alertas;
    }

    /**
     * Cuenta el total de libros pertenecientes al usuario actual según filtros.
     */
    public static function total(string $busqueda = '', string|int $activo = '1', int $usuarioId = 0): int 
    {
        $usuarioId = $usuarioId ?: (int) ($_SESSION['id'] ?? $_SESSION['usuario_id'] ?? 0);
        
        $sql = "SELECT COUNT(*) FROM libros
                LEFT JOIN autores ON autores.id = libros.autor_id
                LEFT JOIN generos ON generos.id = libros.genero_id
                WHERE libros.usuario_id = :usuario_id";

        if ($activo !== 'todos') {
            $sql .= " AND libros.activo = :activo";
        }

        if ($busqueda !== '') {
            $sql .= " AND (
                libros.titulo LIKE :busqueda
                OR autores.nombre LIKE :busqueda
                OR autores.apellido LIKE :busqueda
                OR generos.nombre LIKE :busqueda
            )";
        }

        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);

        if ($activo !== 'todos') {
            $stmt->bindValue(':activo', (int)$activo, PDO::PARAM_INT);
        }

        if ($busqueda !== '') {
            $stmt->bindValue(':busqueda', '%' . trim($busqueda) . '%');
        }

        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    /**
     * Lista los libros del usuario logueado con paginación, filtros y relaciones.
     */
    public static function listar(
        string $busqueda = '',
        int $pagina = 1,
        int $porPagina = 10,
        string|int $activo = '1',
        int $usuarioId = 0
    ): array {
        $usuarioId = $usuarioId ?: (int) ($_SESSION['id'] ?? $_SESSION['usuario_id'] ?? 0);

        $offset = ($pagina - 1) * $porPagina;

        $sql = "SELECT 
                    libros.*,
                    autores.nombre AS autor_nombre,
                    autores.apellido AS autor_apellido,
                    generos.nombre AS genero_nombre
                FROM libros
                LEFT JOIN autores ON autores.id = libros.autor_id
                LEFT JOIN generos ON generos.id = libros.genero_id
                WHERE libros.usuario_id = :usuario_id ";

        if ($activo !== 'todos') {
            $sql .= " AND libros.activo = :activo ";
        }

        if ($busqueda !== '') {
            $sql .= " AND (
                libros.titulo LIKE :busqueda
                OR autores.nombre LIKE :busqueda
                OR autores.apellido LIKE :busqueda
                OR generos.nombre LIKE :busqueda
            ) ";
        }

        $sql .= " ORDER BY libros.id DESC LIMIT :limite OFFSET :offset ";

        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);

        if ($activo !== 'todos') {
            $stmt->bindValue(':activo', (int)$activo, PDO::PARAM_INT);
        }

        if ($busqueda !== '') {
            $stmt->bindValue(':busqueda', '%' . trim($busqueda) . '%');
        }

        $stmt->bindValue(':limite', $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        return static::crearObjetos($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public static function obtenerPublicos(int $limite = 8): array 
    {
        $sql = "SELECT 
                    libros.*, 
                    autores.nombre AS autor_nombre, 
                    autores.apellido AS autor_apellido, 
                    generos.nombre AS genero_nombre
                FROM libros
                INNER JOIN usuarios ON usuarios.id = libros.usuario_id
                LEFT JOIN autores ON autores.id = libros.autor_id
                LEFT JOIN generos ON generos.id = libros.genero_id
                WHERE libros.activo = 1 AND usuarios.admin = 1
                ORDER BY libros.id DESC 
                LIMIT :limite";

        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':limite', $limite, \PDO::PARAM_INT);
        $stmt->execute();

        return static::crearObjetos($stmt->fetchAll(\PDO::FETCH_ASSOC));
    }
}