<?php

function debuguear(mixed $variable): never{
    echo '<pre>';
    var_dump($variable);
    echo '</pre>';
    exit;
}

// Especifica ENT_QUOTES (escapa comillas simples y dobles);
// Escapa / Sanitizar el HTML
function s(string $html) : string{
    return htmlspecialchars($html, ENT_QUOTES, 'UTF-8'); 
}

function fechaEspañol(?string $fecha): string
{
    if (empty($fecha)) {
        return '';
    }

    $date = new DateTime($fecha);

    return $date->format('d/m/Y');
}

function fechaLargaEspañol(?string $fecha): string
{
    if (empty($fecha)) {
        return '';
    }

    $meses = [
        1 => 'enero',
        2 => 'febrero',
        3 => 'marzo',
        4 => 'abril',
        5 => 'mayo',
        6 => 'junio',
        7 => 'julio',
        8 => 'agosto',
        9 => 'septiembre',
        10 => 'octubre',
        11 => 'noviembre',
        12 => 'diciembre'
    ];

    $date = new DateTime($fecha);

    return $date->format('d') . ' de ' .
           $meses[(int)$date->format('m')] . ' de ' .
           $date->format('Y');
}