<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

$tituloPagina = 'Contacto';
$paginaActiva = 'contacto';
$descripcionPagina = 'Envía tus preguntas y comentarios a Librería Horizonte.';

$datos = [
    'nombre' => '',
    'correo' => '',
    'asunto' => '',
    'comentario' => '',
];
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre' => trim((string) ($_POST['nombre'] ?? '')),
        'correo' => trim((string) ($_POST['correo'] ?? '')),
        'asunto' => trim((string) ($_POST['asunto'] ?? '')),
        'comentario' => trim((string) ($_POST['comentario'] ?? '')),
    ];

    $token = (string) ($_POST['csrf_token'] ?? '');
    $sitioWeb = trim((string) ($_POST['sitio_web'] ?? ''));

    if (!tokenCsrfValido($token)) {
        $errores['general'] = 'La sesión del formulario venció. Actualiza la página e inténtalo nuevamente.';
    }

    if ($sitioWeb !== '') {
        $errores['general'] = 'No fue posible procesar el formulario.';
    }

    if ($datos['nombre'] === '' || longitud($datos['nombre']) < 2) {
        $errores['nombre'] = 'Escribe un nombre de al menos 2 caracteres.';
    } elseif (longitud($datos['nombre']) > 100) {
        $errores['nombre'] = 'El nombre no puede superar 100 caracteres.';
    }

    if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
        $errores['correo'] = 'Escribe un correo electrónico válido.';
    } elseif (longitud($datos['correo']) > 150) {
        $errores['correo'] = 'El correo no puede superar 150 caracteres.';
    }

    if ($datos['asunto'] === '' || longitud($datos['asunto']) < 3) {
        $errores['asunto'] = 'Escribe un asunto de al menos 3 caracteres.';
    } elseif (longitud($datos['asunto']) > 150) {
        $errores['asunto'] = 'El asunto no puede superar 150 caracteres.';
    }

    if ($datos['comentario'] === '' || longitud($datos['comentario']) < 10) {
        $errores['comentario'] = 'El comentario debe tener al menos 10 caracteres.';
    } elseif (longitud($datos['comentario']) > 1000) {
        $errores['comentario'] = 'El comentario no puede superar 1,000 caracteres.';
    }

    if ($errores === []) {
        try {
            $conexion = obtenerConexion();
            $consulta = $conexion->prepare(
                'INSERT INTO contacto (fecha, correo, nombre, asunto, comentario)
                 VALUES (NOW(), :correo, :nombre, :asunto, :comentario)'
            );
            $consulta->execute([
                'correo' => $datos['correo'],
                'nombre' => $datos['nombre'],
                'asunto' => $datos['asunto'],
                'comentario' => $datos['comentario'],
            ]);

            guardarMensaje(
                'exito',
                '¡Gracias por escribirnos! Tu mensaje fue guardado correctamente.'
            );
            redireccionar('contacto.php?enviado=1');
        } catch (Throwable $excepcion) {
            error_log('Error al guardar contacto: ' . $excepcion->getMessage());
            $errores['general'] = 'No pudimos guardar el mensaje. Revisa la conexión e inténtalo nuevamente.';
        }
    }
}

$mensaje = obtenerMensaje();

require __DIR__ . '/includes/header.php';
?>

<section class="hero hero--contacto">
    <div class="contenedor hero__contenido hero__contenido--centrado">
        <div>
            <span class="etiqueta">Hablemos</span>
            <h1>Estamos para ayudarte</h1>
            <p>
                Envíanos tus preguntas, sugerencias o comentarios. Nuestro equipo
                estará encantado de escucharte.
            </p>
        </div>
    </div>
</section>

<section class="seccion">
    <div class="contenedor contacto">
        <aside class="contacto__informacion">
            <span class="sobretitulo">Librería Horizonte</span>
            <h2>Conversemos sobre libros</h2>
            <p>
                Completa el formulario y almacenaremos tu solicitud de forma segura
                para darle seguimiento.
            </p>

            <div class="dato-contacto">
                <span aria-hidden="true">✉</span>
                <div>
                    <strong>Correo</strong>
                    <a href="mailto:hola@libreriahorizonte.com">hola@libreriahorizonte.com</a>
                </div>
            </div>
            <div class="dato-contacto">
                <span aria-hidden="true">⌖</span>
                <div>
                    <strong>Ubicación</strong>
                    <p>Santo Domingo, República Dominicana</p>
                </div>
            </div>
            <div class="dato-contacto">
                <span aria-hidden="true">◷</span>
                <div>
                    <strong>Horario</strong>
                    <p>Lunes a viernes, 9:00 a. m. - 6:00 p. m.</p>
                </div>
            </div>
        </aside>

        <div class="panel-formulario">
            <span class="sobretitulo">Formulario de contacto</span>
            <h2>Envíanos un mensaje</h2>

            <?php if ($mensaje !== null): ?>
                <div class="alerta alerta--exito alerta--cerrable" role="status">
                    <strong>Mensaje enviado</strong>
                    <span><?= e($mensaje['texto']) ?></span>
                    <button type="button" aria-label="Cerrar mensaje">×</button>
                </div>
            <?php endif; ?>

            <?php if (isset($errores['general'])): ?>
                <div class="alerta alerta--error" role="alert">
                    <strong>No se pudo enviar</strong>
                    <span><?= e($errores['general']) ?></span>
                </div>
            <?php endif; ?>

            <form class="formulario-contacto" method="post" action="contacto.php" novalidate>
                <input type="hidden" name="csrf_token" value="<?= e(tokenCsrf()) ?>">

                <div class="campo-trampa" aria-hidden="true">
                    <label>
                        Sitio web
                        <input type="text" name="sitio_web" tabindex="-1" autocomplete="off">
                    </label>
                </div>

                <label class="campo">
                    <span>Nombre completo <b aria-hidden="true">*</b></span>
                    <input
                        type="text"
                        name="nombre"
                        value="<?= e($datos['nombre']) ?>"
                        maxlength="100"
                        autocomplete="name"
                        required
                        <?= isset($errores['nombre']) ? 'aria-invalid="true"' : '' ?>
                    >
                    <?php if (isset($errores['nombre'])): ?>
                        <small class="mensaje-error"><?= e($errores['nombre']) ?></small>
                    <?php endif; ?>
                </label>

                <label class="campo">
                    <span>Correo electrónico <b aria-hidden="true">*</b></span>
                    <input
                        type="email"
                        name="correo"
                        value="<?= e($datos['correo']) ?>"
                        maxlength="150"
                        autocomplete="email"
                        required
                        <?= isset($errores['correo']) ? 'aria-invalid="true"' : '' ?>
                    >
                    <?php if (isset($errores['correo'])): ?>
                        <small class="mensaje-error"><?= e($errores['correo']) ?></small>
                    <?php endif; ?>
                </label>

                <label class="campo">
                    <span>Asunto <b aria-hidden="true">*</b></span>
                    <input
                        type="text"
                        name="asunto"
                        value="<?= e($datos['asunto']) ?>"
                        maxlength="150"
                        required
                        <?= isset($errores['asunto']) ? 'aria-invalid="true"' : '' ?>
                    >
                    <?php if (isset($errores['asunto'])): ?>
                        <small class="mensaje-error"><?= e($errores['asunto']) ?></small>
                    <?php endif; ?>
                </label>

                <label class="campo">
                    <span>Comentario <b aria-hidden="true">*</b></span>
                    <textarea
                        name="comentario"
                        rows="6"
                        maxlength="1000"
                        required
                        data-contador
                        <?= isset($errores['comentario']) ? 'aria-invalid="true"' : '' ?>
                    ><?= e($datos['comentario']) ?></textarea>
                    <small class="ayuda-campo">
                        <span data-total-caracteres><?= e((string) longitud($datos['comentario'])) ?></span>/1000
                    </small>
                    <?php if (isset($errores['comentario'])): ?>
                        <small class="mensaje-error"><?= e($errores['comentario']) ?></small>
                    <?php endif; ?>
                </label>

                <button class="boton boton--primario boton--ancho" type="submit">
                    Enviar mensaje
                </button>
                <p class="nota-formulario">Los campos marcados con * son obligatorios.</p>
            </form>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
