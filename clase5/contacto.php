<?php
/**
 * Clase 5 – Formulario por POST: contacto / opinión.
 * - Validación en servidor (campos vacíos, formato de email, largos, lista blanca del asunto).
 * - Sanitización: trim + eliminación de caracteres de control al leer; htmlspecialchars() al imprimir.
 * - Persistencia: si hay errores se conservan los valores escritos.
 * - Patrón Post/Redirect/Get: al enviar bien se redirige, así F5 no reenvía el formulario.
 */
require_once __DIR__ . '/../includes/nav.php';
require_once __DIR__ . '/../includes/funciones.php';

$titulo_pagina = 'Contacto y Opiniones';
$bodyClass = 'contacto-page';
$basePath = prefijo_raiz();
$headExtra = '<link rel="stylesheet" href="' . e($basePath) . 'css/formularios.css">';

$asuntos = [
    'consulta' => 'Consulta sobre productos',
    'pedido'   => 'Estado de mi pedido',
    'opinion'  => 'Opinión sobre la tienda',
    'otro'     => 'Otro motivo',
];

$valores = ['nombre' => '', 'email' => '', 'asunto' => '', 'mensaje' => '', 'acepta' => ''];
$errores = [];

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $valores = [
        'nombre'  => post_texto('nombre'),
        'email'   => post_texto('email'),
        'asunto'  => post_texto('asunto'),
        'mensaje' => post_texto('mensaje'),
        'acepta'  => post_texto('acepta'),
    ];

    if (!csrf_valido()) {
        $errores['general'] = 'La sesión del formulario expiró. Volvé a enviarlo.';
    }

    // Nombre
    if ($valores['nombre'] === '') {
        $errores['nombre'] = 'El nombre es obligatorio.';
    } elseif (largo($valores['nombre']) < 2 || largo($valores['nombre']) > 80) {
        $errores['nombre'] = 'El nombre debe tener entre 2 y 80 caracteres.';
    }

    // Email
    if ($valores['email'] === '') {
        $errores['email'] = 'El correo electrónico es obligatorio.';
    } elseif (largo($valores['email']) > 150 || !filter_var($valores['email'], FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = 'Ingresá un correo electrónico con formato válido (ej.: nombre@dominio.com).';
    }

    // Asunto (lista blanca)
    if ($valores['asunto'] === '') {
        $errores['asunto'] = 'Elegí un asunto.';
    } elseif (!array_key_exists($valores['asunto'], $asuntos)) {
        $errores['asunto'] = 'El asunto elegido no es válido.';
    }

    // Mensaje
    if ($valores['mensaje'] === '') {
        $errores['mensaje'] = 'El mensaje es obligatorio.';
    } elseif (largo($valores['mensaje']) < 10) {
        $errores['mensaje'] = 'El mensaje debe tener al menos 10 caracteres.';
    } elseif (largo($valores['mensaje']) > 1000) {
        $errores['mensaje'] = 'El mensaje no puede superar los 1000 caracteres.';
    }

    // Aceptación
    if ($valores['acepta'] !== '1') {
        $errores['acepta'] = 'Tenés que aceptar el tratamiento de tus datos para continuar.';
    }

    if ($errores === []) {
        $_SESSION['flash_contacto'] = [
            'nombre'  => $valores['nombre'],
            'email'   => $valores['email'],
            'asunto'  => $asuntos[$valores['asunto']],
            'mensaje' => $valores['mensaje'],
        ];
        header('Location: contacto.php?enviado=1');
        exit;
    }
}

// Resultado de un envío exitoso (se muestra una sola vez)
$enviado = null;
if (get_texto('enviado') === '1' && isset($_SESSION['flash_contacto'])) {
    $enviado = $_SESSION['flash_contacto'];
    unset($_SESSION['flash_contacto']);
}

$invalido = static fn(string $c): string => isset($errores[$c]) ? 'invalido' : '';

require_once __DIR__ . '/../includes/header.php';
?>

<div class="contenedor">

<?php render_page_banner(); ?>

<?php render_nav(null, 'nav-ul'); ?>

<section class="modulo">
    <h2>Contacto y opiniones</h2>
    <p>Escribinos tu consulta u opinión y te respondemos a la brevedad.</p>

    <?php if ($enviado !== null): ?>
        <div class="msg msg-ok" role="status">¡Gracias, <?= e($enviado['nombre']) ?>! Tu mensaje se envió correctamente.</div>
        <div class="form-card resumen">
            <h3>Resumen de lo enviado</h3>
            <dl>
                <dt>Nombre</dt><dd><?= e($enviado['nombre']) ?></dd>
                <dt>Correo</dt><dd><?= e($enviado['email']) ?></dd>
                <dt>Asunto</dt><dd><?= e($enviado['asunto']) ?></dd>
                <dt>Mensaje</dt><dd><?= nl2br(e($enviado['mensaje'])) ?></dd>
            </dl>
        </div>
    <?php endif; ?>

    <?php if ($errores !== []): ?>
        <div class="msg msg-error" role="alert">
            No se pudo enviar el formulario. Corregí lo siguiente:
            <ul>
                <?php foreach ($errores as $mensajeError): ?>
                    <li><?= e($mensajeError) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="contacto.php" class="form-card" novalidate>
        <?= csrf_campo() ?>

        <div class="fila-campos">
            <div class="campo">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" maxlength="80"
                       class="<?= $invalido('nombre') ?>" value="<?= e($valores['nombre']) ?>">
                <?php if (isset($errores['nombre'])): ?><span class="error-campo"><?= e($errores['nombre']) ?></span><?php endif; ?>
            </div>

            <div class="campo">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" maxlength="150"
                       class="<?= $invalido('email') ?>" value="<?= e($valores['email']) ?>">
                <?php if (isset($errores['email'])): ?><span class="error-campo"><?= e($errores['email']) ?></span><?php endif; ?>
            </div>
        </div>

        <div class="campo">
            <label for="asunto">Asunto</label>
            <select id="asunto" name="asunto" class="<?= $invalido('asunto') ?>">
                <option value="">Elegí un asunto</option>
                <?php foreach ($asuntos as $clave => $texto): ?>
                    <option value="<?= e($clave) ?>" <?= $valores['asunto'] === $clave ? 'selected' : '' ?>><?= e($texto) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errores['asunto'])): ?><span class="error-campo"><?= e($errores['asunto']) ?></span><?php endif; ?>
        </div>

        <div class="campo">
            <label for="mensaje">Mensaje</label>
            <textarea id="mensaje" name="mensaje" maxlength="1000" class="<?= $invalido('mensaje') ?>"><?= e($valores['mensaje']) ?></textarea>
            <?php if (isset($errores['mensaje'])): ?><span class="error-campo"><?= e($errores['mensaje']) ?></span><?php endif; ?>
        </div>

        <div class="campo check">
            <input type="checkbox" id="acepta" name="acepta" value="1" <?= $valores['acepta'] === '1' ? 'checked' : '' ?>>
            <label for="acepta">Acepto que usen mis datos para responder esta consulta.</label>
        </div>
        <?php if (isset($errores['acepta'])): ?><span class="error-campo"><?= e($errores['acepta']) ?></span><?php endif; ?>

        <div style="margin-top:14px;">
            <button type="submit" class="btn-modulo">Enviar mensaje</button>
        </div>
    </form>
</section>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
