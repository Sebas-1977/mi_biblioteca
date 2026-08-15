<?php

// Configuración base de datos
$db_host = $_ENV['DB_HOST'] ?? 'localhost';
$db_nombre = $_ENV['DB_NAME'] ?? 'biblioteca';
$db_usuario = $_ENV['DB_USER'] ?? 'root';
$db_password = $_ENV['DB_PASS'] ?? '';
$db_puerto = $_ENV['DB_PORT'] ?? '3306';

try {
    // Si estamos conectando a Aiven (puerto distinto al default 3306), exigimos SSL en la DSN
    $ssl_param = ($db_puerto !== '3306') ? ';sslmode=verify-ca' : '';

    $dsn = "mysql:host={$db_host};port={$db_puerto};dbname={$db_nombre};charset=utf8mb4{$ssl_param}";

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    $db = new PDO($dsn, $db_usuario, $db_password, $options);

} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit;
}