<?php

declare(strict_types=1);

namespace Model;

use PDO;

class Generos extends ActiveRecord
{
    protected static string $tabla = 'generos';
    protected static string $campoBusqueda = 'nombre';

    protected static array $columnasDB = [
        'id',
        'nombre',
        'descripcion',
        'activo'
    ];

    public ?int $id = null;
    public string $nombre = '';
    public ?string $descripcion = null;
    public int $activo = 1;

    public function __construct(array $args = [])
    {
        $this->id = isset($args['id']) && $args['id'] !== '' ? (int) $args['id'] : null;
        $this->nombre = trim($args['nombre'] ?? '');
        $this->descripcion = isset($args['descripcion']) && trim($args['descripcion']) !== '' 
            ? trim($args['descripcion']) 
            : null;
        $this->activo = isset($args['activo']) ? (int) $args['activo'] : 1;
    }

    public function validar(): array
    {
        static::$alertas = [];

        if (trim($this->nombre) === '') {
            self::setAlerta('error', 'El nombre del género es obligatorio');
        }

        return static::$alertas;
    }

    /**
     * Cuenta el total de géneros registrados en el catálogo global según filtros.
     */
    public static function total(string $busqueda = '', string|int $activo = '1'): int 
    {
        $sql = "SELECT COUNT(*) FROM generos WHERE 1=1";

        if ($activo !== 'todos') {
            $sql .= " AND generos.activo = :activo";
        }

        if ($busqueda !== '') {
            $sql .= " AND (generos.nombre LIKE :busqueda OR generos.descripcion LIKE :busqueda)";
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
     * Lista géneros del catálogo global con opción de búsqueda, paginación y filtro de estado.
     */
    public static function listar(
        string $busqueda = '',
        int $pagina = 1,
        int $porPagina = 10,
        string|int $activo = '1'
    ): array {
        $offset = ($pagina - 1) * $porPagina;

        $sql = "SELECT generos.* FROM generos WHERE 1=1";

        if ($activo !== 'todos') {
            $sql .= " AND generos.activo = :activo";
        }

        if ($busqueda !== '') {
            $sql .= " AND (
                generos.nombre LIKE :busqueda
                OR generos.descripcion LIKE :busqueda
            )";
        }

        $sql .= " ORDER BY generos.id DESC LIMIT :limite OFFSET :offset";

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
}