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
        $this->id = $args['id'] ?? null;

        $this->nombre = $args['nombre'] ?? '';

        $this->apellido = $args['apellido'] ?? '';

        $this->nacionalidad = $args['nacionalidad'] ?? null;

        $this->fecha_nacimiento = $args['fecha_nacimiento'] ?? null;

        $this->activo = $args['activo'] ?? 1;
    }


    public function validar(): array
    {
        static::$errores = [];

        if (trim($this->nombre) === '') {
            static::$errores[] = 'El nombre es obligatorio';
        }

        if (trim($this->apellido) === '') {
            static::$errores[] = 'El apellido es obligatorio';
        }

        if ($this->fecha_nacimiento !== null && $this->fecha_nacimiento !== '') {
            // Validar que la fecha no sea superior a la fecha actual
            $fecha = strtotime($this->fecha_nacimiento);
            if (!$fecha || $fecha > time()) {
                static::$errores[] = 'La fecha de nacimiento no es válida';
            }
        }

        return static::$errores;
    }

    /**
 * Cuenta el total de autores según filtros para calcular las páginas.
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
 * Lista autores con opción de búsqueda, paginación y filtro de estado.
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