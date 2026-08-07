<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

$tituloPagina = 'Catálogo de libros';
$paginaActiva = 'libros';
$descripcionPagina = 'Consulta todos los libros disponibles en Librería Horizonte.';

$busqueda = obtenerParametroGet('q');
$tipoSeleccionado = obtenerParametroGet('tipo');
$ordenSeleccionado = obtenerParametroGet('orden', 'titulo');

$ordenesPermitidos = [
    'titulo' => 't.titulo ASC',
    'precio_asc' => 't.precio IS NULL, t.precio ASC',
    'precio_desc' => 't.precio IS NULL, t.precio DESC',
    'recientes' => 't.fecha_pub DESC',
];

if (!array_key_exists($ordenSeleccionado, $ordenesPermitidos)) {
    $ordenSeleccionado = 'titulo';
}

$libros = [];
$tipos = [];
$totalLibros = 0;
$errorConexion = null;

try {
    $conexion = obtenerConexion();
    $tipos = obtenerTipos($conexion);

    $condiciones = [];
    $parametros = [];

    if ($busqueda !== '') {
        $condiciones[] = '(t.titulo LIKE :busqueda_titulo OR t.notas LIKE :busqueda_notas)';
        $parametros['busqueda_titulo'] = '%' . $busqueda . '%';
        $parametros['busqueda_notas'] = '%' . $busqueda . '%';
    }

    if ($tipoSeleccionado !== '') {
        $condiciones[] = 't.tipo = :tipo';
        $parametros['tipo'] = $tipoSeleccionado;
    }

    $sql = 'SELECT
                t.id_titulo,
                t.titulo,
                t.tipo,
                t.precio,
                t.total_ventas,
                t.notas,
                t.fecha_pub,
                t.contrato,
                p.nombre_pub
            FROM titulos AS t
            LEFT JOIN publicadores AS p ON p.id_pub = t.id_pub';

    if ($condiciones !== []) {
        $sql .= ' WHERE ' . implode(' AND ', $condiciones);
    }

    $sql .= ' ORDER BY ' . $ordenesPermitidos[$ordenSeleccionado];

    $consulta = $conexion->prepare($sql);
    $consulta->execute($parametros);
    $libros = $consulta->fetchAll();

    $totalLibros = (int) $conexion
        ->query('SELECT COUNT(*) FROM titulos')
        ->fetchColumn();
} catch (Throwable $excepcion) {
    $errorConexion = $excepcion->getMessage();
}

// Se usan expresamente count() y sizeof(), según lo solicitado en la práctica.
$totalEncontrados = count($libros);
$totalCategorias = sizeof($tipos);

require __DIR__ . '/includes/header.php';
?>

<section class="hero hero--libros">
    <div class="contenedor hero__contenido">
        <div>
            <span class="etiqueta">Catálogo en línea</span>
            <h1>Encuentra tu próxima gran lectura</h1>
            <p>
                Explora nuestra colección, descubre nuevos temas y elige el libro
                que acompañará tu próxima historia.
            </p>
            <a class="boton boton--claro" href="#catalogo">Explorar catálogo</a>
        </div>
        <div class="hero__libros" aria-hidden="true">
            <span class="libro-decorativo libro-decorativo--uno"></span>
            <span class="libro-decorativo libro-decorativo--dos"></span>
            <span class="libro-decorativo libro-decorativo--tres"></span>
        </div>
    </div>
</section>

<section id="catalogo" class="seccion">
    <div class="contenedor">
        <div class="encabezado-seccion">
            <div>
                <span class="sobretitulo">Nuestra colección</span>
                <h2>Libros disponibles</h2>
                <p>
                    <?= $errorConexion === null
                        ? e("$totalLibros libros en $totalCategorias categorías")
                        : 'Conecta la base de datos para consultar el catálogo.' ?>
                </p>
            </div>
        </div>

        <form class="filtros" method="get" action="index.php">
            <label class="campo campo--busqueda">
                <span>Buscar un libro</span>
                <input
                    type="search"
                    name="q"
                    value="<?= e($busqueda) ?>"
                    placeholder="Título o palabra clave"
                >
            </label>

            <label class="campo">
                <span>Categoría</span>
                <select name="tipo">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($tipos as $tipo): ?>
                        <option
                            value="<?= e($tipo) ?>"
                            <?= $tipo === $tipoSeleccionado ? 'selected' : '' ?>
                        >
                            <?= e(nombreTipo($tipo)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label class="campo">
                <span>Ordenar por</span>
                <select name="orden">
                    <option value="titulo" <?= $ordenSeleccionado === 'titulo' ? 'selected' : '' ?>>
                        Título
                    </option>
                    <option value="precio_asc" <?= $ordenSeleccionado === 'precio_asc' ? 'selected' : '' ?>>
                        Menor precio
                    </option>
                    <option value="precio_desc" <?= $ordenSeleccionado === 'precio_desc' ? 'selected' : '' ?>>
                        Mayor precio
                    </option>
                    <option value="recientes" <?= $ordenSeleccionado === 'recientes' ? 'selected' : '' ?>>
                        Más recientes
                    </option>
                </select>
            </label>

            <button class="boton boton--primario" type="submit">Aplicar filtros</button>

            <?php if ($busqueda !== '' || $tipoSeleccionado !== '' || $ordenSeleccionado !== 'titulo'): ?>
                <a class="boton boton--texto" href="index.php">Limpiar</a>
            <?php endif; ?>
        </form>

        <?php if ($errorConexion !== null): ?>
            <div class="alerta alerta--error" role="alert">
                <strong>No se pudo cargar el catálogo.</strong>
                <span><?= e($errorConexion) ?></span>
            </div>
        <?php elseif ($libros === []): ?>
            <div class="estado-vacio">
                <span aria-hidden="true">⌕</span>
                <h3>No encontramos libros</h3>
                <p>Prueba con otra búsqueda o elimina los filtros seleccionados.</p>
                <a class="boton boton--primario" href="index.php">Ver todos</a>
            </div>
        <?php else: ?>
            <p class="contador-resultados">
                <?= e((string) $totalEncontrados) ?>
                <?= $totalEncontrados === 1 ? 'resultado encontrado' : 'resultados encontrados' ?>
            </p>

            <div class="rejilla-libros">
                <?php foreach ($libros as $libro): ?>
                    <article class="tarjeta-libro">
                        <div class="portada <?= e(claseTipo($libro['tipo'])) ?>">
                            <span class="portada__categoria"><?= e(nombreTipo($libro['tipo'])) ?></span>
                            <strong><?= e($libro['titulo']) ?></strong>
                            <small>Librería Horizonte</small>
                        </div>
                        <div class="tarjeta-libro__contenido">
                            <div class="tarjeta-libro__cabecera">
                                <span class="insignia"><?= e(nombreTipo($libro['tipo'])) ?></span>
                                <?php if ($libro['contrato'] !== '1'): ?>
                                    <span class="insignia insignia--suave">Próximamente</span>
                                <?php endif; ?>
                            </div>
                            <h3><?= e($libro['titulo']) ?></h3>
                            <p class="tarjeta-libro__editorial">
                                <?= e($libro['nombre_pub'] ?: 'Editorial independiente') ?>
                            </p>
                            <p class="tarjeta-libro__descripcion"><?= e($libro['notas']) ?></p>
                            <div class="tarjeta-libro__pie">
                                <strong><?= e(formatearPrecio($libro['precio'])) ?></strong>
                                <span><?= e((string) ($libro['total_ventas'] ?? 0)) ?> ventas</span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="llamada">
    <div class="contenedor llamada__contenido">
        <div>
            <span class="sobretitulo">Conoce las historias detrás de los libros</span>
            <h2>Descubre a nuestros autores</h2>
        </div>
        <a class="boton boton--claro" href="autores.php">Ver autores</a>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
