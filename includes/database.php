<?php

    // Configuración base de datos

    $db_host = $_ENV['DB_HOST'] ?? 'localhost';
    $db_nombre = $_ENV['DB_NAME'] ?? 'biblioteca';
    $db_usuario = $_ENV['DB_USER'] ?? 'root';
    $db_password = $_ENV['DB_PASS'] ?? '';
    $db_puerto = $_ENV['DB_PORT'] ?? '3306'; // <--- Opcional pero recomendado

    try {
        // Crear conexión PDO
        $db = new PDO(
            "mysql:host={$db_host};port={$db_puerto};dbname={$db_nombre};charset=utf8mb4",
            $db_usuario,
            $db_password
        );

        // Configurar errores PDO
        $db->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );
        // Resultados como array asociativo
        $db->setAttribute(
            PDO::ATTR_DEFAULT_FETCH_MODE,
            PDO::FETCH_ASSOC
        );

        } catch(PDOException $e) {

        echo "Error de conexión: " . $e->getMessage();
        exit;
}


