-- 1. Tabla de Usuarios 
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `rol` ENUM('admin', 'worker') DEFAULT 'worker'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Tabla de Pacientes (Direcciones fijas con GPS)
CREATE TABLE IF NOT EXISTS `pacientes` (
  `id_paciente` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `direccion` VARCHAR(255) NOT NULL,
  `latitud` DECIMAL(10, 8),
  `longitud` DECIMAL(11, 8)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabla de Asignaciones (La "Hoja de Ruta" diaria)
CREATE TABLE IF NOT EXISTS `asignaciones` (
  `id_asignacion` INT AUTO_INCREMENT PRIMARY KEY,
  `id_usuario` INT NOT NULL,
  `id_paciente` INT NOT NULL,
  `fecha` DATE NOT NULL,
  `orden_visita` INT NOT NULL, -- 1, 2, 3... para optimizar la ruta
  `estado` ENUM('pendiente', 'en_curso', 'completado', 'incidencia') DEFAULT 'pendiente',
  FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id_paciente`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Tabla de Registros de Visita 
CREATE TABLE IF NOT EXISTS `registros_visita` (
  `id_registro` INT AUTO_INCREMENT PRIMARY KEY,
  `id_asignacion` INT NOT NULL,
  `hora_inicio` DATETIME NOT NULL,
  `hora_fin` DATETIME DEFAULT NULL,
  `minutos_visita` INT DEFAULT 0, -- Aquí calculamos el tiempo con el paciente
  `comentario_incidencia` TEXT DEFAULT NULL,
  FOREIGN KEY (`id_asignacion`) REFERENCES `asignaciones` (`id_asignacion`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;