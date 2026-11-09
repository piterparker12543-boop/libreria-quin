<?php
// Crea (o promueve) el administrador inicial usando ADMIN_* del archivo .env.
// Se ejecuta solo desde la terminal: php config/crear_admin.php
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('Este script solo se ejecuta desde la línea de comandos.');
}

require_once __DIR__ . '/conexion.php';

$env = cargar_env(__DIR__ . '/../.env');
$nombre   = $env['ADMIN_NOMBRE'] ?? 'Administrador';
$email    = $env['ADMIN_EMAIL'] ?? '';
$password = $env['ADMIN_PASSWORD'] ?? '';

if ($email === '' || strlen($password) < 8) {
    exit("Revisá ADMIN_EMAIL y ADMIN_PASSWORD en .env (la clave debe tener al menos 8 caracteres).\n");
}

$stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email');
$stmt->execute([':email' => $email]);

if ($stmt->fetch()) {
    $stmt = $pdo->prepare("UPDATE usuarios SET rol = 'admin' WHERE email = :email");
    $stmt->execute([':email' => $email]);
    echo "El usuario $email ya existía y ahora tiene rol admin.\n";
    exit;
}

$stmt = $pdo->prepare(
    "INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre, :email, :password, 'admin')"
);
$stmt->execute([
    ':nombre'   => $nombre,
    ':email'    => $email,
    ':password' => password_hash($password, PASSWORD_BCRYPT),
]);
echo "Administrador creado: $email\n";