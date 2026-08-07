<?php

declare(strict_types=1);

$tituloPagina = $tituloPagina ?? 'Inicio';
$paginaActiva = $paginaActiva ?? '';
$descripcionPagina = $descripcionPagina ?? $configuracion['app']['descripcion'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($descripcionPagina) ?>">
    <title><?= e($tituloPagina) ?> | <?= e($configuracion['app']['nombre']) ?></title>
    <link rel="stylesheet" href="assets/css/estilos.css">
    <script src="assets/js/app.js" defer></script>
</head>
<body>
    <a class="saltar-contenido" href="#contenido">Saltar al contenido</a>

    <header class="encabezado">
        <div class="contenedor barra-navegacion">
            <a class="marca" href="index.php" aria-label="Ir al catálogo">
                <span class="marca__simbolo" aria-hidden="true">LH</span>
                <span>
                    <strong>Librería Horizonte</strong>
                    <small>Historias que inspiran</small>
                </span>
            </a>

            <button
                class="boton-menu"
                type="button"
                aria-expanded="false"
                aria-controls="menu-principal"
                aria-label="Abrir menú de navegación"
            >
                <span></span><span></span><span></span>
            </button>

            <nav id="menu-principal" class="menu-principal" aria-label="Navegación principal">
                <a class="<?= $paginaActiva === 'libros' ? 'activo' : '' ?>" href="index.php">
                    Libros
                </a>
                <a class="<?= $paginaActiva === 'autores' ? 'activo' : '' ?>" href="autores.php">
                    Autores
                </a>
                <a class="<?= $paginaActiva === 'contacto' ? 'activo' : '' ?>" href="contacto.php">
                    Contacto
                </a>
            </nav>
        </div>
    </header>

    <main id="contenido">
