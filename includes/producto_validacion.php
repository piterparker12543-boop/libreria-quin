<?php
/**
 * Validación en servidor del formulario de productos (crear / editar).
 * Devuelve [valores, errores, datos]. Los valores están limpios (trim + sin caracteres de control)
 * y se escapan con e() recién al imprimirlos. "datos" solo se completa si no hay errores.
 */

require_once __DIR__ . '/funciones.php';

function validar_producto(PDO $pdo): array
{
    $valores = [
        'titulo'       => post_texto('titulo'),
        'descripcion'  => post_texto('descripcion'),
        'precio'       => post_texto('precio'),
        'stock'        => post_texto('stock'),
        'categoria_id' => post_texto('categoria_id'),
    ];
    $errores = [];

    // Título
    if ($valores['titulo'] === '') {
        $errores['titulo'] = 'El título es obligatorio.';
    } elseif (largo($valores['titulo']) < 3 || largo($valores['titulo']) > 150) {
        $errores['titulo'] = 'El título debe tener entre 3 y 150 caracteres.';
    }

    // Descripción
    if ($valores['descripcion'] === '') {
        $errores['descripcion'] = 'La descripción es obligatoria.';
    } elseif (largo($valores['descripcion']) > 1000) {
        $errores['descripcion'] = 'La descripción no puede superar los 1000 caracteres.';
    }

    // Precio
    $precio = a_decimal($valores['precio']);
    if ($valores['precio'] === '') {
        $errores['precio'] = 'El precio es obligatorio.';
    } elseif ($precio === null || $precio < 0 || $precio > 99999999.99) {
        $errores['precio'] = 'Ingresá un precio numérico válido (mayor o igual a 0).';
    }

    // Stock
    $stock = filter_var($valores['stock'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 1000000]]);
    if ($valores['stock'] === '') {
        $errores['stock'] = 'El stock es obligatorio.';
    } elseif ($stock === false) {
        $errores['stock'] = 'El stock debe ser un número entero mayor o igual a 0.';
    }

    // Categoría (debe existir en la base)
    $categoriaId = filter_var($valores['categoria_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($valores['categoria_id'] === '') {
        $errores['categoria_id'] = 'Elegí una categoría.';
    } elseif ($categoriaId === false) {
        $errores['categoria_id'] = 'La categoría elegida no es válida.';
    } else {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM categorias WHERE id = :id');
        $stmt->execute([':id' => $categoriaId]);
        if ((int) $stmt->fetchColumn() === 0) {
            $errores['categoria_id'] = 'La categoría elegida no existe.';
        }
    }

    $datos = $errores === [] ? [
        'titulo'       => $valores['titulo'],
        'descripcion'  => $valores['descripcion'],
        'precio'       => round((float) $precio, 2),
        'stock'        => (int) $stock,
        'categoria_id' => (int) $categoriaId,
    ] : [];

    return [$valores, $errores, $datos];
}

function listar_categorias(PDO $pdo): array
{
    $stmt = $pdo->prepare('SELECT id, nombre FROM categorias ORDER BY nombre ASC');
    $stmt->execute();
    return $stmt->fetchAll();
}
