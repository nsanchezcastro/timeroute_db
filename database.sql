-- 1. Tabla de Usuarios
CREATE TABLE `usuarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `rol` ENUM('admin', 'trabajador') DEFAULT 'trabajador'
);

-- 2. Tabla de Rutas
CREATE TABLE `rutas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL
);

-- 3. Tabla de Clientes
CREATE TABLE `clientes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `direccion` VARCHAR(255) NOT NULL,
  `lat` DECIMAL(10, 8),
  `lng` DECIMAL(11, 8)
);

-- 4. Tabla de Jornadas
CREATE TABLE `jornadas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `id_usuario` INT NOT NULL,
  `id_ruta` INT NOT NULL,
  `fecha` DATE NOT NULL,
  `inicio_plan` DATETIME,
  `fin_plan` DATETIME,
  `estado` ENUM('CREADA', 'EN_CURSO', 'FINALIZADA') DEFAULT 'CREADA',
  `pausa_activa` TINYINT(1) DEFAULT 0,
  FOREIGN KEY (`id_usuario`) REFERENCES `usuarios`(`id`),
  FOREIGN KEY (`id_ruta`) REFERENCES `rutas`(`id`)
);

-- 5. Tabla de Visitas
CREATE TABLE `visitas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `id_jornada` INT NOT NULL,
  `id_cliente` INT NOT NULL,
  `orden` INT NOT NULL,
  `llegada` DATETIME DEFAULT NULL,
  `salida` DATETIME DEFAULT NULL,
  `duracion_minutos` INT DEFAULT 0,
  `incidencia` TEXT,
  FOREIGN KEY (`id_jornada`) REFERENCES `jornadas`(`id`),
  FOREIGN KEY (`id_cliente`) REFERENCES `clientes`(`id`)
);