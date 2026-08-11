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
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->descripcion = $args['descripcion'] ?? null;
        $this->activo = $args['activo'] ?? 1;
    }

    public function validar(): array
    {
        static::$errores = [];

        if (trim($this->nombre) === '') {
            static::$errores[] = 'El nombre del género es obligatorio';
        }

        return static::$errores;
    }

    /**
     * Cuenta el total de géneros según filtros para la paginación.
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
     * Lista géneros con opción de búsqueda, paginación y filtro de estado.
     */
    public static function listar(
        string $busqueda = '',
        int $pagina = 1,
        int $porPagina = 10,
        string|int $activo = '1' // '1', '0' o 'todos'
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