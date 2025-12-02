-- Base de datos: test_diagnostic_1764688331534
-- Generado: 2025-12-02 16:12:11

CREATE DATABASE IF NOT EXISTS `test_diagnostic_1764688331534` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `test_diagnostic_1764688331534`;

-- Tabla: test_users
DROP TABLE IF EXISTS `test_users`;
CREATE TABLE `test_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

