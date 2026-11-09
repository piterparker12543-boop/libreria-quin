<?php
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/../includes/funciones.php';
iniciar_sesion_segura();

$errores = [];
$datos = ['nombre' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos['nombre'] = post('nombre');
    $datos['email']  = post('email');
    $password  = (string)($_POST['password'] ?? '');
    $confirmar = (string)($_POST['confirmar'] ?? '');

    if ($datos['nombre'] === '') {
        $errores['nombre'] = 'El nombre es obligatorio.';
    }
    if ($datos['email'] === '') {
        $errores['email'] = 'El correo es obligatorio.';
    } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = 'El formato del correo no es válido.';
    }
    if (strlen($password) < 8) {
        $errores['password'] = 'La contraseña debe tener al menos 8 caracteres.';
    }
    if ($password !== $confirmar) {
        $errores['confirmar'] = 'Las contraseñas no coinciden.';
    }
    if (!$errores) {
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email');
        $stmt->execute([':email' => $datos['email']]);
        if ($stmt->fetch()) {
            $errores['email'] = 'Ya existe una cuenta con ese correo.';
        }
    }
    if (!$errores) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre, :email, :password, 'usuario')");
        $stmt->execute([':nombre' => $datos['nombre'], ':email' => $datos['email'], ':password' => $hash]);
        header('Location: ' . BASE_URL . '/auth/login.php?registrado=1');
        exit;
    }
}

$seccion_activa = 'registro';
$titulo_pagina = 'Registro';
require_once __DIR__ . '/../includes/header.php';
?>
<h2>Crear cuenta</h2>
<form method="post" action="" novalidate>
    <div class="field"><label for="nombre">Nombre</label>
        <input id="nombre" name="nombre" value="<?= e($datos['nombre']) ?>">
        <span class="error"><?= e($errores['nombre'] ?? '') ?></span></div>
    <div class="field"><label for="email">Correo electrónico</label>
        <input id="email" name="email" type="email" value="<?= e($datos['email']) ?>">
        <span class="error"><?= e($errores['email'] ?? '') ?></span></div>
    <div class="field"><label for="password">Contraseña (mínimo 8 caracteres)</label>
        <input id="password" name="password" type="password">
        <span class="error"><?= e($errores['password'] ?? '') ?></span></div>
    <div class="field"><label for="confirmar">Repetir contraseña</label>
        <input id="confirmar" name="confirmar" type="password">
        <span class="error"><?= e($errores['confirmar'] ?? '') ?></span></div>
    <button class="btn fill" type="submit">Registrarme</button>
</form>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
