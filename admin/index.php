<?php
// Listado principal (Read). Solo el rol admin puede acceder (TP7 y TP8).
require_once __DIR__ . '/../auth/auth_guard.php';
proteger_ruta('admin');

$titulo_pagina = 'Administración';
$seccion_activa = 'admin';
require_once __DIR__ . '/../includes/header.php';

$mensajes = [
    'created' => 'Producto creado correctamente.',
    'updated' => 'Producto actualizado correctamente.',
    'deleted' => 'Producto eliminado.',
];
$msg = $mensajes[$_GET['status'] ?? ''] ?? '';

$stmt = $pdo->prepare(
    "SELECT p.id, p.titulo, p.precio, p.estado, c.nombre AS categoria
     FROM productos p
     LEFT JOIN categorias c ON c.id = p.categoria_id
     ORDER BY p.id DESC"
);
$stmt->execute();
$registros = $stmt->fetchAll();
?>
<h2>Administración del sitio</h2>
<?php if ($msg): ?><p class="ok"><?= e($msg) ?></p><?php endif; ?>
<p><a class="btn fill" href="crear.php">Agregar nuevo registro</a></p>
<table>
    <thead><tr><th>ID</th><th>Título</th><th>Categoría</th><th>Precio</th><th>Estado</th><th>Acciones</th></tr></thead>
    <tbody>
    <?php foreach ($registros as $r): ?>
        <tr>
            <td><?= (int)$r['id'] ?></td>
            <td><?= e($r['titulo']) ?></td>
            <td><?= e($r['categoria'] ?? 'Sin categoría') ?></td>
            <td>$<?= number_format((float)$r['precio'], 2, ',', '.') ?></td>
            <td><?= e($r['estado']) ?></td>
            <td>
                <a class="btn" href="editar.php?id=<?= (int)$r['id'] ?>">Editar</a>
                <a class="btn" href="eliminar.php?id=<?= (int)$r['id'] ?>" onclick="return confirm('¿Confirma eliminar este registro?')">Eliminar</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
