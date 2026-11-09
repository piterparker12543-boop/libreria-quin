<?php
// Formulario compartido por crear.php y editar.php. Espera $datos, $errores y $categorias.
?>
<form method="post" action="" novalidate>
    <div class="field"><label for="titulo">Título</label>
        <input id="titulo" name="titulo" maxlength="150" value="<?= e($datos['titulo']) ?>">
        <span class="error"><?= e($errores['titulo'] ?? '') ?></span></div>
    <div class="field"><label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion" rows="4"><?= e($datos['descripcion']) ?></textarea></div>
    <div class="field"><label for="precio">Precio (use punto decimal, ej: 2500.50)</label>
        <input id="precio" name="precio" value="<?= e((string)$datos['precio']) ?>">
        <span class="error"><?= e($errores['precio'] ?? '') ?></span></div>
    <div class="field"><label for="categoria_id">Categoría</label>
        <select id="categoria_id" name="categoria_id">
            <option value="">Sin categoría</option>
            <?php foreach ($categorias as $c): ?>
                <option value="<?= (int)$c['id'] ?>" <?= (string)$datos['categoria_id'] === (string)$c['id'] ? 'selected' : '' ?>><?= e($c['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
        <span class="error"><?= e($errores['categoria_id'] ?? '') ?></span></div>
    <button class="btn fill" type="submit">Guardar</button>
    <a class="btn" href="index.php">Cancelar</a>
</form>
