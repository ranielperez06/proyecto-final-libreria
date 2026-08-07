<?php

declare(strict_types=1);

/**
 * Crea una única conexión PDO durante cada solicitud.
 */
function obtenerConexion(): PDO
{
    static $conexion = null;

    if ($conexion instanceof PDO) {
        return $conexion;
    }

    $configuracion = require __DIR__ . '/../config/configuracion.php';
    $baseDatos = $configuracion['database'];

    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
        $baseDatos['host'],
        $baseDatos['port'],
        $baseDatos['name']
    );

    try {
        $conexion = new PDO(
            $dsn,
            $baseDatos['user'],
            $baseDatos['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    } catch (PDOException $excepcion) {
        error_log('Error de conexión MySQL: ' . $excepcion->getMessage());

        throw new RuntimeException(
            'No fue posible conectar con la base de datos. Revisa la configuración de MySQL.',
            0,
            $excepcion
        );
    }

    return $conexion;
}
