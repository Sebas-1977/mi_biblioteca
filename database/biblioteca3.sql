SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- 1. TABLA Y DATOS: usuarios
-- --------------------------------------------------------
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `confirmado` tinyint(1) NOT NULL,
  `admin` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `password`, `token`, `confirmado`, `admin`) VALUES
(2, 'Pedro', 'López', 'pedro@correo.com', '$2y$10$gbAvyS9di4/jS3yZFkyaouGdvHmi.ezbmswIRBM8veXGg8IokpV2y', '', 1, 0),
(3, 'Juan', 'Pérez', 'juan@correo.com', '$2y$10$7PNeiq7W8UvTR85i9awdDu5LDXqZGLSn6EHnNMPsA6lHebuemZD6i', '', 1, 0),
(4, 'Fabiana', 'Díaz', 'fabiana@correo.com', '$2y$10$Id/1s1ULzGBXqVu9cpIQqO88b1UIAAPsUE2Mo9wZCFZgkQsK.V21m', '', 1, 0),
(5, 'Ana', 'Juárez', 'ana@correo.com', '$2y$10$zNkHbfEFtGr7UVfX44lFwei4DVD0Z9PZ6ICjxAEgFW.QQNY42teFC', '', 1, 0),
(6, 'Javier ', 'Méndez', 'javier@correo.com', '$2y$10$naXvanVQFKWnVyoLBMgHS.9HpQXwtfAUqLRd8x5805HdMt6iOY/4C', '', 1, 0),
(7, 'admin', 'admin', 'admin@admin.com', '$2y$10$KYVg16Y.DRXsyI7/7TdMWOOwIq0OXd9S.UQcQjJ8mNoip/lcTPhBK', '', 1, 1);

-- --------------------------------------------------------
-- 2. TABLA Y DATOS: autores
-- --------------------------------------------------------
DROP TABLE IF EXISTS `autores`;
CREATE TABLE IF NOT EXISTS `autores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nacionalidad` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `autores` (`id`, `nombre`, `apellido`, `nacionalidad`, `fecha_nacimiento`, `activo`) VALUES
(3, 'Eduardo', 'Galeano', 'Uruguay ', '1940-09-03', 1),
(11, 'Simone', 'De Beauvoir', 'Francia', '1908-01-09', 1),
(12, 'Mario', 'Vargas Llosa', 'Perú ', '1936-03-28', 1),
(13, 'Gabriel', 'García Márquez ', 'Colombia ', '1927-03-06', 1),
(14, 'Miguel ', 'de Cervantes', 'España', '1547-09-29', 1),
(15, 'William ', 'Shakespeare', 'Inglaterra', '1564-04-23', 1),
(16, 'Mario ', 'Benedetti', 'Uruguay', '1920-09-14', 1),
(17, 'Juana ', 'de Ibarbourou', 'Uruguay ', '1892-03-08', 0),
(18, 'Alfonsina', 'Storni', 'Suiza', '1892-05-29', 1),
(19, 'Delmira', 'Agustini', 'Uruguay ', '1886-10-24', 1),
(20, 'Gabriela ', 'Mistral', 'Chile', '1889-04-07', 1);

-- --------------------------------------------------------
-- 3. TABLA Y DATOS: generos
-- --------------------------------------------------------
DROP TABLE IF EXISTS `generos`;
CREATE TABLE IF NOT EXISTS `generos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `generos` (`id`, `nombre`, `descripcion`, `activo`) VALUES
(3, 'Novela', 'Narrativa extensa en prosa con trama compleja y personajes desarrollados.', 1),
(4, 'Ensayo', 'Descripción de ensayo', 1),
(5, 'Narrativo', 'Descripción de narrativo ', 1),
(6, 'Lírico', 'Descripción de lírico ', 1),
(7, 'Dramático', 'Descripción de drámatico', 1),
(8, 'Didáctico ', 'Descripción de didáctico', 1),
(9, 'Poético', 'Descripción de poético', 1),
(10, 'Terror ', 'Historias diseñadas para generar suspenso, inquietud, miedo o sobrecogimiento.', 1),
(11, 'Novela Histórica', 'Combinación de novela histórica, poesía épica y ensayo literario', 1),
(12, 'Dramático ', 'Representa conflictos graves y solemnes entre personajes heroicos o ilustres cuyo desenlace es funesto o fatal. Busca generar una purificación de las pasiones en el espectador, conocida como catarsis, a través del temor y la compasión.', 1);

-- --------------------------------------------------------
-- 4. TABLA Y DATOS: libros
-- --------------------------------------------------------
DROP TABLE IF EXISTS `libros`;
CREATE TABLE IF NOT EXISTS `libros` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL DEFAULT '2',
  `titulo` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `autor_id` int DEFAULT NULL,
  `genero_id` int DEFAULT NULL,
  `anio` int DEFAULT NULL,
  `paginas` int DEFAULT NULL,
  `estado` enum('pendiente','en_progreso','leido') COLLATE utf8mb4_unicode_ci DEFAULT 'pendiente',
  `portada` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `autor_id` (`autor_id`),
  KEY `genero_id` (`genero_id`),
  KEY `fk_libros_usuarios` (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `libros` (`id`, `usuario_id`, `titulo`, `autor_id`, `genero_id`, `anio`, `paginas`, `estado`, `portada`, `activo`) VALUES
(7, 2, 'Cien años de soledad', 13, 3, 1956, 200, 'pendiente', '/img/portadas/7ca171ee21748ebf90b7bf61e4ea9a20.webp', 1),
(8, 2, 'Las Venas Abiertas de América Latina', 3, 4, 1950, 300, 'pendiente', '/img/portadas/1e9f1fd924286abc3481618730869c74.webp', 1),
(15, 2, 'Chico Carlo', 17, 8, 1944, 121, 'pendiente', '/img/portadas/ca982f5d7e5fcdcaf56e88a168c572bc.jpg', 1),
(16, 2, 'Memorias del Fuego, Los nacimientos', 3, NULL, 1982, 367, 'pendiente', '/img/portadas/5c364e8bea0448d0240a61964b6d8d50.webp', 1),
(17, 2, 'Memoria del fuego, Las caras y las máscaras', 3, 11, 1984, 376, 'leido', '/img/portadas/fc76595b9783926371d390e62d0e9fe7.webp', 1),
(18, 2, 'Hamlet', 15, 12, 1599, 304, 'pendiente', '/img/portadas/26e1aa2a7bd41d631f6f5112cd942742.webp', 1),
(25, 2, 'Memoria del fuego, Las caras y las máscaras', 18, 10, 1627, 312, 'pendiente', '/img/portadas/c7f92ab95486ae183ad0b7de3f79cceb.webp', 1),
(29, 2, 'El Barrio Unido', NULL, 8, 2012, 147, 'pendiente', '/img/portadas/0770cd7b6de69366b989313766485572.webp', 1),
(32, 6, 'Memoria del fuego, Las caras y las máscaras', 3, 11, 1984, 376, 'pendiente', '/img/portadas/8133110ab505b8071f8de144540ff6bd.webp', 1),
(33, 7, 'Las Venas Abiertas de América Latina', 3, 11, 1971, 350, 'pendiente', '/img/portadas/20b9f6a2dd448c8a6c419ddf444bf421.webp', 1),
(34, 7, 'Cien años de soledad', 13, 3, 1967, 471, 'pendiente', '/img/portadas/9b4c474a64a4af269dfcd2563f0f9acb.webp', 1),
(35, 7, 'Hamlet', 15, 7, 1599, 304, 'pendiente', '/img/portadas/faeff8f45918a512a8cc2c3387171690.webp', 1);

-- --------------------------------------------------------
-- 5. RESTRICCIONES Y CLAVES FORÁNEAS
-- --------------------------------------------------------
ALTER TABLE `libros`
  ADD CONSTRAINT `fk_libros_usuarios` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `libros_ibfk_1` FOREIGN KEY (`autor_id`) REFERENCES `autores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `libros_ibfk_2` FOREIGN KEY (`genero_id`) REFERENCES `generos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

COMMIT;

SET FOREIGN_KEY_CHECKS = 1;