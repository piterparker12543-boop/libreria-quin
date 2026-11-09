<?php
// Formulario con envío GET: búsqueda de productos (TP5 y TP7).
$titulo_pagina = 'Buscar';
$seccion_activa = 'buscar';
require_once __DIR__ . '/includes/header.php';

$q = get('q');
$resultados = [];
$mensaje = '';
if (isset($_GET['q'])) {
    if ($q === '') {
        $mensaje = 'Escribí algo para buscar.';
    } elseif (mb_strlen($q) > 100) {
        $mensaje = 'La búsqueda no puede superar 100 caracteres.';
    } else {
        // Con ATTR_EMULATE_PREPARES=false no se puede repetir un mismo marcador: se usan :q1 y :q2.
        $stmt = $pdo->prepare(
            "SELECT titulo, precio FROM productos
             WHERE estado = 'disponible' AND (titulo LIKE :q1 OR descripcion LIKE :q2)
             ORDER BY titulo"
        );
        $patron = '%' . $q . '%';
        $stmt->execute([':q1' => $patron, ':q2' => $patron]);
        $resultados = $stmt->fetchAll();
        if (!$resultados) {
            $mensaje = 'No se encontraron productos para "' . $q . '".';
        }
    }
}
?>
<h2>Buscar productos</h2>
<form method="get" action="" novalidate>
    <div class="field"><label for="q">Término de búsqueda</label>
        <input id="q" name="q" value="<?= e($q) ?>" maxlength="100"></div>
    <button class="btn fill" type="submit">Buscar</button>
</form>
<?php if ($mensaje): ?><p class="error"><?= e($mensaje) ?></p><?php endif; ?>
<?php foreach ($resultados as $r): ?>
    <article class="card">
        <div>
            <h3><?= e($r['titulo']) ?></h3>
            <p>$<?= number_format((float)$r['precio'], 2, ',', '.') ?></p>
        </div>
    </article>
<?php endforeach; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>