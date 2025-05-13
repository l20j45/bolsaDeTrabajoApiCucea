-- --------------------------------------------------------
-- Host:                         192.168.100.60
-- Server version:               10.11.5-MariaDB-log - Alpine Linux
-- Server OS:                    Linux
-- HeidiSQL Version:             12.10.0.7000
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for bolsaDeTrabajoCucea
DROP DATABASE IF EXISTS `bolsaDeTrabajoCucea`;
CREATE DATABASE IF NOT EXISTS `bolsaDeTrabajoCucea` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci */;
USE `bolsaDeTrabajoCucea`;

-- Dumping structure for table bolsaDeTrabajoCucea.certificaciones
DROP TABLE IF EXISTS `certificaciones`;
CREATE TABLE IF NOT EXISTS `certificaciones` (
  `idCertificacion` int(11) NOT NULL AUTO_INCREMENT,
  `NombreCertificacion` varchar(255) DEFAULT NULL,
  `archivo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idCertificacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Dumping data for table bolsaDeTrabajoCucea.certificaciones: ~0 rows (approximately)
DELETE FROM `certificaciones`;

-- Dumping structure for table bolsaDeTrabajoCucea.certificacionesAlumnos
DROP TABLE IF EXISTS `certificacionesAlumnos`;
CREATE TABLE IF NOT EXISTS `certificacionesAlumnos` (
  `estudianteId` int(11) DEFAULT NULL,
  `certificacionId` int(11) DEFAULT NULL,
  KEY `FK_certificacionesAlumnos_estudiante` (`estudianteId`),
  KEY `FK_certificacionesAlumnos_certificaciones` (`certificacionId`),
  CONSTRAINT `FK_certificacionesAlumnos_certificaciones` FOREIGN KEY (`certificacionId`) REFERENCES `certificaciones` (`idCertificacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_certificacionesAlumnos_estudiante` FOREIGN KEY (`estudianteId`) REFERENCES `estudiante` (`idEstudiante`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Dumping data for table bolsaDeTrabajoCucea.certificacionesAlumnos: ~0 rows (approximately)
DELETE FROM `certificacionesAlumnos`;

-- Dumping structure for table bolsaDeTrabajoCucea.estudiante
DROP TABLE IF EXISTS `estudiante`;
CREATE TABLE IF NOT EXISTS `estudiante` (
  `idEstudiante` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(50) NOT NULL DEFAULT '',
  `codigoAlumno` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellidoPaterno` varchar(50) NOT NULL,
  `apellidoMaterno` varchar(50) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `carrera` enum('Administración','Administración Gubernamental y Políticas Públicas','Administración Financiera y Sistemas','Contaduría Pública','Economía','Gestión de Negocios Gastronómicos','Gestión y Economía Ambiental','Mercadotecnia','Mercadotecnia Digital','Ingeniería en Negocios','Negocios Internacionales','Recursos Humanos','Relaciones Públicas y Comunicación','Tecnologías de la Información','Turismo','','no especificado') DEFAULT NULL,
  `estatus` enum('activo','egresado','titulado','failure','') DEFAULT NULL,
  `semestre` varchar(20) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `curriculum` varchar(255) DEFAULT NULL,
  `descripcion` varchar(1000) DEFAULT NULL,
  `puestoDeseado` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`idEstudiante`)
) ENGINE=InnoDB AUTO_INCREMENT=1011 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Dumping data for table bolsaDeTrabajoCucea.estudiante: ~3 rows (approximately)
DELETE FROM `estudiante`;
INSERT INTO `estudiante` (`idEstudiante`, `uuid`, `codigoAlumno`, `password`, `nombre`, `apellidoPaterno`, `apellidoMaterno`, `telefono`, `correo`, `carrera`, `estatus`, `semestre`, `foto`, `curriculum`, `descripcion`, `puestoDeseado`) VALUES
	(1008, '0da2078c-1919-4cb9-925d-e2816251b57a', '3321265', 'Pickner12.', 'Daniel', 'Rojas', 'Artiaga', '3318231058', 'daniel@correo.com', 'Tecnologías de la Información', 'egresado', '', 'uploads/fotos/681a1a5c42b9b8.54191318.jpg', 'uploads/curriculum/681a1a62408267.10045106.pdf', 'descripcion', 'Full stack'),
	(1009, '363bddf2-5ceb-464b-87a5-bae44ea22e4f', 'alumno@udg.com', 'testeo', 'felipez', 'Manriquez', 'godinez', '2312314574', 'alumno@udg.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(1010, 'bd03590a-1ca2-4bb7-8d67-27d3d120b29c', 'Test213@unam.com', 'riotpropa', 'Daniel', 'Materno', 'Rojas', '3318231058', 'Test213@unam.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- Dumping structure for table bolsaDeTrabajoCucea.habilidadesBlandas
DROP TABLE IF EXISTS `habilidadesBlandas`;
CREATE TABLE IF NOT EXISTS `habilidadesBlandas` (
  `idHabilidadesBlandas` int(11) NOT NULL AUTO_INCREMENT,
  `nombreHabilidadesBlandas` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`idHabilidadesBlandas`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Dumping data for table bolsaDeTrabajoCucea.habilidadesBlandas: ~2 rows (approximately)
DELETE FROM `habilidadesBlandas`;
INSERT INTO `habilidadesBlandas` (`idHabilidadesBlandas`, `nombreHabilidadesBlandas`) VALUES
	(1, 'Comunicacion'),
	(2, 'Proactivo'),
	(3, 'revisionista');

-- Dumping structure for table bolsaDeTrabajoCucea.habilidadesBlandasAlumnos
DROP TABLE IF EXISTS `habilidadesBlandasAlumnos`;
CREATE TABLE IF NOT EXISTS `habilidadesBlandasAlumnos` (
  `idEstudiante` int(11) DEFAULT NULL,
  `idHabilidadesBlandas` int(11) DEFAULT NULL,
  KEY `FK_habilidadesBlandasAlumnos_estudiante` (`idEstudiante`),
  KEY `FK_habilidadesBlandasAlumnos_habilidadesBlandas` (`idHabilidadesBlandas`),
  CONSTRAINT `FK_habilidadesBlandasAlumnos_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiante` (`idEstudiante`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_habilidadesBlandasAlumnos_habilidadesBlandas` FOREIGN KEY (`idHabilidadesBlandas`) REFERENCES `habilidadesBlandas` (`idHabilidadesBlandas`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Dumping data for table bolsaDeTrabajoCucea.habilidadesBlandasAlumnos: ~2 rows (approximately)
DELETE FROM `habilidadesBlandasAlumnos`;
INSERT INTO `habilidadesBlandasAlumnos` (`idEstudiante`, `idHabilidadesBlandas`) VALUES
	(1008, 2),
	(1008, 3);

-- Dumping structure for table bolsaDeTrabajoCucea.habilidadesDuras
DROP TABLE IF EXISTS `habilidadesDuras`;
CREATE TABLE IF NOT EXISTS `habilidadesDuras` (
  `idHabilidadesDuras` int(11) NOT NULL AUTO_INCREMENT,
  `nombreHabilidadesDuras` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`idHabilidadesDuras`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Dumping data for table bolsaDeTrabajoCucea.habilidadesDuras: ~4 rows (approximately)
DELETE FROM `habilidadesDuras`;
INSERT INTO `habilidadesDuras` (`idHabilidadesDuras`, `nombreHabilidadesDuras`) VALUES
	(1, 'Excel Avanzado'),
	(2, 'Python'),
	(3, 'Auditor de empresa de alimentos'),
	(4, 'testing');

-- Dumping structure for table bolsaDeTrabajoCucea.habilidadesDurasAlumnos
DROP TABLE IF EXISTS `habilidadesDurasAlumnos`;
CREATE TABLE IF NOT EXISTS `habilidadesDurasAlumnos` (
  `idEstudiante` int(11) DEFAULT NULL,
  `idHabilidadesDuras` int(11) DEFAULT NULL,
  KEY `FK_habilidadesDurasAlumnos_estudiante` (`idEstudiante`),
  KEY `FK_habilidadesDurasAlumnos_habilidadesDuras` (`idHabilidadesDuras`),
  CONSTRAINT `FK_habilidadesDurasAlumnos_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiante` (`idEstudiante`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_habilidadesDurasAlumnos_habilidadesDuras` FOREIGN KEY (`idHabilidadesDuras`) REFERENCES `habilidadesDuras` (`idHabilidadesDuras`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Dumping data for table bolsaDeTrabajoCucea.habilidadesDurasAlumnos: ~2 rows (approximately)
DELETE FROM `habilidadesDurasAlumnos`;
INSERT INTO `habilidadesDurasAlumnos` (`idEstudiante`, `idHabilidadesDuras`) VALUES
	(1008, 2),
	(1008, 3),
	(1008, 4);

-- Dumping structure for table bolsaDeTrabajoCucea.idiomasAdicionales
DROP TABLE IF EXISTS `idiomasAdicionales`;
CREATE TABLE IF NOT EXISTS `idiomasAdicionales` (
  `idIdiomasAdicionales` int(11) NOT NULL AUTO_INCREMENT,
  `nombreIdiomasAdicionales` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idIdiomasAdicionales`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Dumping data for table bolsaDeTrabajoCucea.idiomasAdicionales: ~7 rows (approximately)
DELETE FROM `idiomasAdicionales`;
INSERT INTO `idiomasAdicionales` (`idIdiomasAdicionales`, `nombreIdiomasAdicionales`) VALUES
	(1, 'Ingles'),
	(2, 'Aleman'),
	(3, 'Japones'),
	(4, 'klingon'),
	(6, 'Chino'),
	(8, 'Patois'),
	(9, 'Portugues'),
	(10, 'Ruso');

-- Dumping structure for table bolsaDeTrabajoCucea.idiomasAdicionalesAlumnos
DROP TABLE IF EXISTS `idiomasAdicionalesAlumnos`;
CREATE TABLE IF NOT EXISTS `idiomasAdicionalesAlumnos` (
  `idEstudiante` int(11) DEFAULT NULL,
  `ididiomasAdicionales` int(11) DEFAULT NULL,
  KEY `FK_idiomasAdicionalesAlumnos_estudiante` (`idEstudiante`),
  KEY `FK_idiomasAdicionalesAlumnos_idiomasAdicionales` (`ididiomasAdicionales`),
  CONSTRAINT `FK_idiomasAdicionalesAlumnos_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiante` (`idEstudiante`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_idiomasAdicionalesAlumnos_idiomasAdicionales` FOREIGN KEY (`ididiomasAdicionales`) REFERENCES `idiomasAdicionales` (`idIdiomasAdicionales`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Dumping data for table bolsaDeTrabajoCucea.idiomasAdicionalesAlumnos: ~3 rows (approximately)
DELETE FROM `idiomasAdicionalesAlumnos`;
INSERT INTO `idiomasAdicionalesAlumnos` (`idEstudiante`, `ididiomasAdicionales`) VALUES
	(1008, 1),
	(1008, 3),
	(1008, 4);

-- Dumping structure for table bolsaDeTrabajoCucea.trabajadores
DROP TABLE IF EXISTS `trabajadores`;
CREATE TABLE IF NOT EXISTS `trabajadores` (
  `idTrabajador` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) DEFAULT NULL,
  `codigoPersonal` varchar(20) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `apellidoPaterno` varchar(100) DEFAULT NULL,
  `apellidoMaterno` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idTrabajador`),
  UNIQUE KEY `trabajadores_pk` (`idTrabajador`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Dumping data for table bolsaDeTrabajoCucea.trabajadores: ~0 rows (approximately)
DELETE FROM `trabajadores`;
INSERT INTO `trabajadores` (`idTrabajador`, `uuid`, `codigoPersonal`, `password`, `nombre`, `apellidoPaterno`, `apellidoMaterno`, `telefono`, `correo`) VALUES
	(1, '2b2c7e21-671b-49cc-b951-88fbcf25e47b', '13257854', 'testeo', 'Yazmin', 'Godinez', 'Manriquez', '2332265', 'yazmin@test.com');

-- Dumping structure for table bolsaDeTrabajoCucea.trabajoDeseado
DROP TABLE IF EXISTS `trabajoDeseado`;
CREATE TABLE IF NOT EXISTS `trabajoDeseado` (
  `idtrabajoDeseado` int(11) NOT NULL AUTO_INCREMENT,
  `NombretrabajoDeseado` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idtrabajoDeseado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Dumping data for table bolsaDeTrabajoCucea.trabajoDeseado: ~0 rows (approximately)
DELETE FROM `trabajoDeseado`;

-- Dumping structure for table bolsaDeTrabajoCucea.trabajoDeseadoAlumnos
DROP TABLE IF EXISTS `trabajoDeseadoAlumnos`;
CREATE TABLE IF NOT EXISTS `trabajoDeseadoAlumnos` (
  `estudianteId` int(11) DEFAULT NULL,
  `trabajoDeseadoId` int(11) DEFAULT NULL,
  KEY `FK_trabajoDeseadoAlumnos_estudiante` (`estudianteId`),
  KEY `FK_trabajoDeseadoAlumnos_trabajoDeseado` (`trabajoDeseadoId`),
  CONSTRAINT `FK_trabajoDeseadoAlumnos_estudiante` FOREIGN KEY (`estudianteId`) REFERENCES `estudiante` (`idEstudiante`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_trabajoDeseadoAlumnos_trabajoDeseado` FOREIGN KEY (`trabajoDeseadoId`) REFERENCES `trabajoDeseado` (`idtrabajoDeseado`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Dumping data for table bolsaDeTrabajoCucea.trabajoDeseadoAlumnos: ~0 rows (approximately)
DELETE FROM `trabajoDeseadoAlumnos`;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
