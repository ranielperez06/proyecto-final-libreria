<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

$tituloPagina = 'Autores';
$paginaActiva = 'autores';
$descripcionPagina = 'Conoce el listado completo de autores de nuestra librería.';

$busqueda = obtenerParametroGet('q');
$autores = [];
$totalAutores = 0;
$errorConexion = null;

try {
    $conexion = obtenerConexion();
    $sql = 'SELECT
                id_autor,
                nombre,
                apellido,
                telefono,
                direccion,
                ciudad,
                estado,
                pais,
                cod_postal
            FROM autores';
    $parametros = [];

    if ($busqueda !== '') {
        $sql .= ' WHERE nombre LIKE :busqueda
                  OR apellido LIKE :busqueda
                  OR ciudad LIKE :busqueda
                  OR pais LIKE :busqueda';
        $parametros['busqueda'] = '%' . $busqueda . '%';
    }

    $sql .= ' ORDER BY apellido, nombre';

    $consulta = $conexion->prepare($sql);
    $consulta->execute($parametros);
    $autores = $consulta->fetchAll();

    $totalAutores = (int) $conexion
        ->query('SELECT COUNT(*) FROM autores')
        ->fetchColumn();
} catch (Throwable $excepcion) {
    $errorConexion = $excepcion->getMessage();
}

$totalEncontrados = count($autores);

require __DIR__ . '/includes/header.php';
?>

<section class="hero hero--autores">
    <div class="contenedor hero__contenido hero__contenido--centrado">
        <div>
            <span class="etiqueta">Voces que dejan huella</span>
            <h1>Conoce a nuestros autores</h1>
            <p>
                Explora el directorio de escritores que dan vida a nuestra colección
                y descubre desde dónde nacen sus historias.
            </p>
        </div>
    </div>
</section>

<section class="seccion">
    <div class="contenedor">
        <div class="encabezado-seccion encabezado-seccion--fila">
            <div>
                <span class="sobretitulo">Directorio</span>
                <h2>Autores disponibles</h2>
                <p>
                    <?= $errorConexion === null
                        ? e("$totalAutores autores registrados")
                        : 'Conecta la base de datos para consultar los autores.' ?>
                </p>
            </div>

            <form class="busqueda-compacta" method="get" action="autores.php">
                <label>
                    <span class="solo-lectores">Buscar autor</span>
                    <input
                        type="search"
                        name="q"
                        value="<?= e($busqueda) ?>"
                        placeholder="Nombre, apellido o ciudad"
                    >
                </label>
                <button class="boton boton--primario" type="submit">Buscar</button>
                <?php if ($busqueda !== ''): ?>
                    <a class="boton boton--texto" href="autores.php">Limpiar</a>
                <?php endif; ?>
            </form>
        </div>

        <?php if ($errorConexion !== null): ?>
            <div class="alerta alerta--error" role="alert">
                <strong>No se pudo cargar el directorio.</strong>
                <span><?= e($errorConexion) ?></span>
            </div>
        <?php elseif ($autores === []): ?>
            <div class="estado-vacio">
                <span aria-hidden="true">⌕</span>
                <h3>No encontramos autores</h3>
                <p>Revisa el término de búsqueda e inténtalo nuevamente.</p>
                <a class="boton boton--primario" href="autores.php">Ver todos</a>
            </div>
        <?php else: ?>
            <p class="contador-resultados">
                <?= e((string) $totalEncontrados) ?>
                <?= $totalEncontrados === 1 ? 'autor encontrado' : 'autores encontrados' ?>
            </p>

            <div class="rejilla-autores">
                <?php foreach ($autores as $autor): ?>
                    <article class="tarjeta-autor">
                        <div class="avatar" aria-hidden="true">
                            <?= e(iniciales($autor['nombre'], $autor['apellido'])) ?>
                        </div>
                        <div>
                            <span class="sobretitulo">Autor</span>
                            <h3><?= e(trim($autor['nombre']) . ' ' . trim($autor['apellido'])) ?></h3>
                            <p class="tarjeta-autor__identificador">ID: <?= e($autor['id_autor']) ?></p>
                        </div>
                        <dl class="datos-autor">
                            <div>
                                <dt>Ubicación</dt>
                                <dd>
                                    <?= e(trim($autor['ciudad'])) ?>,
                                    <?= e($autor['estado']) ?> · <?= e($autor['pais']) ?>
                                </dd>
                            </div>
                            <div>
                                <dt>Teléfono</dt>
                                <dd>
                                    <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $autor['telefono'])) ?>">
                                        <?= e($autor['telefono']) ?>
                                    </a>
                                </dd>
                            </div>
                            <div>
                                <dt>Dirección</dt>
                                <dd>
                                    <?= e(trim($autor['direccion'])) ?>,
                                    <?= e((string) $autor['cod_postal']) ?>
                                </dd>
                            </div>
                        </dl>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="llamada llamada--clara">
    <div class="contenedor llamada__contenido">
        <div>
            <span class="sobretitulo">¿Necesitas información?</span>
            <h2>Estamos para ayudarte</h2>
        </div>
        <a class="boton boton--primario" href="contacto.php">Contactar</a>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
