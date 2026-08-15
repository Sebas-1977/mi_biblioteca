<?php

declare(strict_types=1);

namespace Model;

use PDO;

class Usuarios extends ActiveRecord
{
    protected static string $tabla = 'usuarios';
    protected static string $campoBusqueda = 'email';

    protected static array $columnasDB = [
        'id',
        'nombre',
        'apellido',
        'email',
        'password',
        'token',
        'confirmado'
    ];

    public ?int $id = null;
    public string $nombre = '';
    public string $apellido = '';
    public string $email = '';
    public string $password = '';
    public string $password2 = '';
    public ?string $token = null;
    public int $confirmado = 0;

    public function __construct(array $args = [])
    {
        $this->id = isset($args['id']) && $args['id'] !== '' ? (int) $args['id'] : null;
        $this->nombre = trim($args['nombre'] ?? '');
        $this->apellido = trim($args['apellido'] ?? '');
        $this->email = trim($args['email'] ?? '');
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->token = $args['token'] ?? null;
        $this->confirmado = isset($args['confirmado']) ? (int) $args['confirmado'] : 0;
    }

    // Validación básica de formato e ingreso de contraseña
    public function validarPassword(): array
    {
        if (empty($this->password)) {
            self::setAlerta('error', 'El password no puede ir vacío');
        } elseif (strlen($this->password) < 6) {
            self::setAlerta('error', 'El password debe tener al menos 6 caracteres');
        }

        return static::$alertas;
    }

    // Validación extendida para registro / cambio con confirmación
    public function validarPasswordConfirmado(): array
    {
        $this->validarPassword();

        if ($this->password !== '' && $this->password !== $this->password2) {
            self::setAlerta('error', 'Los passwords no coinciden');
        }

        return static::$alertas;
    }

    public function validarEmail(): array
    {
        if ($this->email === '') {
            self::setAlerta('error', 'El email es obligatorio');
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::setAlerta('error', 'El email no es válido');
        }

        return static::$alertas;
    }

    // Validación para Login (sin exigir password2)
    public function validarLogin(): array
    {
        static::$alertas = [];

        $this->validarEmail();
        $this->validarPassword();

        return static::$alertas;
    }

    // Validación para Nueva Cuenta
    public function validarNuevaCuenta(): array
    {
        static::$alertas = [];

        if ($this->nombre === '') {
            self::setAlerta('error', 'El nombre es obligatorio');
        }

        if ($this->apellido === '') {
            self::setAlerta('error', 'El apellido es obligatorio');
        }

        $this->validarEmail();
        $this->validarPasswordConfirmado();

        return static::$alertas;
    }

    public function hashPassword(): void
    {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function crearToken(): void
    {
        // Genera 15 caracteres aleatorios criptográficamente seguros para VARCHAR(15)
        $this->token = substr(bin2hex(random_bytes(10)), 0, 15);
    }

//     public function crearToken(): void
//    {
//        $this->token = substr(md5(uniqid((string) rand(), true)), 0, 15);
//   }

    public function comprobarPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    public static function total(string $busqueda = ''): int
    {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE 1 = 1";

        if ($busqueda !== '') {
            $sql .= " AND (
                nombre LIKE :busqueda
                OR apellido LIKE :busqueda
                OR email LIKE :busqueda
            )";
        }

        $stmt = self::$db->prepare($sql);

        if ($busqueda !== '') {
            $stmt->bindValue(':busqueda', '%' . trim($busqueda) . '%');
        }

        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public static function listar(
        string $busqueda = '',
        int $pagina = 1,
        int $porPagina = 10
    ): array {
        $offset = ($pagina - 1) * $porPagina;

        $sql = "SELECT * FROM usuarios WHERE 1 = 1 ";

        if ($busqueda !== '') {
            $sql .= " AND (
                nombre LIKE :busqueda
                OR apellido LIKE :busqueda
                OR email LIKE :busqueda
            ) ";
        }

        $sql .= " ORDER BY id DESC LIMIT :limite OFFSET :offset ";

        $stmt = self::$db->prepare($sql);

        if ($busqueda !== '') {
            $stmt->bindValue(':busqueda', '%' . trim($busqueda) . '%');
        }

        $stmt->bindValue(':limite', $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        return static::crearObjetos($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}