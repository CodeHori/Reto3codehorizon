-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-05-2026 a las 08:47:09
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
-- Base de datos: `ausencias_cpifp`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ausencia`
--

CREATE TABLE `ausencia` (
  `id_a` int(100) NOT NULL,
  `dni_usuario` char(9) NOT NULL,
  `id_horario` int(100) NOT NULL,
  `hora` int(100) NOT NULL,
  `dia_a` date NOT NULL,
  `justificante` varchar(255) NOT NULL,
  `brorrarla` int(11) NOT NULL,
  `tipo_ausencia` varchar(100) DEFAULT NULL,
  `tarea_file` varchar(255) DEFAULT NULL,
  `tarea_text` text DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horario`
--

CREATE TABLE `horario` (
  `id_horario` int(100) NOT NULL,
  `dni_usuario` char(9) NOT NULL,
  `aula` varchar(100) NOT NULL,
  `modulo` varchar(100) NOT NULL,
  `id_hora` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horas`
--

CREATE TABLE `horas` (
  `id_hora` varchar(2) NOT NULL,
  `hora` int(10) NOT NULL,
  `dia` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `horas`
--

INSERT INTO `horas` (`id_hora`, `hora`, `dia`) VALUES
('J1', 1, 'Jueves'),
('J2', 2, 'Jueves'),
('J3', 3, 'Jueves'),
('J4', 4, 'Jueves'),
('J5', 5, 'Jueves'),
('J6', 6, 'Jueves'),
('L1', 1, 'Lunes'),
('L2', 2, 'Lunes'),
('L3', 3, 'Lunes'),
('L4', 4, 'Lunes'),
('L5', 5, 'Lunes'),
('L6', 6, 'Lunes'),
('M1', 1, 'Martes'),
('M2', 2, 'Martes'),
('M3', 3, 'Martes'),
('M4', 4, 'Martes'),
('M5', 5, 'Martes'),
('M6', 6, 'Martes'),
('V1', 1, 'Viernes'),
('V2', 2, 'Viernes'),
('V3', 3, 'Viernes'),
('V4', 4, 'Viernes'),
('V5', 5, 'Viernes'),
('V6', 6, 'Viernes'),
('X1', 1, 'Miércoles'),
('X2', 2, 'Miércoles'),
('X3', 3, 'Miércoles'),
('X4', 4, 'Miércoles'),
('X5', 5, 'Miércoles'),
('X6', 6, 'Miércoles');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `dni` char(9) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `correo_electronico` varchar(100) NOT NULL,
  `contraseña` varchar(100) NOT NULL,
  `familia` varchar(120) NOT NULL,
  `rol` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`dni`, `nombre`, `apellido`, `correo_electronico`, `contraseña`, `familia`, `rol`) VALUES
('11223344D', 'Alba', 'Parrilla Bell', 'albaparrillabel@gmail.com', '$2y$10$jtszTBUxZfmfD2mXz/6C0uoC.z7PSjsiCd5Lb8a8eNL27tF59nJES', 'Informatica', 'admin'),
('25677851A', 'Norbert', 'Kusmodi', 'xmikebiro@gmail.com', '$2y$10$sCau744guIbucnpVOs2Blur5Gokz3gWXoshlG1HykGFtpM6o1AR2S', 'Informatica', 'admin'),
('26283416M', 'Arnau', 'Trillo', 'trilloarnau@gmail.com', '$2y$10$e7dxXP62oFmzVpbxJAw03u8fqDa6j71tn4/BDx02TMr5mOk.HwuL2', 'informatica', 'admin'),
('44332211F', 'Laura', 'Dolz', 'lauradolzm@gmail.com', '$2y$10$6JOwPkc4e72oiNn3mm4F/.1ouKnrKoYWcYUxcMECgtcC0LrJeJ8zS', 'Informatica', 'admin');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `ausencia`
--
ALTER TABLE `ausencia`
  ADD PRIMARY KEY (`id_a`),
  ADD KEY `dni_usuario` (`dni_usuario`),
  ADD KEY `id_h` (`id_horario`);

--
-- Indices de la tabla `horario`
--
ALTER TABLE `horario`
  ADD PRIMARY KEY (`id_horario`),
  ADD KEY `dni_horario` (`dni_usuario`),
  ADD KEY `id_hora_` (`id_hora`);

--
-- Indices de la tabla `horas`
--
ALTER TABLE `horas`
  ADD PRIMARY KEY (`id_hora`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`dni`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `ausencia`
--
ALTER TABLE `ausencia`
  MODIFY `id_a` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `horario`
--
ALTER TABLE `horario`
  MODIFY `id_horario` int(100) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `ausencia`
--
ALTER TABLE `ausencia`
  ADD CONSTRAINT `ausencia_ibfk_1` FOREIGN KEY (`dni_usuario`) REFERENCES `usuarios` (`dni`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ausencia_ibfk_2` FOREIGN KEY (`id_horario`) REFERENCES `horario` (`id_horario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `horario`
--
ALTER TABLE `horario`
  ADD CONSTRAINT `dni_horario` FOREIGN KEY (`dni_usuario`) REFERENCES `usuarios` (`dni`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `horario_ibfk_1` FOREIGN KEY (`id_hora`) REFERENCES `horas` (`id_hora`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
