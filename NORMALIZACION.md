# Parte 1: Normalización de Librería Quin

## 1. Tabla sin normalizar

Registro hipotético de pedidos, con teléfonos, productos y ubicación en campos únicos:

| pedido_id | cliente | email | telefonos | productos | categoria | provincia | ciudad | total |
|---|---|---|---|---|---|---|---|---|
| 1 | Nick | nick@mail.com | 3874111222, 3874333444 | Cuaderno A4 x2; Resaltador x1 | Cuadernos; Útiles | Salta | Salta Capital | 7500 |
| 2 | Camila | camila@mail.com | 3875555666 | Kit escolar x1 | Útiles | Buenos Aires | La Plata | 20500 |

Problemas: campos multivalorados (telefonos, productos, categoria), datos del cliente repetidos en cada pedido y dependencias transitivas (ciudad → provincia).

## 2. Primera forma normal (1FN)

Cada campo debe ser atómico y no debe haber grupos repetidos. Se separa cada teléfono, cada producto y cada categoría en su propia fila.

| pedido_id | cliente | email | telefono | producto | cantidad | categoria | provincia | ciudad | total |
|---|---|---|---|---|---|---|---|---|---|
| 1 | Nick | nick@mail.com | 3874111222 | Cuaderno A4 | 2 | Cuadernos | Salta | Salta Capital | 7500 |
| 1 | Nick | nick@mail.com | 3874333444 | Resaltador | 1 | Útiles | Salta | Salta Capital | 7500 |

## 3. Segunda forma normal (2FN)

La clave compuesta es (pedido_id, producto). Los datos del cliente dependen solo de pedido_id, y el total depende solo de pedido_id, por lo que son dependencias parciales. Se separan las tablas:

- **pedidos**(pedido_id, cliente, email, total)
- **detalle_pedidos**(pedido_id, producto, cantidad)
- **telefonos_cliente**(email, telefono)

Los datos de producto (categoría, precio) dependen solo de producto, así que también se separan.

## 4. Tercera forma normal (3FN)

Se eliminan las dependencias transitivas:
- ciudad → provincia: se extraen **provincias** y **ciudades**.
- producto → categoría: se extrae **categorias**.

## 5. Tablas resultantes

| Tabla | Clave primaria (PK) | Claves foráneas (FK) |
|---|---|---|
| provincias | id | - |
| ciudades | id | provincia_id → provincias.id |
| categorias | id | - |
| usuarios | id | ciudad_id → ciudades.id |
| telefonos_usuario | id | usuario_id → usuarios.id |
| productos | id | categoria_id → categorias.id |
| pedidos | id | usuario_id → usuarios.id |
| detalle_pedidos | (pedido_id, producto_id) | pedido_id → pedidos.id, producto_id → productos.id |
| contactos | id | - |

Nota: en la base del proyecto, el rol del usuario se guarda como ENUM ('admin', 'usuario') en la tabla usuarios, como pide el TP8. Los teléfonos pasan a su tabla propia, según la 1FN y la 3FN.
