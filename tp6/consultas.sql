-- =====================================================================
-- consultas.sql  |  Librería Quin  |  TP6 - Consultas DML de prueba (CRUD)
-- Ejecutar DESPUÉS de schema.sql.
-- =====================================================================
USE libreria_quin;

-- =====================================================================
-- CREATE  (inserción de datos iniciales)
-- =====================================================================

-- Tablas maestras (roles y estados ya vienen cargados desde schema.sql)
INSERT INTO categorias (nombre, descripcion) VALUES
    ('Kits escolares',        'Combos completos de útiles'),
    ('Cuadernos y anotadores','Cuadernos, libretas, planners y separadores'),
    ('Escritura',             'Lapiceras, lápices, marcadores y resaltadores');

-- Usuarios con rol vinculado por clave foránea (roles.nombre).
-- El hash bcrypt de ejemplo corresponde a la contraseña: password   (cambiala en un entorno real)
INSERT INTO usuarios (nombre, email, password, telefono, rol) VALUES
    ('Administrador', 'admin@libreriaquin.test',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3875550001', 'admin'),
    ('Camila Pérez',  'camila@ejemplo.test',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3875550002', 'usuario');

-- Tabla principal (vinculada a categorías mediante clave foránea)
INSERT INTO productos (categoria_id, titulo, descripcion, precio, stock) VALUES
    (1, 'Kit Escolar Completo',
        'Paquete de 15 lápices de colores, 20 marcadores, lápiz negro, lapiceras, gomas, sacapuntas, tijera, corrector, pegamento, set de geometría y cartuchera.',
        20500.00, 25),
    (1, 'Combo Estudiantil',
        'Cuadernos con sistema de discos, anotador tipo checklist y set de resaltadores en colores pasteles.',
        6500.00, 40),
    (2, 'Combo de Cuaderno y Planner',
        'Cuaderno A4 con espiral de 80 hojas, libreta A6, check list y planner mensual A4.',
        22500.00, 18),
    (2, 'Set de Anotadores y Separadores',
        'Anotadores y separadores de distintos colores y formas para decorar cuadernos y carpetas.',
        12500.00, 30),
    -- Registro de prueba que se elimina al final (sección DELETE)
    (3, 'PRODUCTO DE PRUEBA',
        'Registro temporal para probar la baja con DELETE.',
        1.00, 1);

-- Un pedido de ejemplo con su detalle (total = 20.500 + 2 x 6.500)
INSERT INTO pedidos (usuario_id, estado_id, total) VALUES (2, 1, 33500.00);
INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio_unitario) VALUES
    (1, 1, 1, 20500.00),
    (1, 2, 2,  6500.00);

-- =====================================================================
-- READ  (consultas)
-- =====================================================================

-- 1) WHERE + ORDER BY: productos de la categoría 1 con precio menor a 25.000, del más caro al más barato
SELECT id, titulo, precio, stock
FROM productos
WHERE categoria_id = 1
  AND precio < 25000
ORDER BY precio DESC;

-- 2) INNER JOIN: producto junto con el nombre de su categoría
SELECT p.id,
       p.titulo,
       c.nombre AS categoria,
       p.precio
FROM productos AS p
INNER JOIN categorias AS c ON c.id = p.categoria_id
ORDER BY c.nombre ASC, p.titulo ASC;

-- 3) INNER JOIN múltiple: detalle de pedidos con cliente, estado, producto y subtotal
SELECT pe.id                         AS pedido,
       u.nombre                     AS cliente,
       es.nombre                     AS estado,
       pr.titulo                     AS producto,
       d.cantidad,
       d.precio_unitario,
       (d.cantidad * d.precio_unitario) AS subtotal
FROM pedidos AS pe
INNER JOIN usuarios        AS u  ON u.id  = pe.usuario_id
INNER JOIN estados_pedido  AS es ON es.id = pe.estado_id
INNER JOIN detalle_pedidos AS d  ON d.pedido_id = pe.id
INNER JOIN productos       AS pr ON pr.id = d.producto_id
ORDER BY pe.id, pr.titulo;

-- =====================================================================
-- UPDATE  (siempre con WHERE)
-- =====================================================================

-- Cambiar el precio de un producto específico
UPDATE productos
SET precio = 21900.00
WHERE id = 1;

-- Cambiar el estado de un pedido específico (pendiente -> pagado)
UPDATE pedidos
SET estado_id = 2
WHERE id = 1;

-- =====================================================================
-- DELETE  (siempre con WHERE)
-- =====================================================================

-- Dar de baja el registro de prueba (no tiene pedidos asociados)
DELETE FROM productos
WHERE titulo = 'PRODUCTO DE PRUEBA';
