-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-07-2021 a las 02:13:06
-- Versión del servidor: 10.4.18-MariaDB
-- Versión de PHP: 8.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `comanda`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encuestas`
--

CREATE TABLE `encuestas` (
  `idEncuestas` int(11) NOT NULL,
  `dni` varchar(8) COLLATE utf8_spanish2_ci NOT NULL,
  `nombreCliente` varchar(20) COLLATE utf8_spanish2_ci NOT NULL,
  `idMesa` int(11) NOT NULL,
  `idMozo` int(11) NOT NULL,
  `idCocinero` int(11) NOT NULL,
  `descripcion` varchar(66) COLLATE utf8_spanish2_ci NOT NULL,
  `valMesa` int(11) NOT NULL,
  `valMozo` int(11) NOT NULL,
  `valCocinero` int(11) NOT NULL,
  `valRestaurante` int(11) NOT NULL,
  `fechaCreacion` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturacion`
--

CREATE TABLE `facturacion` (
  `idFacturacion` int(11) NOT NULL,
  `idMesa` varchar(45) COLLATE utf8_spanish2_ci NOT NULL,
  `fechaCreacion` date NOT NULL,
  `importe` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs`
--

CREATE TABLE `logs` (
  `idLogs` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `entidad` varchar(45) COLLATE utf8_spanish2_ci NOT NULL,
  `accion` varchar(45) COLLATE utf8_spanish2_ci NOT NULL,
  `detalle` varchar(128) COLLATE utf8_spanish2_ci NOT NULL,
  `fechaCreacion` date NOT NULL,
  `horaRegistro` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas`
--

CREATE TABLE `mesas` (
  `idMesas` int(11) NOT NULL,
  `codigo` varchar(10) COLLATE utf8_spanish2_ci NOT NULL,
  `estado` varchar(45) COLLATE utf8_spanish2_ci NOT NULL,
  `fechaCreacion` date NOT NULL,
  `fechaModificacion` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `mesas`
--

INSERT INTO `mesas` (`idMesas`, `codigo`, `estado`, `fechaCreacion`, `fechaModificacion`) VALUES
(1, 'M1JFW', 'Cerrada', '2021-07-09', '2021-07-13'),
(2, 'M2BEN', 'Cerrada', '2021-07-09', '2021-07-09'),
(3, 'M3LUK', 'Cerrada', '2021-07-09', '2021-07-09'),
(4, 'M4SZW', 'Cerrada', '2021-07-09', '2021-07-09'),
(5, 'M5YDY', 'Cerrada', '2021-07-09', '2021-07-14'),
(6, 'M6TXT', 'Cerrada', '2021-07-09', '2021-07-14'),
(10, 'M10HW', 'Cerrada', '2021-07-09', '2021-07-15'),
(11, 'M11ER', 'Cerrada', '2021-07-09', '2021-07-09'),
(12, 'M12MI', 'Cerrada', '2021-07-09', '2021-07-09'),
(13, 'M13DV', 'Cerrada', '2021-07-09', '2021-07-09'),
(14, 'M14XO', 'Cerrada', '2021-07-09', '2021-07-09'),
(15, 'M15OM', 'Cerrada', '2021-07-09', '2021-07-11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `idPedidos` int(11) NOT NULL,
  `idSolicitante` int(11) NOT NULL,
  `idMesa` int(11) NOT NULL,
  `idProducto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `idEncargado` int(11) DEFAULT NULL,
  `horaInicio` time NOT NULL,
  `horaEstFin` time DEFAULT NULL,
  `horaFin` time DEFAULT NULL,
  `estado` varchar(20) COLLATE utf8_spanish2_ci NOT NULL,
  `fechaCreacion` date NOT NULL,
  `fechaModificacion` date NOT NULL,
  `fechaBaja` date NOT NULL,
  `foto` varchar(45) COLLATE utf8_spanish2_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `idProductos` int(11) NOT NULL,
  `tipo` varchar(20) COLLATE utf8_spanish2_ci NOT NULL,
  `nombre` varchar(45) COLLATE utf8_spanish2_ci NOT NULL,
  `sector` varchar(45) COLLATE utf8_spanish2_ci NOT NULL,
  `fechaCreacion` date NOT NULL,
  `fechaModificacion` date NOT NULL,
  `precio` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`idProductos`, `tipo`, `nombre`, `sector`, `fechaCreacion`, `fechaModificacion`, `precio`) VALUES
(1, 'Bebidas', 'Coca Cola', 'Tragos', '2021-06-09', '2021-06-10', 120),
(2, 'Bebidas', 'Pepsi', 'Tragos', '2021-06-10', '2021-06-10', 115),
(3, 'Comida', 'Guiso de Fideos', 'Cocina', '2021-06-11', '2021-07-13', 500),
(4, 'Bebidas', 'Quilmes', 'Choperas', '2021-07-08', '2021-07-08', 120),
(5, 'Bebidas', 'Stella Artois', 'Choperas', '2021-07-08', '2021-07-08', 120),
(6, 'Comida', 'Pizza', 'Cocina', '2021-07-08', '2021-07-08', 340),
(7, 'Comida', 'Asado', 'Cocina', '2021-07-08', '2021-07-08', 340),
(8, 'Postre', 'Flan', 'Candy Bar', '2021-07-08', '2021-07-08', 90),
(9, 'Bebidas', 'Rutini', 'Tragos', '2021-07-08', '2021-07-08', 900),
(10, 'Bebidas', 'Chandon', 'Tragos', '2021-07-11', '2021-07-11', 900),
(11, 'Postre', 'Budin de pan', 'Candy Bar', '2021-07-11', '2021-07-11', 200),
(12, 'Postre', 'Ensalada de frutas', 'Candy Bar', '2021-07-11', '2021-07-11', 200),
(13, 'Comida', 'Papas Firtas', 'Cocina', '2021-07-12', '2021-07-12', 300),
(14, 'Postre', 'Lemon Pie', 'Candy Bar', '2021-07-12', '2021-07-12', 150),
(15, 'Comida', 'Pollo', 'Cocina', '2021-07-14', '2021-07-14', 300),
(16, 'Postre', 'Helado Vainilla', 'Candy Bar', '2021-07-14', '2021-07-14', 150),
(17, 'Postre', 'Tiramisu', 'Candy Bar', '2021-07-14', '2021-07-14', 150);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `idUsuarios` int(11) NOT NULL,
  `nombre` varchar(45) COLLATE utf8_spanish2_ci NOT NULL,
  `apellido` varchar(45) COLLATE utf8_spanish2_ci NOT NULL,
  `mail` varchar(45) COLLATE utf8_spanish2_ci NOT NULL,
  `clave` varchar(45) COLLATE utf8_spanish2_ci NOT NULL,
  `tipo` varchar(20) COLLATE utf8_spanish2_ci NOT NULL,
  `funcion` varchar(20) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `sector` varchar(20) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `fechaCreacion` date NOT NULL,
  `fechaModificacion` date NOT NULL,
  `fechaBaja` date DEFAULT NULL,
  `estado` varchar(20) COLLATE utf8_spanish2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`idUsuarios`, `nombre`, `apellido`, `mail`, `clave`, `tipo`, `funcion`, `sector`, `fechaCreacion`, `fechaModificacion`, `fechaBaja`, `estado`) VALUES
(1, 'Jose', 'Perez', 'jp@mail.com.ar', '12345', 'Empleado', 'Mozo', 'NA', '2021-06-09', '2021-07-13', '2021-07-13', 'Baja'),
(2, 'Ricardo', 'Suarez', 'rs@mail.com.ar', '12345', 'Empleado', 'Mozo', 'NA', '2021-06-09', '2021-07-07', NULL, 'Activo'),
(3, 'Marta', 'Elizaldez', 'me@mail.com.ar', '12345', 'Empleado', 'Mozo', 'NA', '2021-06-09', '2021-06-10', NULL, 'Activo'),
(4, 'Sebastian', 'Lopez', 'sl@mail.com.ar', '12345', 'Empleado', 'Mozo', 'NA', '2021-06-09', '2021-06-11', NULL, 'Suspendido'),
(5, 'Helena', 'Gimenez', 'hg@mail.com.ar', '12345', 'Empleado', 'Mozo', 'NA', '2021-06-09', '2021-06-09', NULL, 'Activo'),
(7, 'Luis', 'Martinez', 'lm@mail.com.ar', '12345', 'Empleado', 'Bartender', 'Tragos', '2021-06-11', '2021-07-07', '2021-07-07', 'Baja'),
(8, 'Alejandro', 'Bongioanni', 'albongle@mail.com.ar', '12345', 'Admin', 'NA', 'NA', '2021-07-08', '2021-07-13', NULL, 'Activo'),
(9, 'Mariela', 'Esquivel', 'mesquivel@mail.com.ar', '12345', 'Admin', 'NA', 'NA', '2021-07-08', '2021-07-13', NULL, 'Suspendido'),
(10, 'Jose', 'Carmona', 'jsc@mail.com.ar', '12345', 'Socio', 'NA', 'NA', '2021-07-08', '2021-07-08', NULL, 'Activo'),
(11, 'Raul', 'Taibo', 'rt@mail.com.ar', '12345', 'Empleado', 'Bartender', 'Tragos', '2021-07-08', '2021-07-08', NULL, 'Activo'),
(12, 'Marcelo', 'Neri', 'mn@mail.com.ar', '12345', 'Empleado', 'Cocinero', 'Cocina', '2021-07-08', '2021-07-08', NULL, 'Activo'),
(13, 'Sebastian', 'Lorenzo', 'slorenzo@mail.com.ar', '12345', 'Empleado', 'Cocinero', 'Candy Bar', '2021-07-08', '2021-07-08', NULL, 'Activo'),
(14, 'Diego', 'Vea', 'dv@mail.com.ar', '12345', 'Empleado', 'Cocinero', 'Cocina', '2021-07-08', '2021-07-08', NULL, 'Activo'),
(15, 'Leonel', 'Serrizuela', 'lzz@mail.com.ar', '12345', 'Empleado', 'Bartender', 'Choperas', '2021-07-08', '2021-07-08', NULL, 'Activo'),
(16, 'Pepe', 'Argento', 'pa@mail.com.ar', '12345', 'Empleado', 'Mozo', 'NA', '2021-07-08', '2021-07-13', NULL, 'Activo'),
(17, 'Maria Helena', 'Fuseneco', 'mhf@mail.com.ar', '12345', 'Empleado', 'Mozo', 'NA', '2021-07-08', '2021-07-08', NULL, 'Activo'),
(18, 'Juan Sebastian', 'Roca', 'jsr@mail.com.ar', '12345', 'Socio', 'NA', 'NA', '2021-07-12', '2021-07-12', NULL, 'Activo'),
(19, 'Maria Antonieta', 'Nieves', 'man@mail.com.ar', '12345', 'Empleado', 'Bartender', 'Choperas', '2021-07-12', '2021-07-12', NULL, 'Activo'),
(20, 'Edgar', 'Vivar', 'ev@mail.com.ar', '12345', 'Empleado', 'Cocinero', 'Cocina', '2021-07-12', '2021-07-12', NULL, 'Activo'),
(21, 'Camilo', 'Sexto', 'cs@mail.com.ar', '12345', 'Empleado', 'Cocinero', 'Candy Bar', '2021-07-12', '2021-07-12', NULL, 'Activo'),
(30, 'Juan Luis', 'Guerra', 'jlg@mail.com.ar', '12345', 'Empleado', 'Mozo', 'NA', '2021-07-13', '2021-07-13', NULL, 'Activo');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  ADD PRIMARY KEY (`idEncuestas`);

--
-- Indices de la tabla `facturacion`
--
ALTER TABLE `facturacion`
  ADD PRIMARY KEY (`idFacturacion`);

--
-- Indices de la tabla `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`idLogs`);

--
-- Indices de la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`idMesas`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`idPedidos`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`idProductos`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`idUsuarios`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  MODIFY `idEncuestas` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `facturacion`
--
ALTER TABLE `facturacion`
  MODIFY `idFacturacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `logs`
--
ALTER TABLE `logs`
  MODIFY `idLogs` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `idPedidos` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `idProductos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idUsuarios` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
