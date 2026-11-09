<?php
// Control de sesión, cookies seguras y guardián de rutas por rol (TP8).
require_once __DIR__ . '/../config/conexion.php';

function es_https(): bool {
    return !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
}

// Opciones seguras para cualquier cookie del sitio
function opciones_cookie(int $expira): array {
    return [
        'expires'  => $expira,
        'path'     => '/',
        'httponly' => true,
        'secure'   => es_https(),
        'samesite' => 'Lax',
    ];
}

function iniciar_sesion_segura(): void {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'httponly' => true,
            'secure'   => es_https(),
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}

function proteger_ruta(?string $rolRequerido = null): void {
    iniciar_sesion_segura();
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ' . BASE_URL . '/auth/login.php?error=acceso_denegado');
        exit;
    }
    if ($rolRequerido !== null && ($_SESSION['rol'] ?? '') !== $rolRequerido) {
        header('Location: ' . BASE_URL . '/auth/login.php?error=acceso_denegado');
        exit;
    }
}