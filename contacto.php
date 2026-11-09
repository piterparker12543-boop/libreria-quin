<?php
// Formulario con envío POST: contacto, con validación, mensajes y persistencia de campos (TP5).
$titulo_pagina = 'Comentarios y preguntas';
$seccion_activa = 'contacto';
require_once __DIR__ . '/includes/header.php';

$errores = [];
$ok = false;
$datos = ['nombre' => '', 'email' => '', 'mensaje' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos['nombre']  = post('nombre');
    $datos['email']   = post('email');
    $datos['mensaje'] = post('mensaje');

    if ($datos['nombre'] === '') {
        $errores['nombre'] = 'El nombre es obligatorio.';
    }
    if ($datos['email'] === '') {
        $errores['email'] = 'El correo es obligatorio.';
    } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = 'El formato del correo no es válido.';
    }
    if ($datos['mensaje'] === '') {
        $errores['mensaje'] = 'El mensaje es obligatorio.';
    }

    if (!$errores) {
        $stmt = $pdo->prepare('INSERT INTO contactos (nombre, email, mensaje) VALUES (:nombre, :email, :mensaje)');
        $stmt->execute([':nombre' => $datos['nombre'], ':email' => $datos['email'], ':mensaje' => $datos['mensaje']]);
        $ok = true;
        $datos = ['nombre' => '', 'email' => '', 'mensaje' => ''];
    }
}
?>
<h2>Comentarios y preguntas</h2>
<?php if ($ok): ?><p class="ok">¡Gracias! Recibimos tu mensaje.</p><?php endif; ?>
<form method="post" action="" novalidate>
    <div class="field"><label for="nombre">Nombre</label>
        <input id="nombre" name="nombre" value="<?= e($datos['nombre']) ?>">
        <span class="error"><?= e($errores['nombre'] ?? '') ?></span></div>
    <div class="field"><label for="email">Correo electrónico</label>
        <input id="email" name="email" type="email" value="<?= e($datos['email']) ?>">
        <span class="error"><?= e($errores['email'] ?? '') ?></span></div>
    <div class="field"><label for="mensaje">Mensaje</label>
        <textarea id="mensaje" name="mensaje" rows="4"><?= e($datos['mensaje']) ?></textarea>
        <span class="error"><?= e($errores['mensaje'] ?? '') ?></span></div>
    <button class="btn fill" type="submit">Enviar</button>
</form>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
