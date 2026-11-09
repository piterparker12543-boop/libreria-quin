<?php
// Lee el archivo .env de la raíz del proyecto sin dependencias externas.
function cargar_env(string $ruta): array {
    $variables = [];
    if (!is_file($ruta)) {
        return $variables;
    }
    foreach (file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linea) {
        $linea = trim($linea);
        if ($linea === '' || $linea[0] === '#' || strpos($linea, '=') === false) {
            continue;
        }
        [$clave, $valor] = explode('=', $linea, 2);
        $variables[trim($clave)] = trim($valor, " \t\"'");
    }
    return $variables;
}

$env = cargar_env(__DIR__ . '/../.env');

define('APP_NAME',  $env['APP_NAME']  ?? 'Librería Quin');
define('APP_EMAIL', $env['APP_EMAIL'] ?? 'contacto@libreriaquin.com');
define('APP_ENV',   $env['APP_ENV']   ?? 'local');
define('BASE_URL',  $env['BASE_URL']  ?? '');
define('DB_HOST',   $env['DB_HOST']   ?? 'localhost');
define('DB_NAME',   $env['DB_NAME']   ?? 'libreria_quin');
define('DB_USER',   $env['DB_USER']   ?? 'root');
define('DB_PASS',   $env['DB_PASS']   ?? '');
