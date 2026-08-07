<?php

declare(strict_types=1);

/**
 * Escapa valores antes de mostrarlos en HTML.
 */
function e(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function obtenerParametroGet(string $nombre, string $predeterminado = ''): string
{
    // Uso explícito de GET solicitado en la práctica.
    $valor = $_GET[$nombre] ?? $predeterminado;

    return is_string($valor) ? trim($valor) : $predeterminado;
}

function obtenerTipos(PDO $conexion): array
{
    $consulta = $conexion->query(
        'SELECT DISTINCT tipo FROM titulos WHERE tipo <> "" ORDER BY tipo'
    );

    return $consulta->fetchAll(PDO::FETCH_COLUMN);
}

function nombreTipo(string $tipo): string
{
    $nombres = [
        'business' => 'Negocios',
        'mod_cook' => 'Cocina moderna',
        'popular_comp' => 'Computación',
        'psychology' => 'Psicología',
        'trad_cook' => 'Cocina tradicional',
        'UNDECIDED' => 'Próximamente',
    ];

    return $nombres[$tipo] ?? ucfirst(str_replace('_', ' ', $tipo));
}

function claseTipo(string $tipo): string
{
    $clases = [
        'business' => 'portada--negocios',
        'mod_cook' => 'portada--cocina',
        'popular_comp' => 'portada--tecnologia',
        'psychology' => 'portada--psicologia',
        'trad_cook' => 'portada--clasica',
        'UNDECIDED' => 'portada--proximamente',
    ];

    return $clases[$tipo] ?? 'portada--general';
}

function formatearPrecio(float|string|null $precio): string
{
    if ($precio === null || $precio === '') {
        return 'Precio por confirmar';
    }

    return 'US$ ' . number_format((float) $precio, 2, '.', ',');
}

function iniciales(string $nombre, string $apellido): string
{
    $primeraNombre = function_exists('mb_substr')
        ? mb_substr(trim($nombre), 0, 1, 'UTF-8')
        : substr(trim($nombre), 0, 1);
    $primeraApellido = function_exists('mb_substr')
        ? mb_substr(trim($apellido), 0, 1, 'UTF-8')
        : substr(trim($apellido), 0, 1);

    return strtoupper($primeraNombre . $primeraApellido);
}

function longitud(string $valor): int
{
    return function_exists('mb_strlen')
        ? mb_strlen($valor, 'UTF-8')
        : strlen($valor);
}

function tokenCsrf(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function tokenCsrfValido(string $token): bool
{
    return isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function guardarMensaje(string $tipo, string $mensaje): void
{
    $_SESSION['mensaje'] = [
        'tipo' => $tipo,
        'texto' => $mensaje,
    ];
}

function obtenerMensaje(): ?array
{
    $mensaje = $_SESSION['mensaje'] ?? null;
    unset($_SESSION['mensaje']);

    return is_array($mensaje) ? $mensaje : null;
}

function redireccionar(string $ruta): never
{
    header('Location: ' . $ruta);
    exit;
}
