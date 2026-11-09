<?php
/**
 * Formulario reutilizable de producto (lo usan crear.php y editar.php).
 * Variables esperadas: $valores, $errores, $categorias, $textoBoton
 */
$campoInvalido = static fn(string $campo): string => isset($errores[$campo]) ? ' invalido' : '';
?>
<form method="post" action="" class="form-card" novalidate>
    <?= csrf_campo() ?>

    <div class="campo">
        <label for="titulo">Título</label>
        <input type="text" id="titulo" name="titulo" maxlength="150"
               class="<?= trim($campoInvalido('titulo')) ?>"
               value="<?= e($valores['titulo']) ?>">
        <?php if (isset($errores['titulo'])): ?><span class="error-campo"><?= e($errores['titulo']) ?></span><?php endif; ?>
    </div>

    <div class="campo">
        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion" maxlength="1000"
                  class="<?= trim($campoInvalido('descripcion')) ?>"><?= e($valores['descripcion']) ?></textarea>
        <?php if (isset($errores['descripcion'])): ?><span class="error-campo"><?= e($errores['descripcion']) ?></span><?php endif; ?>
    </div>

    <div class="fila-campos">
        <div class="campo">
            <label for="precio">Precio ($)</label>
            <input type="text" id="precio" name="precio" inputmode="decimal"
                   class="<?= trim($campoInvalido('precio')) ?>"
                   value="<?= e($valores['precio']) ?>">
            <?php if (isset($errores['precio'])): ?><span class="error-campo"><?= e($errores['precio']) ?></span><?php endif; ?>
        </div>

        <div class="campo">
            <label for="stock">Stock</label>
            <input type="number" id="stock" name="stock" min="0" step="1"
                   class="<?= trim($campoInvalido('stock')) ?>"
                   value="<?= e($valores['stock']) ?>">
            <?php if (isset($errores['stock'])): ?><span class="error-campo"><?= e($errores['stock']) ?></span><?php endif; ?>
        </div>

        <div class="campo">
            <label for="categoria_id">Categoría</label>
            <select id="categoria_id" name="categoria_id" class="<?= trim($campoInvalido('categoria_id')) ?>">
                <option value="">Elegí una categoría</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= e($cat['id']) ?>" <?= (string) $valores['categoria_id'] === (string) $cat['id'] ? 'selected' : '' ?>>
                        <?= e($cat['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errores['categoria_id'])): ?><span class="error-campo"><?= e($errores['categoria_id']) ?></span><?php endif; ?>
        </div>
    </div>

    <button type="submit" class="btn-modulo"><?= e($textoBoton) ?></button>
    <a href="index.php" class="btn-modulo btn-secundario">Cancelar</a>
</form>
