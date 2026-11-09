<?php
// Menú principal (SSI). La clase "active" depende de $seccion_activa definida en cada página.
$seccion_activa = $seccion_activa ?? '';
$menu = [
    'inicio'    => ['index.php', 'Inicio'],
    'productos' => ['productos.php', 'Productos'],
    'buscar'    => ['buscar.php', 'Buscar'],
    'contacto'  => ['contacto.php', 'Comentarios y preguntas'],
    'faq'       => ['faq.php', 'Preguntas Frecuentes'],
    'quienes'   => ['quienes-somos.php', 'Quiénes somos'],
];
$logueado = isset($_SESSION['usuario_id']);
$es_admin = $logueado && ($_SESSION['rol'] ?? '') === 'admin';
?>
<nav aria-label="Menú principal">
    <ul class="menu">
        <?php foreach ($menu as $clave => [$url, $texto]): ?>
            <li><a href="<?= BASE_URL ?>/<?= e($url) ?>" class="<?= $seccion_activa === $clave ? 'active' : '' ?>"><?= e($texto) ?></a></li>
        <?php endforeach; ?>
        <?php if ($logueado): ?>
            <li><a href="<?= BASE_URL ?>/cuenta.php" class="<?= $seccion_activa === 'cuenta' ? 'active' : '' ?>">Cuenta</a></li>
            <?php if ($es_admin): ?>
                <li><a href="<?= BASE_URL ?>/admin/index.php" class="<?= $seccion_activa === 'admin' ? 'active' : '' ?>">Administración</a></li>
            <?php endif; ?>
            <li><a href="<?= BASE_URL ?>/auth/logout.php">Cerrar sesión</a></li>
        <?php else: ?>
            <li><a href="<?= BASE_URL ?>/auth/login.php" class="<?= $seccion_activa === 'login' ? 'active' : '' ?>">Ingresar</a></li>
            <li><a href="<?= BASE_URL ?>/auth/registro.php" class="<?= $seccion_activa === 'registro' ? 'active' : '' ?>">Registrarse</a></li>
        <?php endif; ?>
    </ul>
</nav>
