<?php
// Inserción (Create) con consulta preparada.
require_once __DIR__ . '/../auth/auth_guard.php';
require_once __DIR__ . '/../includes/funciones.php';
proteger_ruta('admin');

$stmt = $pdo->prepare('SELECT id, nombre FROM categorias ORDER BY nombre');
$stmt->execute();
$categorias = $stmt->fetchAll();

$errores = [];
$datos = ['titulo' => '', 'descripcion' => '', 'precio' => '', 'categoria_id' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos['titulo']       = post('titulo');
    $datos['descripcion']  = post('descripcion');
    $datos['precio']       = post('precio');
    $datos['categoria_id'] = post('categoria_id');
    $errores = validar_producto($datos);

    if (!$errores) {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO productos (titulo, descripcion, precio, categoria_id)
                 VALUES (:titulo, :descripcion, :precio, :categoria_id)'
            );
            $stmt->execute([
                ':titulo'       => $datos['titulo'],
                ':descripcion'  => $datos['descripcion'],
                ':precio'       => (float)$datos['precio'],
                ':categoria_id' => $datos['categoria_id'] === '' ? null : (int)$datos['categoria_id'],
            ]);
            header('Location: index.php?status=created');
            exit;
        } catch (PDOException $ex) {
            $errores['general'] = 'No se pudo guardar el producto.';
        }
    }
}

$titulo_pagina = 'Crear producto';
$seccion_activa = 'admin';
require_once __DIR__ . '/../includes/header.php';
?>
<h2>Agregar producto</h2>
<?php if (!empty($errores['general'])): ?><p class="error"><?= e($errores['general']) ?></p><?php endif; ?>
<?php require __DIR__ . '/formulario_producto.php'; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
