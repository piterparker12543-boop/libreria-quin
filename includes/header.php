<?php
// Plantilla de cabecera (SSI). Antes de incluirla, cada página define $titulo_pagina y $seccion_activa.
require_once __DIR__ . '/../auth/auth_guard.php';
require_once __DIR__ . '/funciones.php';
iniciar_sesion_segura();
$titulo_pagina = $titulo_pagina ?? 'Inicio';
$seccion_activa = $seccion_activa ?? '';

// Cookie de última visita: se lee la anterior y se guarda la actual (1 año)
$ultima_visita = $_COOKIE['ultima_visita'] ?? null;
setcookie('ultima_visita', date('Y-m-d H:i:s'), opciones_cookie(time() + 60 * 60 * 24 * 365));

// Banner por sección. Si el archivo no existe, se muestra el encabezado de texto.
$banners = [
    'productos' => 'banner-productos.png',
    'contacto'  => 'banner-contacto.png',
    'buscar'    => 'banner-buscar.png',
    'faq'       => 'banner-faq.png',
    'quienes'   => 'banner-cuenta.png',
    'cuenta'    => 'banner-cuenta.png',
    'login'     => 'banner-buscar.png',
    'registro'  => 'banner-buscar.png',
    'admin'     => 'banner-productos.png',
];
$banner_archivo = $banners[$seccion_activa] ?? 'banner.png';
$banner_existe = file_exists(__DIR__ . '/../img/' . $banner_archivo);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo_pagina) ?> | <?= e(APP_NAME) ?></title>
    <meta name="description" content="<?= e($descripcion_pagina ?? 'Librería Quin: útiles escolares, cuadernos y cursos de estudio.') ?>">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>
<header>
    <?php if ($banner_existe): ?>
        <img class="banner" src="<?= BASE_URL ?>/img/<?= e($banner_archivo) ?>" alt="Banner de Librería Quin con útiles escolares">
    <?php else: ?>
        <div class="banner-texto"><h1><?= e(APP_NAME) ?></h1></div>
    <?php endif; ?>
</header>
<?php require __DIR__ . '/nav.php'; ?>
<main class="container">