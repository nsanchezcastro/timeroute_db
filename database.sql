-- Estructura de la base de datos para TimeRoute
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Crear la tabla de usuarios.
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `rol` ENUM('admin', 'trabajador') DEFAULT 'trabajador'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Crear la tabla de jornadas (depende de usuarios).
CREATE TABLE IF NOT EXISTS `jornada_laboral` (
  `id_jornada` INT AUTO_INCREMENT PRIMARY KEY,
  `id_usuario` INT NOT NULL,
  `fecha` DATE NOT NULL,
  `hora_inicio` TIME NOT NULL,
  `hora_fin` TIME DEFAULT NULL,
  `latitud_inicio` DECIMAL(10, 8),
  `longitud_inicio` DECIMAL(11, 8),
  `total_horas` TIME DEFAULT NULL,
  FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar un usuario de prueba (Password: 123456)
INSERT INTO `usuarios` (`nombre`, `email`, `password`, `rol`) VALUES
('Admin Test', 'admin@timeroute.com', '$2y$10$8K.p6yT6YqSjC.B6V0uLDe7zB7yq8Yq8Yq8Yq8Yq8Yq8Yq8Yq8Yq8Y', 'admin');

COMMIT;