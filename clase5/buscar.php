<?php
/**
 * Clase 5 – Formulario por GET: búsqueda y filtros de productos.
 * Parámetros: q (texto), categoria (lista blanca), precio_max (número), orden (lista blanca).
 * Todos se sanitizan con trim/filter_input al leerlos y se escapan con htmlspecialchars() al mostrarlos.
 * Los valores se conservan en el formulario (persistencia) aunque haya errores.
 */
require_once __DIR__ . '/../includes/nav.php';
require_once __DIR__ . '/../includes/funciones.php';

$titulo_pagina = 'Buscar Productos';
$bodyClass = 'buscar-page';
$basePath = prefijo_raiz();
$headExtra = '<link rel="stylesheet" href="' . e($basePath) . 'css/formularios.css">';

$catalogo = require __DIR__ . '/catalogo.php';
$categorias = array_values(array_unique(array_column($catalogo, 'categoria')));
sort($categorias);

$ordenes = [
    'relevancia'  => 'Por defecto',
    'precio_asc'  => 'Precio: menor a mayor',
    'precio_desc' => 'Precio: mayor a menor',
    'nombre'      => 'Nombre (A-Z)',
];

$valores = [
    'q'          => get_texto('q'),
    'categoria'  => get_texto('categoria'),
    'precio_max' => get_texto('precio_max'),
    'orden'      => get_texto('orden'),
];
$buscado = array_filter($valores, static fn($v) => $v !== '') !== [];
$errores = [];

// Validación en servidor
if (largo($valores['q']) > 60) {
    $errores['q'] = 'La búsqueda no puede superar los 60 caracteres.';
}
if ($valores['categoria'] !== '' && !in_array($valores['categoria'], $categorias, true)) {
    $errores['categoria'] = 'La categoría elegida no es válida.';
}
$precioMax = null;
if ($valores['precio_max'] !== '') {
    $precioMax = a_decimal($valores['precio_max']);
    if ($precioMax === null || $precioMax < 0) {
        $errores['precio_max'] = 'Ingresá un precio máximo numérico válido.';
        $precioMax = null;
    }
}
if ($valores['orden'] !== '' && !isset($ordenes[$valores['orden']])) {
    $errores['orden'] = 'El orden elegido no es válido.';
}

// Filtrado (solo con parámetros válidos)
$resultados = $catalogo;
if (!isset($errores['q']) && $valores['q'] !== '') {
    $resultados = array_filter($resultados, static function (array $p) use ($valores): bool {
        return mb_stripos($p['titulo'] . ' ' . $p['descripcion'], $valores['q'], 0, 'UTF-8') !== false;
    });
}
if (!isset($errores['categoria']) && $valores['categoria'] !== '') {
    $resultados = array_filter($resultados, static fn(array $p): bool => $p['categoria'] === $valores['categoria']);
}
if ($precioMax !== null) {
    $resultados = array_filter($resultados, static fn(array $p): bool => $p['precio'] <= $precioMax);
}

$orden = isset($errores['orden']) ? '' : $valores['orden'];
$resultados = array_values($resultados);
if ($orden === 'precio_asc') {
    usort($resultados, static fn($a, $b) => $a['precio'] <=> $b['precio']);
} elseif ($orden === 'precio_desc') {
    usort($resultados, static fn($a, $b) => $b['precio'] <=> $a['precio']);
} elseif ($orden === 'nombre') {
    usort($resultados, static fn($a, $b) => strcasecmp($a['titulo'], $b['titulo']));
}

$invalido = static fn(string $c): string => isset($errores[$c]) ? 'invalido' : '';

require_once __DIR__ . '/../includes/header.php';
?>

<div class="contenedor">

<?php render_page_banner(); ?>

<?php render_nav(null, 'nav-ul'); ?>

<section class="modulo">
    <h2>Buscar productos</h2>

    <?php if ($errores !== []): ?>
        <div class="msg msg-error" role="alert">
            Hay datos de búsqueda inválidos:
            <ul>
                <?php foreach ($errores as $mensajeError): ?>
                    <li><?= e($mensajeError) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="get" action="buscar.php" class="form-card" novalidate>
        <div class="fila-campos">
            <div class="campo">
                <label for="q">¿Qué estás buscando?</label>
                <input type="search" id="q" name="q" maxlength="80" class="<?= $invalido('q') ?>" value="<?= e($valores['q']) ?>">
            </div>
            <div class="campo">
                <label for="categoria">Categoría</label>
                <select id="categoria" name="categoria" class="<?= $invalido('categoria') ?>">
                    <option value="">Todas</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= e($cat) ?>" <?= $valores['categoria'] === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="fila-campos">
            <div class="campo">
                <label for="precio_max">Precio máximo ($)</label>
                <input type="text" id="precio_max" name="precio_max" inputmode="decimal" class="<?= $invalido('precio_max') ?>" value="<?= e($valores['precio_max']) ?>">
            </div>
            <div class="campo">
                <label for="orden">Ordenar por</label>
                <select id="orden" name="orden" class="<?= $invalido('orden') ?>">
                    <?php foreach ($ordenes as $clave => $texto): ?>
                        <option value="<?= e($clave) ?>" <?= $valores['orden'] === $clave ? 'selected' : '' ?>><?= e($texto) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <button type="submit" class="btn-modulo">Buscar</button>
        <?php if ($buscado): ?><a href="buscar.php" class="btn-modulo btn-secundario">Limpiar filtros</a><?php endif; ?>
    </form>

    <?php if ($buscado && $errores === []): ?>
        <?php if ($resultados === []): ?>
            <div class="msg msg-error" role="status">
                No se encontraron productos
                <?php if ($valores['q'] !== ''): ?>para «<?= e($valores['q']) ?>»<?php endif; ?>.
            </div>
        <?php else: ?>
            <div class="msg msg-ok" role="status">
                Se encontraron <?= e(count($resultados)) ?> producto(s)
                <?php if ($valores['q'] !== ''): ?>para «<?= e($valores['q']) ?>»<?php endif; ?>.
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="resultados">
        <?php foreach ($resultados as $p): ?>
            <article class="resultado">
                <h3><?= e($p['titulo']) ?></h3>
                <div class="cat"><?= e($p['categoria']) ?></div>
                <p><?= e($p['descripcion']) ?></p>
                <div class="precio-res">$<?= e(number_format($p['precio'], 0, ',', '.')) ?></div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
