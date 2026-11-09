<?php
// Página de cuenta (protegida). Pestañas por GET: ?tab=publicaciones|megusta|respuestas|crear|cv
require_once __DIR__ . '/auth/auth_guard.php';
require_once __DIR__ . '/includes/funciones.php';
proteger_ruta();

$tabs = [
    'publicaciones' => 'Publicaciones',
    'megusta'       => 'Me Gusta',
    'respuestas'    => 'Respuestas',
    'crear'         => 'Crear Publicaciones',
    'cv'            => 'Crea tu CV',
];
$tab = get('tab');
if (!isset($tabs[$tab])) {
    $tab = 'publicaciones';
}

// Pestaña Publicaciones: productos de la base de datos
$publicaciones = [];
if ($tab === 'publicaciones') {
    $stmt = $pdo->prepare("SELECT titulo, descripcion, precio, imagen FROM productos WHERE estado = 'disponible' ORDER BY id DESC");
    $stmt->execute();
    $publicaciones = $stmt->fetchAll();
}

// Pestaña Crea tu CV: campos del formulario
$campos_cv = [
    'nombre' => 'Nombre', 'nacimiento' => 'Fecha de nacimiento', 'direccion' => 'Dirección',
    'telefono' => 'Teléfono', 'correo' => 'Correo electrónico', 'institucion' => 'Nombre de institución',
    'titulo' => 'Título', 'estado' => 'Estado', 'tipo' => 'Tipo de experiencia', 'periodo' => 'Periodo',
    'rol' => 'Rol en ese trabajo', 'cursos' => 'Cursos y capacitación', 'habilidades' => 'Habilidades',
    'idiomas' => 'Idiomas', 'referencias' => 'Referencias', 'perfil' => 'Perfil profesional',
];
$cv = array_fill_keys(array_keys($campos_cv), '');
$errores = [];
$cv_ok = false;

if ($tab === 'cv' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (array_keys($cv) as $clave) {
        $cv[$clave] = post($clave);
    }
    if ($cv['nombre'] === '') {
        $errores['nombre'] = 'El nombre es obligatorio.';
    }
    if ($cv['correo'] === '') {
        $errores['correo'] = 'El correo es obligatorio.';
    } elseif (!filter_var($cv['correo'], FILTER_VALIDATE_EMAIL)) {
        $errores['correo'] = 'El formato del correo no es válido.';
    }
    $cv_ok = !$errores;
}

// Campo de formulario reutilizable para el CV
function campo_cv(string $clave, string $etiqueta, array $cv, array $errores, string $tipo = 'text'): void {
    $id = 'cv-' . $clave;
    echo '<div class="field"><label for="' . $id . '">' . e($etiqueta) . '</label>';
    if ($tipo === 'textarea') {
        echo '<textarea id="' . $id . '" name="' . e($clave) . '" rows="3">' . e($cv[$clave]) . '</textarea>';
    } else {
        echo '<input id="' . $id . '" name="' . e($clave) . '" type="' . e($tipo) . '" value="' . e($cv[$clave]) . '">';
    }
    echo '<span class="error">' . e($errores[$clave] ?? '') . '</span></div>';
}

$titulo_pagina = 'Mi cuenta';
$seccion_activa = 'cuenta';
require_once __DIR__ . '/includes/header.php';
$inicial = mb_strtoupper(mb_substr($_SESSION['usuario_nombre'], 0, 1));
?>
<div class="perfil">
    <div class="avatar" aria-hidden="true"><?= e($inicial) ?></div>
    <h2><?= e($_SESSION['usuario_nombre']) ?></h2>
</div>

<p>Última visita: <?= $ultima_visita ? e($ultima_visita) : 'esta es tu primera visita' ?></p>

<nav class="tabs" aria-label="Secciones de la cuenta">
    <?php foreach ($tabs as $clave => $texto): ?>
        <a href="?tab=<?= e($clave) ?>" class="<?= $tab === $clave ? 'activo' : '' ?>"><?= e($texto) ?></a>
    <?php endforeach; ?>
</nav>

<?php if ($tab === 'publicaciones'): ?>
    <?php if (!$publicaciones): ?><p>Todavía no hay publicaciones.</p><?php endif; ?>
    <?php foreach ($publicaciones as $p): ?>
        <article class="post">
            <?php if (!empty($p['imagen'])): ?>
                <img src="<?= BASE_URL ?>/img/<?= e($p['imagen']) ?>" alt="<?= e($p['titulo']) ?>">
            <?php endif; ?>
            <div>
                <p><?= e($p['descripcion']) ?></p>
                <p class="precio">Costo: $<?= number_format((float)$p['precio'], 2, ',', '.') ?></p>
            </div>
        </article>
    <?php endforeach; ?>

<?php elseif ($tab === 'megusta'): ?>
    <p>Todavía no marcaste publicaciones con me gusta.</p>

<?php elseif ($tab === 'respuestas'): ?>
    <div class="post"><div><p class="stars">★★★☆☆</p><p>“Está bien, cumple con la publicación. Nada espectacular, pero tampoco malo. El envío fue rápido.”</p></div></div>
    <div class="post"><div><p class="stars">★★★★☆</p><p>“Buena relación precio-calidad, cumple con la publicación, aunque uno de los marcadores vino medio seco.”</p></div></div>
    <div class="post"><div><p class="stars">★★★☆☆</p><p>“Los productos son buenos pero tengo una duda. ¿En el combo de con la carpeta pueden venir un block de hoja rayada y otro cuadriculada?”</p></div></div>

<?php elseif ($tab === 'crear'): ?>
    <?php if ($_SESSION['rol'] === 'admin'): ?>
        <p>Como administrador podés crear productos desde el panel.</p>
        <p><a class="btn fill" href="<?= BASE_URL ?>/admin/crear.php">Crear publicación</a></p>
    <?php else: ?>
        <p>Por ahora solo los administradores pueden crear publicaciones.</p>
    <?php endif; ?>

<?php elseif ($tab === 'cv'): ?>
    <?php if ($cv_ok): ?>
        <div class="resumen">
            <h3>Resumen de tu CV</h3>
            <?php foreach ($campos_cv as $clave => $etiqueta): ?>
                <p><b><?= e($etiqueta) ?>:</b> <?= e($cv[$clave] !== '' ? $cv[$clave] : '—') ?></p>
            <?php endforeach; ?>
            <p><a class="btn" href="?tab=cv">Editar</a></p>
        </div>
    <?php else: ?>
        <form method="post" action="?tab=cv" novalidate>
            <fieldset><legend>Datos Personales</legend>
                <?php campo_cv('nombre', 'Nombre', $cv, $errores); ?>
                <?php campo_cv('nacimiento', 'Fecha de nacimiento', $cv, $errores, 'date'); ?>
                <?php campo_cv('direccion', 'Dirección', $cv, $errores); ?>
                <?php campo_cv('telefono', 'Teléfono', $cv, $errores, 'tel'); ?>
                <?php campo_cv('correo', 'Correo electrónico', $cv, $errores, 'email'); ?>
            </fieldset>
            <fieldset><legend>Formación Académica</legend>
                <?php campo_cv('institucion', 'Nombre de institución', $cv, $errores); ?>
                <?php campo_cv('titulo', 'Título', $cv, $errores); ?>
                <?php campo_cv('estado', 'Estado', $cv, $errores); ?>
            </fieldset>
            <fieldset><legend>Experiencia Laboral</legend>
                <?php campo_cv('tipo', 'Tipo de experiencia', $cv, $errores); ?>
                <?php campo_cv('periodo', 'Periodo', $cv, $errores); ?>
                <?php campo_cv('rol', 'Rol en ese trabajo', $cv, $errores); ?>
            </fieldset>
            <fieldset><legend>Cursos, habilidades e idiomas</legend>
                <?php campo_cv('cursos', 'Cursos y capacitación (separados por coma)', $cv, $errores, 'textarea'); ?>
                <?php campo_cv('habilidades', 'Habilidades (separadas por coma)', $cv, $errores, 'textarea'); ?>
                <?php campo_cv('idiomas', 'Idiomas (separados por coma)', $cv, $errores, 'textarea'); ?>
            </fieldset>
            <fieldset><legend>Referencias y perfil</legend>
                <?php campo_cv('referencias', 'Referencias', $cv, $errores, 'textarea'); ?>
                <?php campo_cv('perfil', 'Perfil profesional', $cv, $errores, 'textarea'); ?>
            </fieldset>
            <button class="btn fill" type="submit">Revisar CV</button>
        </form>
    <?php endif; ?>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>