<?php

declare(strict_types=1);

namespace Model;

use PDO;
use PDOException;

abstract class ActiveRecord
{
    protected static PDO $db;

    protected static string $tabla = '';

    protected static string $campoBusqueda = 'id';

    protected static array $columnasDB = [];

    protected static array $errores = [];

    public static function setDB(PDO $database): void
    {
        self::$db = $database;
    }

    public static function getDB(): PDO
    {
        return self::$db;
    }

    public static function getErrores(): array
    {
        return static::$errores;
    }

    public function validar(): array
    {
        static::$errores = [];
        return static::$errores;
    }

    public function sincronizar(array $datos): void
{
    foreach ($datos as $key => $value) {

        if ($key === 'id') {
            continue;
        }

        if (property_exists($this, $key)) {
            $this->$key = $this->convertirValor($key, $value);
        }
    }
}

    protected function atributos(): array
    {
        $atributos = [];

        foreach (static::$columnasDB as $columna) {

            if ($columna !== 'id') {
                $atributos[$columna] = $this->$columna;
            }
        }

        return $atributos;
    }

    public function guardar(): bool
    {
        return empty($this->id)
            ? $this->crear()
            : $this->actualizar();
    }

    protected function crear(): bool
    {
        $atributos = $this->atributos();

        $columnas = implode(', ', array_keys($atributos));

        $valores = ':' . implode(', :', array_keys($atributos));

        $sql = "INSERT INTO " . static::$tabla .
            " ($columnas) VALUES ($valores)";

        try {

            $stmt = self::$db->prepare($sql);

            $resultado = $stmt->execute($atributos);

            if ($resultado) {
                $this->id = (int) self::$db->lastInsertId();
            }

            return $resultado;

        } catch (PDOException $e) {

            static::$errores[] = $e->getMessage();
            return false;
        }
    }

    protected function actualizar(): bool
    {
        $atributos = $this->atributos();

        $campos = [];

        foreach ($atributos as $campo => $valor) {
            $campos[] = "$campo = :$campo";
        }

        $sql = "UPDATE " . static::$tabla .
            " SET " . implode(', ', $campos) .
            " WHERE id = :id LIMIT 1";

        $atributos['id'] = $this->id;

        try {

            $stmt = self::$db->prepare($sql);

            return $stmt->execute($atributos);

        } catch (PDOException $e) {

            static::$errores[] = $e->getMessage();
            return false;
        }
    }

    public static function all(bool $activos = true): array
    {
        $sql = "SELECT * FROM " . static::$tabla;

        if ($activos) {
            $sql .= " WHERE activo = 1";
        }

        $sql .= " ORDER BY id DESC";

        return static::consultarSQL($sql);
    }

    public static function find(int $id): ?static
    {
        $sql = "SELECT *
                FROM " . static::$tabla . "
                WHERE id = :id
                LIMIT 1";

        $stmt = self::$db->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        $registro = $stmt->fetch(PDO::FETCH_ASSOC);

        return $registro
            ? static::crearObjeto($registro)
            : null;
    }

    public static function where(string $columna, mixed $valor): array
    {
        $columna = static::validarColumna($columna);

        $sql = "SELECT *
                FROM " . static::$tabla . "
                WHERE $columna = :valor";

        $stmt = self::$db->prepare($sql);

        $stmt->bindValue(':valor', $valor);

        $stmt->execute();

        return static::crearObjetos(
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public static function firstWhere(
        string $columna,
        mixed $valor
    ): ?static {

        return static::where($columna, $valor)[0] ?? null;
    }

    public static function buscar(string $texto): array
    {
        $campo = static::$campoBusqueda;

        $sql = "SELECT *
                FROM " . static::$tabla . "
                WHERE activo = 1
                AND $campo LIKE :texto
                ORDER BY $campo";

        $stmt = self::$db->prepare($sql);

        $stmt->bindValue(
            ':texto',
            "%$texto%"
        );

        $stmt->execute();

        return static::crearObjetos(
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public static function count(): int
    {
        $sql = "SELECT COUNT(*)
                FROM " . static::$tabla . "
                WHERE activo = 1";

        return (int) self::$db
            ->query($sql)
            ->fetchColumn();
    }

    public function baja(): bool
    {
        return $this->cambiarEstado(0);
    }

    public function alta(): bool
    {
        return $this->cambiarEstado(1);
    }

    protected function cambiarEstado(int $estado): bool
    {
        $sql = "UPDATE " . static::$tabla .
            " SET activo = :estado
              WHERE id = :id";

        $stmt = self::$db->prepare($sql);

        return $stmt->execute([
            'estado' => $estado,
            'id' => $this->id
        ]);
    }

    protected static function validarColumna(string $columna): string
    {
        return in_array($columna, static::$columnasDB)
            ? $columna
            : static::$columnasDB[0];
    }

    protected static function consultarSQL(string $sql): array
    {
        $query = self::$db->query($sql);

        return static::crearObjetos(
            $query->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    protected static function crearObjeto(array $registro): static
{
    $objeto = new static;

    foreach ($registro as $key => $value) {

        if (property_exists($objeto, $key)) {
            $objeto->$key = $objeto->convertirValor($key, $value);
        }

    }

    return $objeto;
}

    protected static function crearObjetos(array $registros): array
    {
        $objetos = [];

        foreach ($registros as $registro) {
            $objetos[] = static::crearObjeto($registro);
        }

        return $objetos;
    }

    protected function convertirValor(string $propiedad, mixed $valor): mixed
    {
        if (!property_exists($this, $propiedad)) {
            return $valor;
        }

        $reflection = new \ReflectionProperty($this, $propiedad);
        $tipo = $reflection->getType();

        if (!$tipo instanceof \ReflectionNamedType) {
            return $valor;
        }

        $nombreTipo = $tipo->getName();

        if ($valor === '' && $tipo->allowsNull()) {
            return null;
        }

        return match ($nombreTipo) {
            'int'    => $valor === null ? null : (int) $valor,
            'float'  => $valor === null ? null : (float) $valor,
            'bool'   => (bool) $valor,
            'string' => $valor === null ? '' : (string) $valor,
            default  => $valor,
        };
    }

        public static function buscarPaginado(
        string $texto = '',
        int $pagina = 1,
        int $cantidad = 10
    ): array {
        $campo = static::$campoBusqueda;
        $pagina = max(1, $pagina);
        $offset = ($pagina - 1) * $cantidad;

        // 1. Obtener el TOTAL de registros que coinciden con la búsqueda
        $sqlCount = "SELECT COUNT(*) FROM " . static::$tabla . " WHERE activo = 1";
        if ($texto !== '') {
            $sqlCount .= " AND $campo LIKE :texto";
        }

        $stmtCount = self::$db->prepare($sqlCount);
        if ($texto !== '') {
            $stmtCount->bindValue(':texto', "%$texto%");
        }
        $stmtCount->execute();
        $totalRegistros = (int) $stmtCount->fetchColumn();

        // 2. Obtener solo los registros de la PÁGINA ACTUAL
        $sql = "SELECT * FROM " . static::$tabla . " WHERE activo = 1";
        if ($texto !== '') {
            $sql .= " AND $campo LIKE :texto";
        }
        $sql .= " ORDER BY id DESC LIMIT :cantidad OFFSET :offset";

        $stmt = self::$db->prepare($sql);

        if ($texto !== '') {
            $stmt->bindValue(':texto', "%$texto%");
        }

        $stmt->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $registros = static::crearObjetos($stmt->fetchAll(PDO::FETCH_ASSOC));

        // 3. Retornar los registros junto con la metadata que necesita el controlador
        return [
            'registros' => $registros,
            'total'     => $totalRegistros,
            'pagina'    => $pagina,
            'paginas'   => (int) ceil($totalRegistros / $cantidad)
        ];
    }
}