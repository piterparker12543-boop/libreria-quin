<?php
// Actualización (Update) con consulta preparada y cláusula WHERE.
require_once __DIR__ . '/../auth/auth_guard.php';
require_once __DIR__ . '/../includes/funciones.php';
proteger_ruta('admin');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM productos WHERE id = :id');
$stmt->execute([':id' => $id]);
$producto = $stmt->fetch();
if (!$producto) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT id, nombre FROM categorias ORDER BY nombre');
$stmt->execute();
$categorias = $stmt->fetchAll();

$errores = [];
$datos = [
    'titulo'       => $producto['titulo'],
    'descripcion'  => $producto['descripcion'] ?? '',
    'precio'       => $producto['precio'],
    'categoria_id' => (string)($producto['categoria_id'] ?? ''),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos['titulo']       = post('titulo');
    $datos['descripcion']  = post('descripcion');
    $datos['precio']       = post('precio');
    $datos['categoria_id'] = post('categoria_id');
    $errores = validar_producto($datos);

    if (!$errores) {
        $stmt = $pdo->prepare(
            'UPDATE productos
             SET titulo = :titulo, descripcion = :descripcion, precio = :precio, categoria_id = :categoria_id
             WHERE id = :id'
        );
        $stmt->execute([
            ':titulo'       => $datos['titulo'],
            ':descripcion'  => $datos['descripcion'],
            ':precio'       => (float)$datos['precio'],
            ':categoria_id' => $datos['categoria_id'] === '' ? null : (int)$datos['categoria_id'],
            ':id'           => $id,
        ]);
        header('Location: index.php?status=updated');
        exit;
    }
}

$titulo_pagina = 'Editar producto';
$seccion_activa = 'admin';
require_once __DIR__ . '/../includes/header.php';
?>
<h2>Editar producto #<?= (int)$id ?></h2>
<?php require __DIR__ . '/formulario_producto.php'; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
