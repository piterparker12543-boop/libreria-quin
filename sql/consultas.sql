-- consultas.sql: pruebas CRUD sobre libreria_quin (ejecutar después de schema.sql)
USE libreria_quin;

-- CREATE: datos en tablas maestras
INSERT INTO categorias (nombre) VALUES ('Cuadernos'), ('Útiles escolares');
INSERT INTO provincias (nombre) VALUES ('Salta'), ('Buenos Aires');
INSERT INTO ciudades (nombre, provincia_id) VALUES ('Salta Capital', 1), ('La Plata', 2);

-- CREATE: datos en la tabla principal vinculados por FK
INSERT INTO productos (titulo, descripcion, precio, categoria_id) VALUES
    ('Cuaderno A4 tapa blanda', '80 hojas cuadriculadas', 2500.00, 1),
    ('Kit escolar completo', 'Lápices, marcadores, cartuchera y más', 20500.00, 2);

-- Registro de prueba para luego eliminarlo
INSERT INTO contactos (nombre, email, mensaje) VALUES ('Prueba', 'prueba@test.com', 'Mensaje de prueba');

-- READ: WHERE + ORDER BY
SELECT titulo, precio FROM productos WHERE precio > 2000 AND estado = 'disponible' ORDER BY precio DESC;

-- READ: INNER JOIN entre productos y categorias
SELECT p.titulo, c.nombre AS categoria, p.precio
FROM productos p
INNER JOIN categorias c ON c.id = p.categoria_id
ORDER BY c.nombre, p.titulo;

-- UPDATE: con WHERE
UPDATE productos SET precio = 2700.00 WHERE id = 1;

-- DELETE: con WHERE
DELETE FROM contactos WHERE email = 'prueba@test.com';

-- Para crear un administrador: registrate en localhost/libreria-quin/auth/registro.php y luego ejecutá:
-- UPDATE usuarios SET rol = 'admin' WHERE email = 'tu@correo.com';
