<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$configuracion = require __DIR__ . '/../config/configuracion.php';

require_once __DIR__ . '/funciones.php';
require_once __DIR__ . '/conexion.php';
