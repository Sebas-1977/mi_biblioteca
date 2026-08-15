<?php

require __DIR__ . '/../vendor/autoload.php';

// Inicializar Dotenv para cargar el archivo .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

require 'funciones.php';
require 'database.php';

// Conectamos a la base de datos
use Model\ActiveRecord;
ActiveRecord::setDB($db);