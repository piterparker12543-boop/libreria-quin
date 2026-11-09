<?php
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/../includes/funciones.php';
iniciar_sesion_segura();

$error = '';
$exito = '';
$email = '';
$recordar = isset($_COOKIE['recordar_email']);
if ($recordar) {
    $email = limpiar($_COOKIE['recordar_email']);
}

if (($_GET['error'] ?? '') === 'acceso_denegado') {
    $error = 'Necesitás iniciar sesión para acceder a esa página.';
}
if (isset($_GET['registrado'])) {
    $exito = 'Cuenta creada. Ya podés ingresar.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = post('email');
    $password = (string)($_POST['password'] ?? '');
    $recordar = isset($_POST['recordar']);

    $stmt = $pdo->prepare('SELECT id, nombre, password, rol FROM usuarios WHERE email = :email');
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($password, $usuario['password'])) {
        session_regenerate_id(true); // evita Session Fixation
        $_SESSION['usuario_id']     = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['rol']            = $usuario['rol'];

        // Cookie "Recordar mi correo": 30 días, o se borra si no está marcada
        if ($recordar) {
            setcookie('recordar_email', $email, opciones_cookie(time() + 60 * 60 * 24 * 30));
        } else {
            setcookie('recordar_email', '', opciones_cookie(time() - 3600));
        }

        $destino = $usuario['rol'] === 'admin' ? '/admin/index.php' : '/cuenta.php';
        header('Location: ' . BASE_URL . $destino);
        exit;
    }
    $error = 'Correo o contraseña incorrectos.';
}

$seccion_activa = 'login';
$titulo_pagina = 'Ingresar';
require_once __DIR__ . '/../includes/header.php';
?>
<h2>Iniciar sesión</h2>
<?php if ($exito): ?><p class="ok"><?= e($exito) ?></p><?php endif; ?>
<?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
<form method="post" action="" novalidate>
    <div class="field"><label for="email">Correo electrónico</label>
        <input id="email" name="email" type="email" value="<?= e($email) ?>"></div>
    <div class="field"><label for="password">Contraseña</label>
        <input id="password" name="password" type="password"></div>
    <div class="field"><label><input type="checkbox" name="recordar" <?= $recordar ? 'checked' : '' ?>> Recordar mi correo en este equipo</label></div>
    <button class="btn fill" type="submit">Ingresar</button>
</form>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>