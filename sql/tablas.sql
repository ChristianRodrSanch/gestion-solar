-- ========================================
-- CREACIÓN DE BASE DE DATOS
-- ========================================
DROP DATABASE IF EXISTS gestion_solar;
CREATE DATABASE gestion_solar;
USE gestion_solar;

-- ========================================
-- TABLA DE USUARIOS
-- ========================================
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(150) UNIQUE NOT NULL,
    contraseña VARCHAR(255) NOT NULL,
    rol ENUM('administrador', 'vendedor', 'cliente') NOT NULL,
    imagen VARCHAR(255) DEFAULT 'img/usuario.png',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ========================================
-- TABLA DE PRODUCTOS (GENÉRICA)
-- ========================================
CREATE TABLE productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL UNIQUE,
    descripcion TEXT,
    categoria ENUM('panel', 'bateria', 'inversor', 'accesorio') NOT NULL,
    precio_base DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    id_vendedor INT,
    imagen VARCHAR(255) DEFAULT 'img/producto.png',
    FOREIGN KEY (id_vendedor) REFERENCES usuarios(id_usuario)
        ON DELETE SET NULL
);

-- ========================================
-- TABLAS ESPECÍFICAS DE PRODUCTOS
-- ========================================
CREATE TABLE paneles (
    id_panel INT AUTO_INCREMENT PRIMARY KEY,
    modelo VARCHAR(100) UNIQUE NOT NULL,
    eficiencia DECIMAL(5,2) NOT NULL,
    superficie DECIMAL(6,2) NOT NULL,
    produccion DECIMAL(10,2) NOT NULL,
    id_producto INT UNIQUE NOT NULL,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
        ON DELETE CASCADE
);

CREATE TABLE baterias (
    id_bateria INT AUTO_INCREMENT PRIMARY KEY,
    modelo VARCHAR(100) UNIQUE NOT NULL,
    capacidad DECIMAL(10,2) NOT NULL,
    autonomia DECIMAL(10,2) NOT NULL,
    id_producto INT UNIQUE NOT NULL,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
        ON DELETE CASCADE
);

CREATE TABLE inversores (
    id_inversor INT AUTO_INCREMENT PRIMARY KEY,
    modelo VARCHAR(100) UNIQUE NOT NULL,
    potencia_nominal DECIMAL(10,2) NOT NULL,
    eficiencia DECIMAL(5,2) NOT NULL,
    tipo ENUM('on-grid','off-grid','hibrido') NOT NULL,
    id_producto INT UNIQUE NOT NULL,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
        ON DELETE CASCADE
);

-- ========================================
-- TABLA DE CONSUMOS
-- ========================================
CREATE TABLE consumos (
    id_consumo INT AUTO_INCREMENT PRIMARY KEY,
    aparato VARCHAR(100) NOT NULL,
    potencia DECIMAL(10,2) NOT NULL,
    horas_uso DECIMAL(10,2) NOT NULL,
    consumo_total DECIMAL(10,2) NOT NULL,
    fecha DATE NOT NULL,
    id_usuario INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON DELETE CASCADE
);

-- ========================================
-- TABLA DE RESULTADOS MySQL
-- ========================================
/*CREATE TABLE resultados (
    id_resultado INT AUTO_INCREMENT PRIMARY KEY,
    mes VARCHAR(20) NOT NULL,
    produccion DECIMAL(10,2) NOT NULL,
    consumo DECIMAL(10,2) NOT NULL,
    balance DECIMAL(10,2) AS (produccion - consumo) STORED,
    id_usuario INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON DELETE CASCADE
);*/

-- ========================================
-- TABLA DE RESULTADOS SQLITE
-- ========================================

CREATE TABLE resultados (
    id_resultado INTEGER PRIMARY KEY AUTOINCREMENT,
    mes TEXT NOT NULL,
    produccion DECIMAL(10,2) NOT NULL,
    consumo DECIMAL(10,2) NOT NULL,
    id_usuario INTEGER NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON DELETE CASCADE
);


/* 
Y luego, al hacer la consulta en Laravel, puedes calcular el balance así:

$resultados = DB::table('resultados')
    ->select('id_resultado', 'mes', 'produccion', 'consumo',
             DB::raw('produccion - consumo as balance'))
    ->where('id_usuario', $idUsuario)
    ->get();
*/

-- ========================================
-- TABLA DE COMPARATIVA DE PRECIOS
-- ========================================
CREATE TABLE comparativa_precios (
    id_comparativa INT AUTO_INCREMENT PRIMARY KEY,
    proveedor VARCHAR(100) NOT NULL,
    precio_ofrecido DECIMAL(10,2) NOT NULL,
    fecha_actualizacion DATE NOT NULL,
    id_producto INT NOT NULL,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
        ON DELETE CASCADE
);

-- ========================================
-- TABLA DE ESTUDIO DE MERCADO
-- ========================================
CREATE TABLE estudio_mercado (
    id_estudio INT AUTO_INCREMENT PRIMARY KEY,
    producto VARCHAR(100) NOT NULL,
    precio_min DECIMAL(10,2) NOT NULL,
    precio_max DECIMAL(10,2) NOT NULL,
    precio_promedio DECIMAL(10,2) NOT NULL,
    anio_estudio INTEGER NOT NULL,
    fuente_datos VARCHAR(150) NOT NULL,
    id_producto INT NOT NULL,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
        ON DELETE CASCADE
);

-- ========================================
-- TABLA DE INSTALACIONES
-- ========================================
CREATE TABLE instalaciones (
    id_instalacion INT AUTO_INCREMENT PRIMARY KEY,
    direccion VARCHAR(200) NOT NULL,
    descripcion TEXT NOT NULL,
    id_usuario INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON DELETE CASCADE
);

-- ========================================
-- TABLA DE MATERIALES UTILIZADOS
-- ========================================
CREATE TABLE materiales_utilizados (
    id_material INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    cantidad DECIMAL(10,2) NOT NULL,
    unidad VARCHAR(20) NOT NULL,
    coste_unitario DECIMAL(10,2) NOT NULL,
    coste_total DECIMAL(10,2) NOT NULL,
    id_instalacion INT NOT NULL,
    FOREIGN KEY (id_instalacion) REFERENCES instalaciones(id_instalacion)
        ON DELETE CASCADE
);

-- ========================================
-- TABLA DE TELEMETRÍA
-- ========================================
CREATE TABLE telemetria (
    id_telemetria INT AUTO_INCREMENT PRIMARY KEY,
    fecha_registro DATETIME,
    temperatura DECIMAL(5,2),
    presion DECIMAL(6,2),
    lux DECIMAL(10,2),
    id_instalacion INT,
    FOREIGN KEY (id_instalacion) REFERENCES instalaciones(id_instalacion)
        ON DELETE CASCADE
);

-- ========================================
-- TABLA DE CONSUMO ELÉCTRICO
-- ========================================
CREATE TABLE consumo_electrico (
    id_consumoElectrico INT AUTO_INCREMENT PRIMARY KEY,
    voltaje DECIMAL(10,2),
    intensidad DECIMAL(10,2),
    potencia DECIMAL(10,2),
    energia DECIMAL(10,2),
    factor_potencia DECIMAL(5,2),
    factor_voltaje DECIMAL(5,2),
    temperatura DECIMAL(5,2),
    id_telemetria INT UNIQUE,
    FOREIGN KEY (id_telemetria) REFERENCES telemetria(id_telemetria)
        ON DELETE CASCADE
);
