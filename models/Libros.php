<?php

declare(strict_types=1);

namespace Model;

Use PDO;

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
        'activo'
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

    public ?string $autor_nombre = null;
    public ?string $autor_apellido = null;
    public ?string $genero_nombre = null;

       public function __construct(array $args = [])
    {
        $this->id = isset($args['id']) && $args['id'] !== ''
            ? (int) $args['id']
            : null;

        $this->titulo = trim($args['titulo'] ?? '');

        $this->autor_id = isset($args['autor_id']) && $args['autor_id'] !== ''
            ? (int) $args['autor_id']
            : null;

        $this->genero_id = isset($args['genero_id']) && $args['genero_id'] !== ''
            ? (int) $args['genero_id']
            : null;

        $this->anio = isset($args['anio']) && $args['anio'] !== ''
            ? (int) $args['anio']
            : null;

        $this->paginas = isset($args['paginas']) && $args['paginas'] !== ''
            ? (int) $args['paginas']
            : null;

        $this->estado = $args['estado'] ?? 'pendiente';

        $this->portada = $args['portada'] ?? null;

        $this->activo = isset($args['activo'])
            ? (int) $args['activo']
            : 1;

        // --- AGREGAR ESTAS TRES LÍNEAS ---
        $this->autor_nombre = $args['autor_nombre'] ?? null;
        $this->autor_apellido = $args['autor_apellido'] ?? null;
        $this->genero_nombre = $args['genero_nombre'] ?? null;
    }
    


    public function validar(): array
    {
        static::$errores = [];

        if (trim($this->titulo) === '') {
            static::$errores[] = 'El título es obligatorio';
        }

        if ($this->anio !== null) {
            $anioActual = (int) date('Y');
            if (
                $this->anio < 0 ||
                $this->anio > $anioActual
            ) {
                static::$errores[] = 'El año no es válido';
            }
        }

        if ($this->paginas !== null && $this->paginas < 1) {
            static::$errores[] = 'La cantidad de páginas no es válida';
        }

        $estadosPermitidos = [
            'pendiente',
            'en_progreso',
            'leido'
        ];

        if (!in_array($this->estado, $estadosPermitidos)) {
            static::$errores[] = 'Estado inválido';
        }

        if (!$this->titulo) {
        static::$errores[] = 'El título es obligatorio';
        }
        
        // Validación del año (ej. entre el año 0 y el año actual)
        $anioActual = (int) date('Y');
        if ($this->anio && ($this->anio < 0 || $this->anio > $anioActual)) {
            static::$errores[] = "El año debe estar entre 0 y {$anioActual}";
        }

        return static::$errores;
    }

/**
 * Lista libros filtrados por estado ('1', '0' o 'todos')
 */
public static function listar(
    string $busqueda = '',
    int $pagina = 1,
    int $porPagina = 10,
    string|int $activo = '1' // '1', '0' o 'todos'
): array {
    $offset = ($pagina - 1) * $porPagina;

    $sql = "SELECT 
                libros.*,
                autores.nombre AS autor_nombre,
                autores.apellido AS autor_apellido,
                generos.nombre AS genero_nombre
            FROM libros
            LEFT JOIN autores ON autores.id = libros.autor_id
            LEFT JOIN generos ON generos.id = libros.genero_id
            WHERE 1 = 1 ";

    // Si no pide 'todos', filtramos por la columna activo
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