<?php
// Eliminación (Delete) con cláusula WHERE y consulta preparada.
require_once __DIR__ . '/../auth/auth_guard.php';
proteger_ruta('admin');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id) {
    $stmt = $pdo->prepare('DELETE FROM productos WHERE id = :id');
    $stmt->execute([':id' => $id]);
}
header('Location: index.php?status=deleted');
exit;
