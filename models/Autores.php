<?php

declare(strict_types=1);

namespace Model;

use PDO;

class Autores extends ActiveRecord
{
    protected static string $tabla = 'autores';
    protected static string $campoBusqueda = 'nombre';

    protected static array $columnasDB = [
        'id',
        'nombre',
        'apellido',
        'nacionalidad',
        'fecha_nacimiento',
        'activo'
    ];

    public ?int $id = null;
    public string $nombre = '';
    public string $apellido = '';
    public ?string $nacionalidad = null;
    public ?string $fecha_nacimiento = null;
    public int $activo = 1;

    public function __construct(array $args = [])
    {
        $this->id = isset($args['id']) && $args['id'] !== '' ? (int) $args['id'] : null;
        $this->nombre = trim($args['nombre'] ?? '');
        $this->apellido = trim($args['apellido'] ?? '');
        $this->nacionalidad = isset($args['nacionalidad']) && trim($args['nacionalidad']) !== '' 
            ? trim($args['nacionalidad']) 
            : null;
        $this->fecha_nacimiento = isset($args['fecha_nacimiento']) && trim($args['fecha_nacimiento']) !== '' 
            ? trim($args['fecha_nacimiento']) 
            : null;
        $this->activo = isset($args['activo']) ? (int) $args['activo'] : 1;
    }

    public function validar(): array
    {
        static::$alertas = [];

        if (trim($this->nombre) === '') {
            self::setAlerta('error', 'El nombre es obligatorio');
        }

        if (trim($this->apellido) === '') {
            self::setAlerta('error', 'El apellido es obligatorio');
        }

        if ($this->fecha_nacimiento !== null && $this->fecha_nacimiento !== '') {
            $fecha = strtotime($this->fecha_nacimiento);
            if (!$fecha || $fecha > time()) {
                self::setAlerta('error', 'La fecha de nacimiento no es válida');
            }
        }

        return static::$alertas;
    }

    /**
     * Cuenta el total de autores registrados en el catálogo global.
     */
    public static function total(string $busqueda = '', string|int $activo = '1'): int 
    {
        $sql = "SELECT COUNT(*) FROM autores WHERE 1=1";

        if ($activo !== 'todos') {
            $sql .= " AND autores.activo = :activo";
        }

        if ($busqueda !== '') {
            $sql .= " AND (nombre LIKE :busqueda OR apellido LIKE :busqueda OR nacionalidad LIKE :busqueda)";
        }

        $stmt = self::$db->prepare($sql);

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
     * Lista autores del catálogo global con búsqueda, paginación y filtro de estado.
     */
    public static function listar(
        string $busqueda = '',
        int $pagina = 1,
        int $porPagina = 10,
        string|int $activo = '1'
    ): array {
        $offset = ($pagina - 1) * $porPagina;

        $sql = "SELECT autores.* FROM autores WHERE 1=1";

        if ($activo !== 'todos') {
            $sql .= " AND autores.activo = :activo";
        }

        if ($busqueda !== '') {
            $sql .= " AND (
                autores.nombre LIKE :busqueda
                OR autores.apellido LIKE :busqueda
                OR autores.nacionalidad LIKE :busqueda
            )";
        }

        $sql .= " ORDER BY autores.id DESC LIMIT :limite OFFSET :offset";

        $stmt = self::$db->prepare($sql);

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

    /**
     * Devuelve el nombre completo del autor.
     */
    public function getNombreCompleto(): string
    {
        return trim($this->nombre . ' ' . $this->apellido);
    }
}