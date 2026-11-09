-- =====================================================================
-- schema.sql  |  Librería Quin  |  TP6 - Esquema DDL (MySQL / MariaDB)
-- Ejecutar en phpMyAdmin (pestaña SQL) o: mysql -u root < schema.sql
-- =====================================================================

CREATE DATABASE IF NOT EXISTS libreria_quin
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE libreria_quin;

-- Reinicio seguro para poder ejecutar el script varias veces
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS detalle_pedidos;
DROP TABLE IF EXISTS pedidos;
DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS estados_pedido;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS roles;
SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
-- Tablas maestras
-- ---------------------------------------------------------------------
CREATE TABLE roles (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(50)  NOT NULL UNIQUE,
    descripcion VARCHAR(255) NULL
) ENGINE=InnoDB;

CREATE TABLE categorias (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(80)  NOT NULL UNIQUE,
    descripcion VARCHAR(255) NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE estados_pedido (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(40) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Usuarios (autenticación, rol y datos personales) - estructura del TP8
-- ---------------------------------------------------------------------
CREATE TABLE usuarios (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nombre     VARCHAR(80)  NOT NULL,
    email      VARCHAR(150) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,                 -- hash de password_hash(); nunca texto plano
    telefono   VARCHAR(30)  NULL,
    rol        VARCHAR(50)  NOT NULL DEFAULT 'usuario',  -- 'admin' / 'usuario'
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuarios_rol
        FOREIGN KEY (rol) REFERENCES roles(nombre)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Tabla principal de negocio: productos
-- ---------------------------------------------------------------------
CREATE TABLE productos (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT           NOT NULL,
    titulo       VARCHAR(150)  NOT NULL,
    descripcion  TEXT          NOT NULL,
    precio       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    stock        INT           NOT NULL DEFAULT 0,
    imagen       VARCHAR(255)  NULL,
    activo       TINYINT(1)    NOT NULL DEFAULT 1,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_productos_categoria
        FOREIGN KEY (categoria_id) REFERENCES categorias(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_productos_precio CHECK (precio >= 0),
    CONSTRAINT chk_productos_stock  CHECK (stock  >= 0)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Pedidos y detalle (relación N:M entre pedidos y productos)
-- ---------------------------------------------------------------------
CREATE TABLE pedidos (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT           NOT NULL,
    estado_id  INT           NOT NULL,
    total      DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_pedidos_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_pedidos_estado
        FOREIGN KEY (estado_id) REFERENCES estados_pedido(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE detalle_pedidos (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id       INT           NOT NULL,
    producto_id     INT           NOT NULL,
    cantidad        INT           NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(10,2) NOT NULL,   -- precio al momento de la compra
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_detalle_pedido
        FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_detalle_producto
        FOREIGN KEY (producto_id) REFERENCES productos(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT uq_detalle_pedido_producto UNIQUE (pedido_id, producto_id),
    CONSTRAINT chk_detalle_cantidad CHECK (cantidad > 0)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Índices explícitos (filtros y uniones frecuentes)
-- ---------------------------------------------------------------------
CREATE INDEX idx_productos_categoria_activo ON productos (categoria_id, activo);
CREATE INDEX idx_productos_titulo           ON productos (titulo);
CREATE INDEX idx_pedidos_usuario            ON pedidos (usuario_id);
CREATE INDEX idx_usuarios_rol               ON usuarios (rol);

-- ---------------------------------------------------------------------
-- Datos de referencia (la aplicación los necesita para funcionar)
-- ---------------------------------------------------------------------
INSERT INTO roles (nombre, descripcion) VALUES
    ('admin',   'Gestiona productos, pedidos y usuarios'),
    ('usuario', 'Usuario registrado del sitio');

INSERT INTO estados_pedido (nombre) VALUES
    ('pendiente'), ('pagado'), ('enviado'), ('entregado'), ('cancelado');
