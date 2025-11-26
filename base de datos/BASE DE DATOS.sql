-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-11-2025 a las 16:44:06
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `gestion_materiales`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_solicitudes_genero` (IN `p_days` INT)   BEGIN
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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_top_carreras` (IN `p_days` INT)   BEGIN
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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_top_materiales` (IN `p_days` INT)   BEGIN
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
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administradores`
--

CREATE TABLE `administradores` (
  `id_admin` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administradores`
--

INSERT INTO `administradores` (`id_admin`, `id_usuario`) VALUES
(1, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos`
--

CREATE TABLE `alumnos` (
  `id_alumno` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_carrera` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumnos`
--

INSERT INTO `alumnos` (`id_alumno`, `id_usuario`, `id_carrera`) VALUES
(3, 10, 1),
(5, 11, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carreras`
--

CREATE TABLE `carreras` (
  `id_carrera` int(11) NOT NULL,
  `nombre_carrera` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carreras`
--

INSERT INTO `carreras` (`id_carrera`, `nombre_carrera`, `descripcion`) VALUES
(1, 'Ingeniería en Sistemas Computacionales', 'Formación en desarrollo de software, redes y bases de datos.'),
(2, 'Ingeniería Industrial', 'Optimización de procesos productivos y gestión empresarial.'),
(3, 'Arquitectura', 'Diseño y construcción de espacios arquitectónicos.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `coordinadores`
--

CREATE TABLE `coordinadores` (
  `id_coordinadores` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_carrera` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `coordinadores`
--

INSERT INTO `coordinadores` (`id_coordinadores`, `id_usuario`, `id_carrera`) VALUES
(5, 9, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materiales`
--

CREATE TABLE `materiales` (
  `id_material` int(11) NOT NULL,
  `id_ubicacion` int(11) NOT NULL,
  `nombre_material` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `cantidad_disponible` int(11) NOT NULL DEFAULT 0,
  `unidad_medida` varchar(20) DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materiales`
--

INSERT INTO `materiales` (`id_material`, `id_ubicacion`, `nombre_material`, `descripcion`, `cantidad_disponible`, `unidad_medida`, `estado`) VALUES
(1, 3, 'Multímetro Digital', 'Herramienta para medir voltaje, corriente y resistencia.', 250, 'piezas', 'activo'),
(2, 2, 'Resistencia 220Ω', 'Resistencias de 1/4W para prácticas de electrónica.', 500, 'piezas', 'activo'),
(3, 1, 'Cemento Portland', 'Bolsa de 50 kg para prácticas de construcción.', 46, 'bolsas', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes`
--

CREATE TABLE `solicitudes` (
  `id_solicitud` int(11) NOT NULL,
  `id_coordinadores` int(11) DEFAULT NULL,
  `id_alumno` int(11) DEFAULT NULL,
  `id_material` int(11) NOT NULL,
  `cantidad_solicitada` int(11) NOT NULL,
  `estado` enum('pendiente','aprobada','rechazada','entregada','cancelada') DEFAULT 'pendiente',
  `fecha_solicitud` datetime DEFAULT current_timestamp(),
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitudes`
--

INSERT INTO `solicitudes` (`id_solicitud`, `id_coordinadores`, `id_alumno`, `id_material`, `cantidad_solicitada`, `estado`, `fecha_solicitud`, `observaciones`) VALUES
(14, NULL, 3, 1, 4, 'cancelada', '2025-11-23 19:12:28', '45'),
(15, 5, NULL, 2, 45, 'pendiente', '2025-11-23 19:13:46', 'hola'),
(16, NULL, 3, 2, 7, 'entregada', '2025-11-25 01:48:11', 'l2');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubicaciones`
--

CREATE TABLE `ubicaciones` (
  `id_ubicacion` int(11) NOT NULL,
  `nombre_ubicacion` varchar(100) NOT NULL,
  `ubicacion_fisica` varchar(150) NOT NULL,
  `capacidad` float DEFAULT 0,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ubicaciones`
--

INSERT INTO `ubicaciones` (`id_ubicacion`, `nombre_ubicacion`, `ubicacion_fisica`, `capacidad`, `descripcion`) VALUES
(1, 'Bodega Principal', 'Edificio A - Planta Baja', 500, 'Área central de almacenamiento general de materiales.'),
(2, 'Taller de Electrónica', 'Edificio B - Segundo Piso', 120, 'Prácticas de electrónica y mantenimiento.'),
(3, 'Laboratorio de Materiales', 'Edificio C - Primer Piso', 80, 'Laboratorio de construcción y análisis de materiales.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
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
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `matricula`, `nombre`, `apellido_pa`, `apellido_ma`, `correo`, `telefono`, `contrasena`, `tipo_usuario`, `estatus`, `genero`, `fecha_registro`) VALUES
(2, 'u2025001', 'jose', 'Labra', 'Robles', 'h2@gmail.com', '7772509138', '$2y$10$ifCchARRNaG3MMDmZpneCej3XHTjbDg3W0PDhEjtOuxPi32tK..Ea', 'administrador', 'activo', 'masculino', '2025-11-07 13:10:08'),
(9, 'u2025003', 'luis', 'lan', 'ramirez', 'h5@gmail.com', '777 250 9137', '$2y$10$.EO.yP6mmMzcdwqvH7SAou.d2LyrlHY9FN/UwyNFJcBlYCI5bHede', 'coordinador', 'activo', 'masculino', '2025-11-23 19:09:02'),
(10, 'u2025002', 'luis', 'Lang', 'ramirez', 'hongo5@hotmail.com', '777 250 9138', '$2y$10$t0G.G/m/105ZEKjvkmXGG.sx9MfPFC9r1e8sK173x7MTtCW2V5TJm', 'alumno', 'activo', 'femenino', '2025-11-23 19:12:13'),
(11, 'u2025004', 'Luispruebas', 'Lang', 'Ramírez', 'hongo@hotmail.com', '777 250 9138', '$2y$10$cjYrtZ3iHGIt6L9jff6N4OkCwMpaBq6t0mwBEtwMiq7WMjsbOYzLK', 'alumno', 'activo', 'otro', '2025-11-24 00:36:50');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id_admin`),
  ADD KEY `fk_admin_usuario` (`id_usuario`);

--
-- Indices de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD PRIMARY KEY (`id_alumno`),
  ADD KEY `fk_alumno_usuario` (`id_usuario`),
  ADD KEY `fk_alumno_carrera` (`id_carrera`);

--
-- Indices de la tabla `carreras`
--
ALTER TABLE `carreras`
  ADD PRIMARY KEY (`id_carrera`),
  ADD UNIQUE KEY `nombre_carrera` (`nombre_carrera`);

--
-- Indices de la tabla `coordinadores`
--
ALTER TABLE `coordinadores`
  ADD PRIMARY KEY (`id_coordinadores`),
  ADD KEY `fk_coord_usuario` (`id_usuario`),
  ADD KEY `fk_coord_carrera` (`id_carrera`);

--
-- Indices de la tabla `materiales`
--
ALTER TABLE `materiales`
  ADD PRIMARY KEY (`id_material`),
  ADD KEY `fk_material_ubicacion` (`id_ubicacion`);

--
-- Indices de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD PRIMARY KEY (`id_solicitud`),
  ADD KEY `fk_solicitud_coord` (`id_coordinadores`),
  ADD KEY `fk_solicitud_alumno` (`id_alumno`),
  ADD KEY `fk_solicitud_material` (`id_material`);

--
-- Indices de la tabla `ubicaciones`
--
ALTER TABLE `ubicaciones`
  ADD PRIMARY KEY (`id_ubicacion`),
  ADD UNIQUE KEY `nombre_ubicacion` (`nombre_ubicacion`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `matricula` (`matricula`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administradores`
--
ALTER TABLE `administradores`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  MODIFY `id_alumno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `carreras`
--
ALTER TABLE `carreras`
  MODIFY `id_carrera` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `coordinadores`
--
ALTER TABLE `coordinadores`
  MODIFY `id_coordinadores` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `materiales`
--
ALTER TABLE `materiales`
  MODIFY `id_material` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  MODIFY `id_solicitud` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `ubicaciones`
--
ALTER TABLE `ubicaciones`
  MODIFY `id_ubicacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `administradores`
--
ALTER TABLE `administradores`
  ADD CONSTRAINT `fk_admin_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD CONSTRAINT `fk_alumno_carrera` FOREIGN KEY (`id_carrera`) REFERENCES `carreras` (`id_carrera`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_alumno_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `coordinadores`
--
ALTER TABLE `coordinadores`
  ADD CONSTRAINT `fk_coord_carrera` FOREIGN KEY (`id_carrera`) REFERENCES `carreras` (`id_carrera`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_coord_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `materiales`
--
ALTER TABLE `materiales`
  ADD CONSTRAINT `fk_material_ubicacion` FOREIGN KEY (`id_ubicacion`) REFERENCES `ubicaciones` (`id_ubicacion`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD CONSTRAINT `fk_solicitud_alumno` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_solicitud_coord` FOREIGN KEY (`id_coordinadores`) REFERENCES `coordinadores` (`id_coordinadores`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_solicitud_material` FOREIGN KEY (`id_material`) REFERENCES `materiales` (`id_material`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
