<?php
/**
 * Guardián de rutas (reutilizable).
 *
 * Uso al inicio de una página protegida, ANTES de imprimir cualquier HTML:
 *
 *     require_once __DIR__ . '/includes/funciones.php';
 *     $rolRequerido = 'admin';                       // opcional: restringe por rol
 *     require_once __DIR__ . '/includes/auth_guard.php';
 *
 * - Sin sesión iniciada  -> login.php?error=acceso_denegado
 * - Inactividad > 30 min -> logout.php?motivo=expirada
 * - Rol insuficiente     -> cuenta.php?error=sin_permiso
 */
require_once __DIR__ . '/funciones.php';

if (!defined('SESION_INACTIVIDAD_MAX')) {
    define('SESION_INACTIVIDAD_MAX', 1800); // segundos
}

// Evita que el navegador muestre páginas protegidas desde la caché (botón "atrás" tras salir)
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$__raiz = prefijo_raiz();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . $__raiz . 'login.php?error=acceso_denegado');
    exit;
}

$__ahora = time();
if (isset($_SESSION['ultima_actividad']) && ($__ahora - (int) $_SESSION['ultima_actividad']) > SESION_INACTIVIDAD_MAX) {
    header('Location: ' . $__raiz . 'logout.php?motivo=expirada');
    exit;
}
$_SESSION['ultima_actividad'] = $__ahora;

if (isset($rolRequerido) && ($_SESSION['rol'] ?? '') !== $rolRequerido) {
    header('Location: ' . $__raiz . 'cuenta.php?error=sin_permiso');
    exit;
}
