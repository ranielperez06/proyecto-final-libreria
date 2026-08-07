<?php

declare(strict_types=1);

$configuracion = [
    'app' => [
        'nombre' => 'Librería Horizonte',
        'descripcion' => 'Catálogo en línea de libros y autores',
    ],
    'database' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'port' => (int) (getenv('DB_PORT') ?: 3306),
        'name' => getenv('DB_NAME') ?: 'dblibreria',
        'user' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASS') ?: '',
    ],
];

$archivoLocal = __DIR__ . '/configuracion.local.php';

if (is_file($archivoLocal)) {
    $configuracionLocal = require $archivoLocal;

    if (is_array($configuracionLocal)) {
        $configuracion = array_replace_recursive($configuracion, $configuracionLocal);
    }
}

return $configuracion;
