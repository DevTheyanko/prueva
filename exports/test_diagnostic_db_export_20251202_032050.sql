-- Base de datos: test_diagnostic_db
-- Generado: 2025-12-02 03:20:50

CREATE DATABASE IF NOT EXISTS `test_diagnostic_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `test_diagnostic_db`;

-- Tabla: test_table
DROP TABLE IF EXISTS `test_table`;
CREATE TABLE `test_table` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `edad` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

