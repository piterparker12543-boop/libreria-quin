<?php
$titulo_pagina = 'Productos';
$seccion_activa = 'productos';
require_once __DIR__ . '/includes/header.php';

$stmt = $pdo->prepare(
    "SELECT p.titulo, p.descripcion, p.precio, p.imagen, c.nombre AS categoria
     FROM productos p
     LEFT JOIN categorias c ON c.id = p.categoria_id
     WHERE p.estado = 'disponible'
     ORDER BY p.precio DESC"
);
$stmt->execute();
$productos = $stmt->fetchAll();
?>
<h2>Productos</h2>
<?php if (!$productos): ?><p>No hay productos disponibles.</p><?php endif; ?>
<?php foreach ($productos as $p): ?>
    <article class="card">
        <?php if (!empty($p['imagen'])): ?>
            <img class="card-img" src="<?= BASE_URL ?>/img/<?= e($p['imagen']) ?>" alt="<?= e($p['titulo']) ?>">
        <?php endif; ?>
        <div>
            <h3><?= e($p['titulo']) ?></h3>
            <p><?= e($p['categoria'] ?? 'Sin categoría') ?> · $<?= number_format((float)$p['precio'], 2, ',', '.') ?></p>
            <p><?= e($p['descripcion']) ?></p>
        </div>
    </article>
<?php endforeach; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>