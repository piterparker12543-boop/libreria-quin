# Librería Quin: sitio web en PHP

## 1. Título y descripción
Sitio de la librería Quin, con cuadernos, útiles escolares y cursos. Está migrado de la maqueta estática a PHP con plantillas SSI, títulos dinámicos y variables de entorno. Incluye formularios con POST y GET, un CRUD de productos con PDO y registro, login y control de acceso por roles.

**Enlace al prototipo de Figma:** [pegar enlace de Figma con permiso de lectura]

## 2. Tecnologías utilizadas
- HTML5, CSS3
- JavaScript (alert, validación, fecha/hora, acordeón, galería, modo oscuro)
- PHP 8.x
- MySQL / MariaDB (phpMyAdmin)
- WAMPSERVER

## 3. Instalación y ejecución local (WAMPSERVER)
1. Instalar WAMPSERVER 64 bits y abrirlo. El icono de la bandeja debe quedar en verde.
2. Si el puerto 80 está ocupado (por ejemplo, por Skype), cambiar el puerto de Apache desde el menú de WAMP → Apache → Service → Port.
3. Copiar la carpeta `libreria-quin` en `C:\wamp64\www\`.
4. Copiar `.env.example` como `.env` en la raíz del proyecto. Revisar `BASE_URL=/libreria-quin`.
5. Entrar a `http://localhost/phpmyadmin`, pestaña SQL, y ejecutar `sql/schema.sql` y después `sql/consultas.sql`.
6. Copiar el JavaScript de la página publicada en `js/app.js`.
7. Abrir `http://localhost/libreria-quin/`.
8. Para la versión de PHP: menú WAMP → PHP → Version, elegir 8.x.

Para crear un administrador: registrarse en `/auth/registro.php` y luego ejecutar en phpMyAdmin `UPDATE usuarios SET rol='admin' WHERE email='tu@correo.com';`

## 4. Estructura modular (SSI)
- `includes/header.php`: `<head>`, metadatos, CSS y apertura de `<body>`. Usa la variable `$titulo_pagina`.
- `includes/nav.php`: menú. La clase `active` depende de `$seccion_activa`.
- `includes/footer.php`: pie con `<hr>` y script JavaScript.
- Cada página hace `require_once` de estas plantillas.

## 5. Variables de entorno
- `.env.example`: plantilla pública con APP_NAME, APP_EMAIL, APP_ENV y datos de base de datos.
- `.env`: archivo local, listado en `.gitignore`, nunca se sube al repositorio.
- `config/env.php`: lee `.env` y define las constantes.

## 6. Formularios (Clase 5)
- **POST, `contacto.php`:** formulario de comentarios y preguntas. Valida nombre, correo (formato con `FILTER_VALIDATE_EMAIL`) y mensaje. Muestra errores bajo cada campo, conserva los datos ingresados si hay error y guarda el mensaje en la tabla `contactos`.
- **GET, `buscar.php`:** búsqueda de productos. Valida que el término no esté vacío y tenga hasta 100 caracteres, y consulta con sentencias preparadas.
- **Sanitización:** las entradas pasan por `trim()` y `strip_tags()` (funciones `post()` y `get()`), y toda salida usa `htmlspecialchars()` (función `e()`).

## 7. CRUD con PDO (Clase 7)
- `config/conexion.php`: conexión PDO con try-catch (`PDOException`).
- `admin/index.php` (Read), `admin/crear.php` (Create), `admin/editar.php` (Update), `admin/eliminar.php` (Delete).
- Todas las consultas usan sentencias preparadas con marcadores `:parametro`.

## 8. Login, registro y control de acceso (Clase 8)
- `auth/registro.php`: verifica que el correo no exista, guarda la contraseña con `password_hash(..., PASSWORD_BCRYPT)` y usa consultas preparadas.
- `auth/login.php`: valida con `password_verify()`. Al ingresar llama a `session_start()` y `session_regenerate_id(true)`, y guarda `$_SESSION['usuario_id']`, `$_SESSION['usuario_nombre']` y `$_SESSION['rol']`.
- `auth/auth_guard.php`: función `proteger_ruta()` que verifica `isset($_SESSION['usuario_id'])` y redirige a `login.php?error=acceso_denegado`. Con un parámetro de rol, restringe la página.
- Rutas protegidas: `cuenta.php` (cualquier usuario logueado) y `admin/*` (solo rol admin).
- `auth/logout.php`: vacía `$_SESSION`, borra la cookie de sesión y llama a `session_destroy()`.

## 9. Capturas de pantalla para la entrega
Guardarlas en `docs/capturas/` y referenciarlas acá:
1. Sitio corriendo en `http://localhost/libreria-quin/`.
2. Código de `includes/header.php`, `nav.php` y `footer.php`.
3. Pestaña del navegador con el título dinámico (por ejemplo, "Productos | Librería Quin" y "Buscar | Librería Quin").
4. Menú con la clase `active` en la sección actual.
5. Contenido de `.env.example`.
6. Formulario de contacto con mensajes de validación y búsqueda con resultados.
7. Lighthouse: F12 → pestaña Lighthouse → Analyze page load, con captura del informe.
8. Base de datos en phpMyAdmin con las tablas creadas.

## 10. Flujo de Git
```bash
git checkout -b feature/migracion-php-ssi
git add .
git commit -m "feat: migra maquetado html a php e implementa header/footer dinamico"
git push -u origin feature/migracion-php-ssi
```
Luego crear un Pull Request hacia `main`. Para las clases siguientes, usar ramas como `feature/formularios-post-get`, `feature/crud-pdo` y `feature/login-sesiones`, con commits atómicos y descriptivos.

Para la Clase 5 y las siguientes, la carpeta en el repositorio debe llamarse `clase5/`, según la consigna.
