<?php

    // Configuración base de datos

    $db_host = 'localhost';
    $db_nombre = 'biblioteca';
    $db_usuario = 'root';
    $db_password = '';

    try {
        // Crear conexión PDO
        $db = new PDO(
            "mysql:host={$db_host};dbname={$db_nombre};charset=utf8mb4",
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


