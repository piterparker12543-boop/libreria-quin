<?php
// Escapado de salida (anti-XSS) y sanitización de entradas.
function e(?string $texto): string {
    return htmlspecialchars((string)$texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function limpiar(string $valor): string {
    return trim(strip_tags($valor));
}

function post(string $campo): string {
    return limpiar((string)($_POST[$campo] ?? ''));
}

function get(string $campo): string {
    return limpiar((string)($_GET[$campo] ?? ''));
}

// Validación de servidor para productos (TP7 y TP8).
function validar_producto(array $d): array {
    $errores = [];
    if ($d['titulo'] === '') {
        $errores['titulo'] = 'El título es obligatorio.';
    } elseif (mb_strlen($d['titulo']) > 150) {
        $errores['titulo'] = 'El título no puede superar 150 caracteres.';
    }
    if ($d['precio'] === '' || filter_var($d['precio'], FILTER_VALIDATE_FLOAT) === false || (float)$d['precio'] < 0) {
        $errores['precio'] = 'Ingresá un precio válido, con punto decimal y mayor o igual a 0.';
    }
    if ($d['categoria_id'] !== '' && filter_var($d['categoria_id'], FILTER_VALIDATE_INT) === false) {
        $errores['categoria_id'] = 'Categoría no válida.';
    }
    return $errores;
}
