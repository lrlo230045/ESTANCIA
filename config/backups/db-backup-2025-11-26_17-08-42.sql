-- ==========================================
--  RESPALDO COMPLETO DE BASE DE DATOS: gestion_materiales
--  Fecha: 2025-11-26 17:08:42
-- ==========================================

CREATE DATABASE IF NOT EXISTS `gestion_materiales`;
USE `gestion_materiales`;


-- ESTRUCTURA DE TABLA `administradores`
DROP TABLE IF EXISTS `administradores`;
CREATE TABLE `administradores` (
  `id_admin` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  PRIMARY KEY (`id_admin`),
  KEY `fk_admin_usuario` (`id_usuario`),
  CONSTRAINT `fk_admin_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- DATOS DE LA TABLA `administradores`
INSERT INTO `administradores` VALUES("1","2");


-- ESTRUCTURA DE TABLA `alumnos`
DROP TABLE IF EXISTS `alumnos`;
CREATE TABLE `alumnos` (
  `id_alumno` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `id_carrera` int(11) NOT NULL,
  PRIMARY KEY (`id_alumno`),
  KEY `fk_alumno_usuario` (`id_usuario`),
  KEY `fk_alumno_carrera` (`id_carrera`),
  CONSTRAINT `fk_alumno_carrera` FOREIGN KEY (`id_carrera`) REFERENCES `carreras` (`id_carrera`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_alumno_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- DATOS DE LA TABLA `alumnos`
INSERT INTO `alumnos` VALUES("3","10","1");
INSERT INTO `alumnos` VALUES("5","11","1");


-- ESTRUCTURA DE TABLA `carreras`
DROP TABLE IF EXISTS `carreras`;
CREATE TABLE `carreras` (
  `id_carrera` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_carrera` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  PRIMARY KEY (`id_carrera`),
  UNIQUE KEY `nombre_carrera` (`nombre_carrera`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- DATOS DE LA TABLA `carreras`
INSERT INTO `carreras` VALUES("1","Ingeniería en Sistemas Computacionales","Formación en desarrollo de software, redes y bases de datos.");
INSERT INTO `carreras` VALUES("2","Ingeniería Industrial","Optimización de procesos productivos y gestión empresarial.");
INSERT INTO `carreras` VALUES("3","Arquitectura","Diseño y construcción de espacios arquitectónicos.");


-- ESTRUCTURA DE TABLA `coordinadores`
DROP TABLE IF EXISTS `coordinadores`;
CREATE TABLE `coordinadores` (
  `id_coordinadores` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `id_carrera` int(11) NOT NULL,
  PRIMARY KEY (`id_coordinadores`),
  KEY `fk_coord_usuario` (`id_usuario`),
  KEY `fk_coord_carrera` (`id_carrera`),
  CONSTRAINT `fk_coord_carrera` FOREIGN KEY (`id_carrera`) REFERENCES `carreras` (`id_carrera`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_coord_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- DATOS DE LA TABLA `coordinadores`
INSERT INTO `coordinadores` VALUES("5","9","1");


-- ESTRUCTURA DE TABLA `materiales`
DROP TABLE IF EXISTS `materiales`;
CREATE TABLE `materiales` (
  `id_material` int(11) NOT NULL AUTO_INCREMENT,
  `id_ubicacion` int(11) NOT NULL,
  `nombre_material` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `cantidad_disponible` int(11) NOT NULL DEFAULT 0,
  `unidad_medida` varchar(20) DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  PRIMARY KEY (`id_material`),
  KEY `fk_material_ubicacion` (`id_ubicacion`),
  CONSTRAINT `fk_material_ubicacion` FOREIGN KEY (`id_ubicacion`) REFERENCES `ubicaciones` (`id_ubicacion`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- DATOS DE LA TABLA `materiales`
INSERT INTO `materiales` VALUES("1","3","Multímetro Digital","Herramienta para medir voltaje, corriente y resistencia.","250","piezas","activo");
INSERT INTO `materiales` VALUES("2","2","Resistencia 220Ω","Resistencias de 1/4W para prácticas de electrónica.","500","piezas","activo");
INSERT INTO `materiales` VALUES("3","1","Cemento Portland","Bolsa de 50 kg para prácticas de construcción.","46","bolsas","activo");


-- ESTRUCTURA DE TABLA `solicitudes`
DROP TABLE IF EXISTS `solicitudes`;
CREATE TABLE `solicitudes` (
  `id_solicitud` int(11) NOT NULL AUTO_INCREMENT,
  `id_coordinadores` int(11) DEFAULT NULL,
  `id_alumno` int(11) DEFAULT NULL,
  `id_material` int(11) NOT NULL,
  `cantidad_solicitada` int(11) NOT NULL,
  `estado` enum('pendiente','aprobada','rechazada','entregada','cancelada') DEFAULT 'pendiente',
  `fecha_solicitud` datetime DEFAULT current_timestamp(),
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`id_solicitud`),
  KEY `fk_solicitud_coord` (`id_coordinadores`),
  KEY `fk_solicitud_alumno` (`id_alumno`),
  KEY `fk_solicitud_material` (`id_material`),
  CONSTRAINT `fk_solicitud_alumno` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_solicitud_coord` FOREIGN KEY (`id_coordinadores`) REFERENCES `coordinadores` (`id_coordinadores`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_solicitud_material` FOREIGN KEY (`id_material`) REFERENCES `materiales` (`id_material`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- DATOS DE LA TABLA `solicitudes`
INSERT INTO `solicitudes` VALUES("14",NULL,"3","1","4","cancelada","2025-11-23 19:12:28","45");
INSERT INTO `solicitudes` VALUES("16",NULL,"3","2","7","entregada","2025-11-25 01:48:11","l2");


-- ESTRUCTURA DE TABLA `ubicaciones`
DROP TABLE IF EXISTS `ubicaciones`;
CREATE TABLE `ubicaciones` (
  `id_ubicacion` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_ubicacion` varchar(100) NOT NULL,
  `ubicacion_fisica` varchar(150) NOT NULL,
  `capacidad` float DEFAULT 0,
  `descripcion` text DEFAULT NULL,
  PRIMARY KEY (`id_ubicacion`),
  UNIQUE KEY `nombre_ubicacion` (`nombre_ubicacion`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- DATOS DE LA TABLA `ubicaciones`
INSERT INTO `ubicaciones` VALUES("1","Bodega Principal","Edificio A - Planta Baja","500","Área central de almacenamiento general de materiales.");
INSERT INTO `ubicaciones` VALUES("2","Taller de Electrónica","Edificio B - Segundo Piso","120","Prácticas de electrónica y mantenimiento.");
INSERT INTO `ubicaciones` VALUES("3","Laboratorio de Materiales","Edificio C - Primer Piso","80","Laboratorio de construcción y análisis de materiales.");


-- ESTRUCTURA DE TABLA `usuarios`
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `matricula` varchar(20) NOT NULL,
  `nombre` varchar(60) NOT NULL,
  `apellido_pa` varchar(60) NOT NULL,
  `apellido_ma` varchar(60) DEFAULT NULL,
  `correo` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `contrasena` varchar(255) NOT NULL,
  `tipo_usuario` enum('coordinador','administrador','alumno') NOT NULL,
  `estatus` enum('activo','inactivo') DEFAULT 'activo',
  `genero` enum('masculino','femenino','otro') NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `matricula` (`matricula`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- DATOS DE LA TABLA `usuarios`
INSERT INTO `usuarios` VALUES("2","u2025001","jose","Labra","Robles","h2@gmail.com","7772509138","$2y$10$ifCchARRNaG3MMDmZpneCej3XHTjbDg3W0PDhEjtOuxPi32tK..Ea","administrador","activo","masculino","2025-11-07 13:10:08");
INSERT INTO `usuarios` VALUES("9","u2025003","luis","lan","ramirez","h5@gmail.com","777 250 9137","$2y$10$.EO.yP6mmMzcdwqvH7SAou.d2LyrlHY9FN/UwyNFJcBlYCI5bHede","coordinador","activo","masculino","2025-11-23 19:09:02");
INSERT INTO `usuarios` VALUES("10","u2025002","luis","Lang","ramirez","hongo5@hotmail.com","777 250 9138","$2y$10$t0G.G/m/105ZEKjvkmXGG.sx9MfPFC9r1e8sK173x7MTtCW2V5TJm","alumno","activo","femenino","2025-11-23 19:12:13");
INSERT INTO `usuarios` VALUES("11","u2025004","Luispruebas","Lang","Ramírez","hongo@hotmail.com","777 250 9138","$2y$10$cjYrtZ3iHGIt6L9jff6N4OkCwMpaBq6t0mwBEtwMiq7WMjsbOYzLK","alumno","activo","otro","2025-11-24 00:36:50");


-- PROCEDIMIENTOS

DROP PROCEDURE IF EXISTS `sp_solicitudes_genero`;
CREATE PROCEDURE `sp_solicitudes_genero`() BEGIN
    WITH datos AS (
        SELECT u.genero AS etiqueta,
               COUNT(*) AS total
        FROM solicitudes s
        LEFT JOIN alumnos a ON s.id_alumno = a.id_alumno
        LEFT JOIN coordinadores co ON s.id_coordinadores = co.id_coordinadores
        LEFT JOIN usuarios u ON u.id_usuario = COALESCE(a.id_usuario, co.id_usuario)
        WHERE u.genero IS NOT NULL
          AND (p_days = 0 OR s.fecha_solicitud >= NOW() - INTERVAL p_days DAY)
        GROUP BY u.genero
    ),
    total_sum AS (
        SELECT SUM(total) AS total_general FROM datos
    )
    SELECT etiqueta,
           ROUND((total / total_general) * 100, 2) AS porcentaje
    FROM datos, total_sum;
END;

DROP PROCEDURE IF EXISTS `sp_top_carreras`;
CREATE PROCEDURE `sp_top_carreras`() BEGIN
    WITH datos AS (
        SELECT c.nombre_carrera AS etiqueta,
               COUNT(*) AS total
        FROM solicitudes s
        LEFT JOIN alumnos a ON s.id_alumno = a.id_alumno
        LEFT JOIN coordinadores co ON s.id_coordinadores = co.id_coordinadores
        LEFT JOIN carreras c ON c.id_carrera = COALESCE(a.id_carrera, co.id_carrera)
        WHERE c.id_carrera IS NOT NULL
          AND (p_days = 0 OR s.fecha_solicitud >= NOW() - INTERVAL p_days DAY)
        GROUP BY c.id_carrera
        ORDER BY total DESC
        LIMIT 10
    ),
    total_sum AS (
        SELECT SUM(total) AS total_general FROM datos
    )
    SELECT etiqueta,
           ROUND((total / total_general) * 100, 2) AS porcentaje
    FROM datos, total_sum;
END;

DROP PROCEDURE IF EXISTS `sp_top_materiales`;
CREATE PROCEDURE `sp_top_materiales`() BEGIN
    WITH datos AS (
        SELECT m.nombre_material AS etiqueta,
               COUNT(*) AS total
        FROM solicitudes s
        INNER JOIN materiales m ON m.id_material = s.id_material
        WHERE (s.id_alumno IS NOT NULL OR s.id_coordinadores IS NOT NULL)
          AND (p_days = 0 OR s.fecha_solicitud >= NOW() - INTERVAL p_days DAY)
        GROUP BY m.id_material
        ORDER BY total DESC
        LIMIT 10
    ),
    total_sum AS (
        SELECT SUM(total) AS total_general FROM datos
    )
    SELECT etiqueta,
           ROUND((total / total_general) * 100, 2) AS porcentaje
    FROM datos, total_sum;
END;

