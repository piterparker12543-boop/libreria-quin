<?php
$titulo_pagina = 'Inicio';
$seccion_activa = 'inicio';
require_once __DIR__ . '/includes/header.php';
?>
<section class="inicio">
    <img class="logo" src="<?= BASE_URL ?>/img/logo.png" alt="Logo de Librería Quin con lápiz y libros">
    <div>
        <h2>¿Qué ofrecemos?</h2>
        <p class="centrado">Nos dedicamos a la venta de artículos de librería, donde los usuarios pueden encontrar productos como cuadernos, lapiceras, hojas, carpetas y útiles escolares. El sitio ofrece una navegación simple y organizada, permitiendo explorar distintas categorías y acceder fácilmente a todo lo necesario para el estudio, la oficina o el uso diario.</p>
    </div>
    <img class="poster" src="<?= BASE_URL ?>/img/poster.png" alt="Afiche de Librería Quin: todo en artículos de librería">
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>