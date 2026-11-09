<?php
// Cierre de sesión (TP8): vacía $_SESSION, borra la cookie PHPSESSID y destruye la sesión.
require_once __DIR__ . '/auth_guard.php';
iniciar_sesion_segura();

$_SESSION = [];

// Borra la cookie de sesión con los mismos atributos con que se creó (path, httponly, samesite)
setcookie(session_name(), '', opciones_cookie(time() - 3600));

session_destroy();

header('Location: ' . BASE_URL . '/index.php');
exit;