-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 25-11-2026 a las 22:32:28
-- Versión del servidor: 8.4.7
-- Versión de PHP: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `veterinaria`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cajas`
--

DROP TABLE IF EXISTS `cajas`;
CREATE TABLE IF NOT EXISTS `cajas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint UNSIGNED DEFAULT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `saldo_inicial` decimal(12,2) NOT NULL DEFAULT '0.00',
  `saldo_actual` decimal(12,2) NOT NULL DEFAULT '0.00',
  `estado` enum('abierta','cerrada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cerrada',
  `usuario_apertura_id` bigint UNSIGNED DEFAULT NULL,
  `usuario_cierre_id` bigint UNSIGNED DEFAULT NULL,
  `fecha_apertura` timestamp NULL DEFAULT NULL,
  `fecha_cierre` timestamp NULL DEFAULT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cajas_tenant_id_foreign` (`tenant_id`),
  KEY `cajas_usuario_apertura_id_foreign` (`usuario_apertura_id`),
  KEY `cajas_usuario_cierre_id_foreign` (`usuario_cierre_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cajas`
--

INSERT INTO `cajas` (`id`, `tenant_id`, `nombre`, `descripcion`, `saldo_inicial`, `saldo_actual`, `estado`, `usuario_apertura_id`, `usuario_cierre_id`, `fecha_apertura`, `fecha_cierre`, `activa`, `created_at`, `updated_at`) VALUES
(1, 1, 'Caja Principal', 'Caja principal de la clínica', 0.00, 1258110.00, 'abierta', 1, NULL, '2026-10-27 09:01:01', NULL, 1, '2026-10-27 08:59:43', '2026-11-23 10:04:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja_aperturas`
--

DROP TABLE IF EXISTS `caja_aperturas`;
CREATE TABLE IF NOT EXISTS `caja_aperturas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `caja_id` bigint UNSIGNED NOT NULL,
  `usuario_id` bigint UNSIGNED NOT NULL,
  `saldo_inicial` decimal(12,2) NOT NULL,
  `saldo_final` decimal(12,2) DEFAULT NULL,
  `fecha_apertura` timestamp NOT NULL,
  `fecha_cierre` timestamp NULL DEFAULT NULL,
  `estado` enum('abierta','cerrada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'abierta',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `caja_aperturas_caja_id_foreign` (`caja_id`),
  KEY `caja_aperturas_usuario_id_foreign` (`usuario_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `caja_aperturas`
--

INSERT INTO `caja_aperturas` (`id`, `caja_id`, `usuario_id`, `saldo_inicial`, `saldo_final`, `fecha_apertura`, `fecha_cierre`, `estado`, `observaciones`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 150000.00, NULL, '2026-10-27 09:01:01', NULL, 'abierta', 'saldo anterior', '2026-10-27 09:01:01', '2026-10-27 09:01:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja_movimientos`
--

DROP TABLE IF EXISTS `caja_movimientos`;
CREATE TABLE IF NOT EXISTS `caja_movimientos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `caja_id` bigint UNSIGNED NOT NULL,
  `caja_apertura_id` bigint UNSIGNED NOT NULL,
  `usuario_id` bigint UNSIGNED NOT NULL,
  `venta_id` bigint UNSIGNED DEFAULT NULL,
  `factura_id` bigint UNSIGNED DEFAULT NULL,
  `tipo` enum('ingreso','egreso') COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoria` enum('venta','factura','abono','gasto','retiro','ajuste','apertura','cierre') COLLATE utf8mb4_unicode_ci NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `saldo_anterior` decimal(12,2) NOT NULL,
  `saldo_nuevo` decimal(12,2) NOT NULL,
  `metodo_pago` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referencia` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `caja_movimientos_caja_apertura_id_foreign` (`caja_apertura_id`),
  KEY `caja_movimientos_usuario_id_foreign` (`usuario_id`),
  KEY `caja_movimientos_venta_id_foreign` (`venta_id`),
  KEY `caja_movimientos_factura_id_foreign` (`factura_id`),
  KEY `caja_movimientos_caja_id_created_at_index` (`caja_id`,`created_at`),
  KEY `caja_movimientos_tipo_categoria_index` (`tipo`,`categoria`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `caja_movimientos`
--

INSERT INTO `caja_movimientos` (`id`, `caja_id`, `caja_apertura_id`, `usuario_id`, `venta_id`, `factura_id`, `tipo`, `categoria`, `monto`, `saldo_anterior`, `saldo_nuevo`, `metodo_pago`, `referencia`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, NULL, NULL, 'ingreso', 'apertura', 150000.00, 0.00, 150000.00, 'efectivo', NULL, 'Apertura de caja - Saldo inicial: $150.000', '2026-10-27 09:01:02', '2026-10-27 09:01:02'),
(2, 1, 1, 1, NULL, NULL, 'egreso', 'gasto', 45000.00, 150000.00, 105000.00, 'efectivo', 'pago deuda mes', 'pago de deuda', '2026-10-27 09:02:28', '2026-10-27 09:02:28'),
(3, 1, 1, 1, 1, NULL, 'ingreso', 'venta', 324870.00, 105000.00, 429870.00, 'efectivo', NULL, 'Venta #V-000001', '2026-11-20 04:02:11', '2026-11-20 04:02:11'),
(4, 1, 1, 1, 2, NULL, 'ingreso', 'venta', 205870.00, 429870.00, 635740.00, 'efectivo', NULL, 'Venta #V-000002', '2026-11-20 04:23:07', '2026-11-20 04:23:07'),
(5, 1, 1, 1, 3, NULL, 'ingreso', 'venta', 399840.00, 635740.00, 1035580.00, 'efectivo', NULL, 'Venta #V-000003', '2026-11-20 09:31:17', '2026-11-20 09:31:17'),
(6, 1, 1, 1, 4, NULL, 'ingreso', 'venta', 222530.00, 1035580.00, 1258110.00, 'efectivo', NULL, 'Venta #V-000004', '2026-11-23 10:04:42', '2026-11-23 10:04:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `catalogos`
--

DROP TABLE IF EXISTS `catalogos`;
CREATE TABLE IF NOT EXISTS `catalogos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint UNSIGNED NOT NULL,
  `tipo` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valor` decimal(12,2) DEFAULT NULL,
  `padre_id` bigint UNSIGNED DEFAULT NULL,
  `orden` int UNSIGNED NOT NULL DEFAULT '0',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `catalogos_tenant_id_tipo_index` (`tenant_id`,`tipo`),
  KEY `catalogos_tenant_id_index` (`tenant_id`),
  KEY `catalogos_tipo_index` (`tipo`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `catalogos`
--

INSERT INTO `catalogos` (`id`, `tenant_id`, `tipo`, `nombre`, `descripcion`, `valor`, `padre_id`, `orden`, `activo`, `created_at`, `updated_at`) VALUES
(1, 1, 'especie', 'Perro', NULL, NULL, NULL, 0, 1, '2026-11-26 06:00:14', '2026-11-26 06:00:14'),
(2, 1, 'especie', 'Gato', NULL, NULL, NULL, 1, 1, '2026-11-26 06:00:14', '2026-11-26 06:00:14'),
(3, 1, 'especie', 'Ave', NULL, NULL, NULL, 2, 1, '2026-11-26 06:00:14', '2026-11-26 06:00:14'),
(4, 1, 'especie', 'Conejo', NULL, NULL, NULL, 3, 1, '2026-11-26 06:00:14', '2026-11-26 06:00:14'),
(5, 1, 'especie', 'Hámster', NULL, NULL, NULL, 4, 1, '2026-11-26 06:00:14', '2026-11-26 06:00:14'),
(6, 1, 'especie', 'Reptil', NULL, NULL, NULL, 5, 1, '2026-11-26 06:00:14', '2026-11-26 06:00:14'),
(7, 1, 'especie', 'Equino', NULL, NULL, NULL, 6, 1, '2026-11-26 06:00:14', '2026-11-26 06:00:14'),
(8, 1, 'especie', 'Bovino', NULL, NULL, NULL, 7, 1, '2026-11-26 06:00:14', '2026-11-26 06:00:14'),
(9, 1, 'servicio', 'Consulta general', NULL, NULL, NULL, 0, 1, '2026-11-26 06:00:52', '2026-11-26 06:00:52'),
(10, 1, 'servicio', 'Vacunación', NULL, NULL, NULL, 1, 1, '2026-11-26 06:00:52', '2026-11-26 06:00:52'),
(11, 1, 'servicio', 'Desparasitación', NULL, NULL, NULL, 2, 1, '2026-11-26 06:00:52', '2026-11-26 06:00:52'),
(12, 1, 'servicio', 'Cirugía', NULL, NULL, NULL, 3, 1, '2026-11-26 06:00:52', '2026-11-26 06:00:52'),
(13, 1, 'servicio', 'Control', NULL, NULL, NULL, 4, 1, '2026-11-26 06:00:52', '2026-11-26 06:00:52'),
(14, 1, 'servicio', 'Baño y peluquería', NULL, NULL, NULL, 5, 1, '2026-11-26 06:00:52', '2026-11-26 06:00:52'),
(15, 1, 'servicio', 'Hospitalización', NULL, NULL, NULL, 6, 1, '2026-11-26 06:00:52', '2026-11-26 06:00:52'),
(16, 1, 'tipo_cita', 'Consulta general', NULL, NULL, NULL, 0, 1, '2026-11-26 06:01:10', '2026-11-26 06:01:10'),
(17, 1, 'tipo_cita', 'Vacunación', NULL, NULL, NULL, 1, 1, '2026-11-26 06:01:10', '2026-11-26 06:01:10'),
(18, 1, 'tipo_cita', 'Desparasitación', NULL, NULL, NULL, 2, 1, '2026-11-26 06:01:10', '2026-11-26 06:01:10'),
(19, 1, 'tipo_cita', 'Cirugía', NULL, NULL, NULL, 3, 1, '2026-11-26 06:01:10', '2026-11-26 06:01:10'),
(20, 1, 'tipo_cita', 'Control', NULL, NULL, NULL, 4, 1, '2026-11-26 06:01:10', '2026-11-26 06:01:10'),
(21, 1, 'tipo_cita', 'Urgencia', NULL, NULL, NULL, 5, 1, '2026-11-26 06:01:10', '2026-11-26 06:01:10'),
(22, 1, 'tipo_cita', 'Peluquería', NULL, NULL, NULL, 6, 1, '2026-11-26 06:01:10', '2026-11-26 06:01:10'),
(23, 1, 'metodo_pago', 'Efectivo', NULL, NULL, NULL, 0, 1, '2026-11-26 06:01:27', '2026-11-26 06:01:27'),
(24, 1, 'metodo_pago', 'Tarjeta', NULL, NULL, NULL, 1, 1, '2026-11-26 06:01:27', '2026-11-26 06:01:27'),
(25, 1, 'metodo_pago', 'Transferencia', NULL, NULL, NULL, 2, 1, '2026-11-26 06:01:27', '2026-11-26 06:01:27'),
(26, 1, 'metodo_pago', 'Nequi', NULL, NULL, NULL, 3, 1, '2026-11-26 06:01:27', '2026-11-26 06:01:27'),
(27, 1, 'metodo_pago', 'Daviplata', NULL, NULL, NULL, 4, 1, '2026-11-26 06:01:27', '2026-11-26 06:01:27'),
(28, 1, 'categoria_producto', 'Alimentos', NULL, NULL, NULL, 0, 1, '2026-11-26 06:01:36', '2026-11-26 06:01:36'),
(29, 1, 'categoria_producto', 'Medicamentos', NULL, NULL, NULL, 1, 1, '2026-11-26 06:01:36', '2026-11-26 06:01:36'),
(30, 1, 'categoria_producto', 'Accesorios', NULL, NULL, NULL, 2, 1, '2026-11-26 06:01:36', '2026-11-26 06:01:36'),
(31, 1, 'categoria_producto', 'Higiene', NULL, NULL, NULL, 3, 1, '2026-11-26 06:01:36', '2026-11-26 06:01:36'),
(32, 1, 'categoria_producto', 'Juguetes', NULL, NULL, NULL, 4, 1, '2026-11-26 06:01:36', '2026-11-26 06:01:36'),
(33, 1, 'categoria_producto', 'Vacunas', NULL, NULL, NULL, 5, 1, '2026-11-26 06:01:36', '2026-11-26 06:01:36');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_productos`
--

DROP TABLE IF EXISTS `categorias_productos`;
CREATE TABLE IF NOT EXISTS `categorias_productos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(90) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `tipo` enum('producto','servicio','paquete','otro') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'producto',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `orden` int NOT NULL DEFAULT '0',
  `imagen` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icono` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categorias_productos_codigo_unique` (`codigo`),
  KEY `categorias_productos_tenant_id_foreign` (`tenant_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias_productos`
--

INSERT INTO `categorias_productos` (`id`, `tenant_id`, `nombre`, `codigo`, `descripcion`, `tipo`, `activo`, `orden`, `imagen`, `icono`, `color`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Alimentos', 'ALI', 'Alimentos y concentrados para mascotas', 'producto', 1, 1, NULL, 'fa-bone', '#F59E0B', '2026-10-26 08:29:44', '2026-10-26 08:29:44', NULL),
(2, 1, 'Medicamentos', 'MED', 'Medicamentos y vacunas', 'producto', 1, 2, NULL, 'fa-pills', '#3B82F6', '2026-10-26 08:29:45', '2026-10-26 08:29:45', NULL),
(3, 1, 'Accesorios', 'ACC', 'Collares, juguetes y accesorios', 'producto', 1, 3, NULL, 'fa-ring', '#8B5CF6', '2026-10-26 08:29:45', '2026-10-26 08:29:45', NULL),
(4, 1, 'Higiene', 'HIG', 'Shampoos, arena sanitaria, etc.', 'producto', 1, 4, NULL, 'fa-pump-soap', '#06B6D4', '2026-10-26 08:29:45', '2026-10-26 08:29:45', NULL),
(5, 1, 'Consultas', 'CON', 'Consultas veterinarias', 'servicio', 1, 5, NULL, 'fa-stethoscope', '#22C55E', '2026-10-26 08:29:45', '2026-10-26 08:29:45', NULL),
(6, 1, 'Cirugías', 'CIR', 'Procedimientos quirúrgicos', 'servicio', 1, 6, NULL, 'fa-scalpel', '#EF4444', '2026-10-26 08:29:45', '2026-10-26 08:29:45', NULL),
(7, 1, 'Estética', 'EST', 'Baños, cortes y estética canina', 'servicio', 1, 7, NULL, 'fa-cut', '#EC4899', '2026-10-26 08:29:45', '2026-10-26 08:29:45', NULL),
(8, 1, 'Paquetes', 'PAQ', 'Paquetes combinados de productos y servicios', 'paquete', 1, 8, NULL, 'fa-box-open', '#F97316', '2026-10-26 08:29:45', '2026-10-26 08:29:45', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas`
--

DROP TABLE IF EXISTS `citas`;
CREATE TABLE IF NOT EXISTS `citas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `sede_id` bigint UNSIGNED DEFAULT NULL,
  `tenant_id` bigint UNSIGNED DEFAULT NULL,
  `cliente_id` bigint UNSIGNED NOT NULL,
  `mascota_id` bigint UNSIGNED NOT NULL,
  `usuario_id` bigint UNSIGNED NOT NULL,
  `veterinario_id` bigint UNSIGNED DEFAULT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `tipo_consulta` text COLLATE utf8mb4_unicode_ci,
  `estado` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motivo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `costo` decimal(10,2) NOT NULL DEFAULT '0.00',
  `estado_pago` enum('pendiente','pagado','parcial') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `recordatorio_enviado` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `citas_tenant_id_foreign` (`tenant_id`),
  KEY `citas_fecha_estado_index` (`fecha`,`estado`),
  KEY `citas_usuario_id_fecha_index` (`usuario_id`,`fecha`),
  KEY `citas_cliente_id_fecha_index` (`cliente_id`,`fecha`),
  KEY `citas_mascota_id_fecha_index` (`mascota_id`,`fecha`),
  KEY `citas_sede_id_foreign` (`sede_id`),
  KEY `citas_veterinario_id_foreign` (`veterinario_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `citas`
--

INSERT INTO `citas` (`id`, `sede_id`, `tenant_id`, `cliente_id`, `mascota_id`, `usuario_id`, `veterinario_id`, `fecha`, `hora_inicio`, `hora_fin`, `tipo_consulta`, `estado`, `motivo`, `observaciones`, `costo`, `estado_pago`, `recordatorio_enviado`, `created_at`, `updated_at`) VALUES
(2, 4, 1, 1, 2, 1, 4, '2026-10-26', '10:00:00', '10:30:00', 'consulta_general', 'pendiente', 'ninguna', NULL, 0.00, 'pendiente', 0, '2026-10-27 07:52:03', '2026-10-28 08:50:51'),
(4, 3, 1, 1, 2, 1, 1, '2026-10-02', '08:00:00', '08:30:00', 'control', 'confirmada', 'ninguno', NULL, 0.00, 'pendiente', 0, '2026-10-28 07:42:42', '2026-10-28 07:42:42'),
(5, 2, 1, 1, 1, 1, 5, '2026-10-27', '07:00:00', '07:30:00', 'consulta_general', 'en_atencion', 'ninguno', NULL, 0.00, 'pendiente', 0, '2026-10-28 07:59:29', '2026-10-28 07:59:29'),
(6, 2, 1, 1, 1, 1, 3, '2026-10-03', '08:00:00', '08:30:00', 'cirugia', 'en_atencion', 'ninguno', NULL, 0.00, 'pendiente', 0, '2026-10-28 08:08:43', '2026-10-29 00:23:11'),
(7, 2, 1, 1, 1, 1, 2, '2026-10-28', '08:00:00', '08:30:00', 'cirugia', 'finalizada', 'Cirugia de catarata', NULL, 0.00, 'pendiente', 0, '2026-10-29 03:12:20', '2026-10-29 04:09:40'),
(8, 3, 1, 1, 1, 1, 1, '2026-11-02', '08:00:00', '08:30:00', 'estetica', 'en_atencion', 'Ninguno', NULL, 0.00, 'pendiente', 0, '2026-11-01 10:24:59', '2026-11-01 10:24:59'),
(9, 2, 1, 1, 2, 1, 1, '2026-11-03', '10:00:00', '10:30:00', 'emergencia', 'cancelada', 'ninguno', NULL, 0.00, 'pendiente', 0, '2026-11-01 10:25:54', '2026-11-15 10:17:19'),
(10, 4, 1, 1, 2, 1, 2, '2026-11-05', '07:00:00', '07:30:00', 'desparasitacion', 'pendiente', 'ninguno', NULL, 0.00, 'pendiente', 0, '2026-11-01 10:27:33', '2026-11-01 10:27:33'),
(11, 3, 1, 2, 3, 1, 1, '2026-11-06', '07:30:00', '08:00:00', 'consulta_general', 'pendiente', 'Consulta mensual de control', NULL, 0.00, 'pendiente', 0, '2026-11-05 21:57:08', '2026-11-05 21:57:08'),
(12, 2, 1, 1, 2, 1, 4, '2026-11-07', '08:00:00', '08:30:00', 'cirugia', 'confirmada', 'cirugia de catarata', NULL, 0.00, 'pendiente', 0, '2026-11-05 21:58:03', '2026-11-05 21:58:03'),
(13, 4, 1, 1, 2, 1, 1, '2026-11-09', '10:00:00', '10:30:00', 'control', 'en_atencion', 'Control mensual de tiroides', NULL, 0.00, 'pendiente', 0, '2026-11-05 21:59:32', '2026-11-05 21:59:41'),
(14, 4, 1, 2, 3, 1, 5, '2026-10-29', '07:30:00', '08:00:00', 'cirugia', 'pendiente', 'cirugía de encía', NULL, 0.00, 'pendiente', 0, '2026-11-08 20:46:52', '2026-11-08 20:46:52'),
(15, 2, 1, 1, 2, 1, 4, '2026-11-08', '07:30:00', '08:00:00', 'vacunacion', 'confirmada', 'Ninguna', NULL, 0.00, 'pendiente', 0, '2026-11-08 20:47:43', '2026-11-08 20:47:43'),
(16, 3, 1, 1, 1, 1, 3, '2026-11-08', '09:30:00', '10:00:00', 'desparasitacion', 'finalizada', 'ninguna', NULL, 0.00, 'pendiente', 0, '2026-11-08 20:48:26', '2026-11-08 20:48:33'),
(17, 3, 1, 1, 2, 1, 1, '2026-11-15', '08:00:00', '08:30:00', 'cirugia', 'en_atencion', 'Ninguno', NULL, 0.00, 'pendiente', 0, '2026-11-15 10:06:44', '2026-11-15 10:09:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint UNSIGNED NOT NULL,
  `tipo_documento` enum('CC','CE','NIT','PAS') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CC',
  `numero_documento` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombres` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `genero` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `celular` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barrio` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ciudad` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clientes_tenant_id_numero_documento_unique` (`tenant_id`,`numero_documento`),
  KEY `clientes_tenant_id_nombres_index` (`tenant_id`,`nombres`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `tenant_id`, `tipo_documento`, `numero_documento`, `nombres`, `apellidos`, `fecha_nacimiento`, `genero`, `email`, `celular`, `direccion`, `barrio`, `ciudad`, `activo`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'CC', '34566812', 'martha', 'rosales casas', NULL, 'femenino', 'martha.rosales@gmail.com', '3170813912', 'calle 23 # 12 - 43', 'los ocobos', 'cartago', 1, '2026-10-26 22:43:20', '2026-10-26 22:44:09', NULL),
(2, 1, 'CC', '31245672', 'luz enith', 'calvo villada', NULL, 'femenino', 'enith.calvo@gmail.com', '3183456195', 'calle 45 # 12 - 34', 'el troncal', 'cali', 1, '2026-10-29 21:46:43', '2026-10-29 21:50:23', NULL),
(3, 1, 'CC', '32434292', 'amalfi', 'moreno valencia', '1980-05-12', 'femenino', 'amafi.moreno@gmail.com', '31345934212', 'calle 12 C # 12 - 30', 'los cambulos', 'palmira', 1, '2026-11-01 11:07:29', '2026-11-01 11:07:29', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

DROP TABLE IF EXISTS `compras`;
CREATE TABLE IF NOT EXISTS `compras` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint UNSIGNED NOT NULL,
  `proveedor_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `numero_factura_proveedor` varchar(18) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_compra` date NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `metodo_pago` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `iva_valor` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `items_json` json DEFAULT NULL,
  `estado` enum('pendiente','recibida','anulada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `observacion` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `compras_proveedor_id_foreign` (`proveedor_id`),
  KEY `compras_user_id_foreign` (`user_id`),
  KEY `compras_tenant_id_fecha_compra_index` (`tenant_id`,`fecha_compra`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `compras`
--

INSERT INTO `compras` (`id`, `tenant_id`, `proveedor_id`, `user_id`, `numero_factura_proveedor`, `fecha_compra`, `fecha_vencimiento`, `metodo_pago`, `subtotal`, `iva_valor`, `total`, `items_json`, `estado`, `observacion`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 1, '00001', '2026-11-17', '2026-12-17', 'efectivo', 376000.00, 71440.00, 447440.00, '[{\"cantidad\": \"1\", \"subtotal\": 30000, \"producto_id\": \"2\", \"precio_unitario\": \"30000\"}, {\"cantidad\": \"3\", \"subtotal\": 135000, \"producto_id\": \"3\", \"precio_unitario\": \"45000\"}, {\"cantidad\": \"1\", \"subtotal\": 26000, \"producto_id\": \"13\", \"precio_unitario\": \"26000\"}, {\"cantidad\": \"2\", \"subtotal\": 120000, \"producto_id\": \"1\", \"precio_unitario\": \"60000\"}, {\"cantidad\": \"1\", \"subtotal\": 45000, \"producto_id\": \"3\", \"precio_unitario\": \"45000\"}, {\"cantidad\": \"1\", \"subtotal\": 20000, \"producto_id\": \"5\", \"precio_unitario\": \"20000\"}]', 'pendiente', NULL, '2026-11-18 00:17:19', '2026-11-18 10:02:32'),
(2, 1, 1, 1, '00002', '2026-11-18', '2026-12-20', 'tarjeta_credito', 191000.00, 36290.00, 227290.00, '[{\"cantidad\": \"1\", \"subtotal\": 30000, \"producto_id\": \"2\", \"precio_unitario\": \"30000\"}, {\"cantidad\": \"3\", \"subtotal\": 135000, \"producto_id\": \"3\", \"precio_unitario\": \"45000\"}, {\"cantidad\": \"1\", \"subtotal\": 26000, \"producto_id\": \"13\", \"precio_unitario\": \"26000\"}]', 'pendiente', NULL, '2026-11-18 10:04:22', '2026-11-18 10:06:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra_items`
--

DROP TABLE IF EXISTS `compra_items`;
CREATE TABLE IF NOT EXISTS `compra_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `compra_id` bigint UNSIGNED NOT NULL,
  `producto_id` bigint UNSIGNED NOT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `compra_items_compra_id_foreign` (`compra_id`),
  KEY `compra_items_producto_id_foreign` (`producto_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `compra_items`
--

INSERT INTO `compra_items` (`id`, `compra_id`, `producto_id`, `cantidad`, `precio_unitario`, `subtotal`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 1, 30000.00, 30000.00, '2026-11-17 23:17:22', '2026-11-17 23:17:22'),
(2, 2, 2, 1, 30000.00, 30000.00, '2026-11-17 23:17:22', '2026-11-17 23:17:22'),
(3, 2, 3, 3, 45000.00, 135000.00, '2026-11-17 23:17:22', '2026-11-17 23:17:22'),
(4, 1, 3, 3, 45000.00, 135000.00, '2026-11-17 23:17:22', '2026-11-17 23:17:22'),
(5, 2, 13, 1, 26000.00, 26000.00, '2026-11-17 23:17:22', '2026-11-17 23:17:22'),
(6, 1, 13, 1, 26000.00, 26000.00, '2026-11-17 23:17:22', '2026-11-17 23:17:22'),
(7, 1, 1, 2, 60000.00, 120000.00, '2026-11-18 00:17:19', '2026-11-18 00:17:19'),
(8, 1, 3, 1, 45000.00, 45000.00, '2026-11-18 00:17:20', '2026-11-18 00:17:20'),
(9, 1, 5, 1, 20000.00, 20000.00, '2026-11-18 00:17:20', '2026-11-18 00:17:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuraciones`
--

DROP TABLE IF EXISTS `configuraciones`;
CREATE TABLE IF NOT EXISTS `configuraciones` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint UNSIGNED NOT NULL,
  `nombre_clinica` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'VetFlow',
  `razon_social` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nit` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ciudad` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sitio_web` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `iva_porcentaje` decimal(5,2) NOT NULL DEFAULT '19.00',
  `prefijo_factura` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'POS-',
  `moneda` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'COP',
  `formato_impresion` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ticket',
  `pie_factura` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duracion_cita_min` smallint UNSIGNED NOT NULL DEFAULT '30',
  `horario` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `configuraciones_tenant_id_unique` (`tenant_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `configuraciones`
--

INSERT INTO `configuraciones` (`id`, `tenant_id`, `nombre_clinica`, `razon_social`, `nit`, `direccion`, `ciudad`, `telefono`, `email`, `sitio_web`, `logo`, `iva_porcentaje`, `prefijo_factura`, `moneda`, `formato_impresion`, `pie_factura`, `duracion_cita_min`, `horario`, `created_at`, `updated_at`) VALUES
(1, 1, 'VetFlow', 'Clinica veterinaria VetFlow', '439432054-7', 'calle 25 # 12 - 21', 'cali', '3170123492', 'ayuda@vetflow.com', 'www.vetflow.com', 'logos/K5UC72jRKes0cgv0Qr1BPQAqqTNX6acJGAwTCT5z.png', 19.00, 'POS-', 'COP', 'ticket', NULL, 30, NULL, '2026-11-26 05:45:11', '2026-11-26 05:59:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `desparasitacion_aplicaciones`
--

DROP TABLE IF EXISTS `desparasitacion_aplicaciones`;
CREATE TABLE IF NOT EXISTS `desparasitacion_aplicaciones` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `desparasitante_id` bigint UNSIGNED NOT NULL,
  `mascota_id` bigint UNSIGNED NOT NULL,
  `usuario_id` bigint UNSIGNED NOT NULL,
  `fecha_aplicacion` date NOT NULL,
  `fecha_proxima` date DEFAULT NULL,
  `dosis_aplicada` decimal(10,2) NOT NULL DEFAULT '1.00',
  `lote` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `via_aplicacion` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'oral',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `costo` decimal(10,2) NOT NULL DEFAULT '0.00',
  `estado` enum('aplicada','programada','cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aplicada',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `desparasitacion_aplicaciones_usuario_id_foreign` (`usuario_id`),
  KEY `idx_desa_mascota_fecha` (`mascota_id`,`fecha_aplicacion`),
  KEY `idx_desa_producto_fecha` (`desparasitante_id`,`fecha_aplicacion`),
  KEY `idx_desa_fecha_proxima` (`fecha_proxima`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `desparasitacion_aplicaciones`
--

INSERT INTO `desparasitacion_aplicaciones` (`id`, `desparasitante_id`, `mascota_id`, `usuario_id`, `fecha_aplicacion`, `fecha_proxima`, `dosis_aplicada`, `lote`, `via_aplicacion`, `observaciones`, `costo`, `estado`, `created_at`, `updated_at`) VALUES
(1, 3, 2, 5, '2026-11-12', '2027-04-10', 0.00, 'LOT-AR-1934', 'oral', 'ninguna', 7600.00, 'cancelada', '2026-11-12 09:51:40', '2026-11-14 00:40:47'),
(3, 3, 2, 2, '2026-11-12', '2027-02-20', 0.00, 'LOT-DT-1492', 'oral', 'ninguna', 8700.00, 'aplicada', '2026-11-12 10:08:43', '2026-11-12 10:08:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `desparasitantes`
--

DROP TABLE IF EXISTS `desparasitantes`;
CREATE TABLE IF NOT EXISTS `desparasitantes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint UNSIGNED DEFAULT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `laboratorio` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lote` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `tipo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'interno',
  `principio_activo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stock` int NOT NULL DEFAULT '0',
  `stock_minimo` int NOT NULL DEFAULT '5',
  `fecha_caducidad` date DEFAULT NULL,
  `via_aplicacion` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'oral',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `desparasitantes_tenant_id_foreign` (`tenant_id`),
  KEY `desparasitantes_nombre_tipo_activo_index` (`nombre`,`tipo`,`activo`),
  KEY `desparasitantes_fecha_caducidad_index` (`fecha_caducidad`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `desparasitantes`
--

INSERT INTO `desparasitantes` (`id`, `tenant_id`, `nombre`, `laboratorio`, `lote`, `descripcion`, `tipo`, `principio_activo`, `precio`, `stock`, `stock_minimo`, `fecha_caducidad`, `via_aplicacion`, `activo`, `created_at`, `updated_at`) VALUES
(1, 1, 'Bravecto', 'MSD Animal Health', 'LOT-BRV-001', 'Tableta masticable para el tratamiento y prevención de pulgas y garrapatas.', 'externa', 'Fluralaner', 65000.00, 20, 5, '2027-11-12', 'oral', 1, '2026-11-12 08:51:04', '2026-11-12 08:51:04'),
(2, 1, 'NexGard', 'Boehringer Ingelheim', 'LOT-NXG-002', 'Tableta masticable para perros, elimina pulgas y garrapatas.', 'externa', 'Afoxolaner', 58000.00, 25, 5, '2027-09-12', 'oral', 1, '2026-11-12 08:51:04', '2026-11-12 08:51:04'),
(3, 1, 'Drontal Plus', 'Bayer', 'LOT-DRT-003', 'Desparasitante interno de amplio espectro para perros y gatos.', 'interna', 'Praziquantel + Pirantel + Febantel', 22000.00, 40, 10, '2027-07-12', 'oral', 1, '2026-11-12 08:51:04', '2026-11-12 08:51:04'),
(4, 1, 'Milbemax', 'Elanco', 'LOT-MLB-004', 'Desparasitante interno para gatos y perros de razas pequeñas.', 'interna', 'Milbemicina oxima + Praziquantel', 28000.00, 15, 5, '2027-11-12', 'oral', 1, '2026-11-12 08:51:04', '2026-11-12 08:51:04'),
(5, 1, 'Frontline Plus', 'Boehringer Ingelheim', 'LOT-FRT-005', 'Pipeta para el control de pulgas y garrapatas en perros y gatos.', 'externa', 'Fipronil + (S)-metopreno', 35000.00, 30, 10, '2027-11-12', 'topica', 1, '2026-11-12 08:51:04', '2026-11-12 08:51:04'),
(6, 1, 'Endogard', 'Virbac', 'LOT-END-006', 'Desparasitante mixto para perros y gatos.', 'mixta', 'Praziquantel + Pirantel + Febantel', 26000.00, 18, 5, '2027-05-12', 'oral', 1, '2026-11-12 08:51:04', '2026-11-12 08:51:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `examenes`
--

DROP TABLE IF EXISTS `examenes`;
CREATE TABLE IF NOT EXISTS `examenes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint UNSIGNED NOT NULL,
  `mascota_id` bigint UNSIGNED NOT NULL,
  `historia_clinica_id` bigint UNSIGNED DEFAULT NULL,
  `veterinario_id` bigint UNSIGNED DEFAULT NULL,
  `categoria` enum('examen','laboratorio') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha` date NOT NULL,
  `resultado` text COLLATE utf8mb4_unicode_ci,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `archivo_ruta` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `archivo_nombre` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `examenes_mascota_id_foreign` (`mascota_id`),
  KEY `examenes_historia_clinica_id_foreign` (`historia_clinica_id`),
  KEY `examenes_veterinario_id_foreign` (`veterinario_id`),
  KEY `examenes_tenant_id_index` (`tenant_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

DROP TABLE IF EXISTS `facturas`;
CREATE TABLE IF NOT EXISTS `facturas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint UNSIGNED DEFAULT NULL,
  `cliente_id` bigint UNSIGNED NOT NULL,
  `venta_id` bigint UNSIGNED DEFAULT NULL,
  `numero_factura` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_emision` date NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `tipo` enum('venta','servicio','consulta','proforma') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'venta',
  `estado` enum('pendiente','pagada','anulada','vencida') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `iva` decimal(12,2) NOT NULL DEFAULT '0.00',
  `descuento` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `abonado` decimal(12,2) NOT NULL DEFAULT '0.00',
  `saldo` decimal(12,2) NOT NULL DEFAULT '0.00',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `metodo_pago` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referencia_pago` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_pago` date DEFAULT NULL,
  `usuario_id` bigint UNSIGNED NOT NULL,
  `configuracion` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `facturas_numero_factura_unique` (`numero_factura`),
  KEY `facturas_tenant_id_foreign` (`tenant_id`),
  KEY `facturas_venta_id_foreign` (`venta_id`),
  KEY `facturas_usuario_id_foreign` (`usuario_id`),
  KEY `facturas_cliente_id_estado_index` (`cliente_id`,`estado`),
  KEY `facturas_fecha_emision_estado_index` (`fecha_emision`,`estado`),
  KEY `facturas_numero_factura_index` (`numero_factura`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura_detalles`
--

DROP TABLE IF EXISTS `factura_detalles`;
CREATE TABLE IF NOT EXISTS `factura_detalles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `factura_id` bigint UNSIGNED NOT NULL,
  `producto_id` bigint UNSIGNED DEFAULT NULL,
  `descripcion` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` decimal(10,2) NOT NULL DEFAULT '1.00',
  `precio_unitario` decimal(12,2) NOT NULL,
  `descuento` decimal(12,2) NOT NULL DEFAULT '0.00',
  `iva` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL,
  `tipo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'producto',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `factura_detalles_producto_id_foreign` (`producto_id`),
  KEY `factura_detalles_factura_id_index` (`factura_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura_pagos`
--

DROP TABLE IF EXISTS `factura_pagos`;
CREATE TABLE IF NOT EXISTS `factura_pagos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `factura_id` bigint UNSIGNED NOT NULL,
  `fecha_pago` date NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `metodo_pago` enum('efectivo','tarjeta_credito','tarjeta_debito','transferencia','nequi','daviplata','qr') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'efectivo',
  `referencia` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `usuario_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `factura_pagos_usuario_id_foreign` (`usuario_id`),
  KEY `factura_pagos_factura_id_index` (`factura_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `formula_medica`
--

DROP TABLE IF EXISTS `formula_medica`;
CREATE TABLE IF NOT EXISTS `formula_medica` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `historia_clinica_id` bigint UNSIGNED NOT NULL,
  `producto` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `presentacion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dosis` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cantidad` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frecuencia` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duracion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `formula_medica_historia_clinica_id_foreign` (`historia_clinica_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historias_clinicas`
--

DROP TABLE IF EXISTS `historias_clinicas`;
CREATE TABLE IF NOT EXISTS `historias_clinicas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint UNSIGNED NOT NULL,
  `mascota_id` bigint UNSIGNED NOT NULL,
  `veterinario_id` bigint UNSIGNED DEFAULT NULL,
  `cita_id` bigint UNSIGNED DEFAULT NULL,
  `fecha_consulta` datetime NOT NULL,
  `motivo_consulta` text COLLATE utf8mb4_unicode_ci,
  `anamnesis` text COLLATE utf8mb4_unicode_ci,
  `diagnostico` text COLLATE utf8mb4_unicode_ci,
  `tratamiento` text COLLATE utf8mb4_unicode_ci,
  `temperatura` decimal(5,2) DEFAULT NULL,
  `frecuencia_cardiaca` int DEFAULT NULL,
  `frecuencia_respiratoria` int DEFAULT NULL,
  `peso` decimal(6,2) DEFAULT NULL,
  `estado_corporal` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hidratacion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `examen_fisico` text COLLATE utf8mb4_unicode_ci,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `proxima_cita_sugerida` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `historias_clinicas_mascota_id_foreign` (`mascota_id`),
  KEY `historias_clinicas_veterinario_id_foreign` (`veterinario_id`),
  KEY `historias_clinicas_cita_id_foreign` (`cita_id`),
  KEY `historias_clinicas_tenant_id_mascota_id_index` (`tenant_id`,`mascota_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historia_clinica_archivos`
--

DROP TABLE IF EXISTS `historia_clinica_archivos`;
CREATE TABLE IF NOT EXISTS `historia_clinica_archivos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `historia_clinica_id` bigint UNSIGNED NOT NULL,
  `nombre_original` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ruta` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('imagen','pdf') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'imagen',
  `peso_kb` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `historia_clinica_archivos_historia_clinica_id_foreign` (`historia_clinica_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mascotas`
--

DROP TABLE IF EXISTS `mascotas`;
CREATE TABLE IF NOT EXISTS `mascotas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint UNSIGNED NOT NULL,
  `cliente_id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `especie` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `raza` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sexo` enum('Macho','Hembra') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `genero` enum('macho','hembra') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `peso` decimal(6,2) DEFAULT NULL,
  `numero_chip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `microchip` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alergias` text COLLATE utf8mb4_unicode_ci,
  `enfermedades_cronicas` text COLLATE utf8mb4_unicode_ci,
  `esterilizado` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('activo','inactivo','fallecido') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `foto` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notas` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mascotas_cliente_id_foreign` (`cliente_id`),
  KEY `mascotas_tenant_id_nombre_index` (`tenant_id`,`nombre`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `mascotas`
--

INSERT INTO `mascotas` (`id`, `tenant_id`, `cliente_id`, `nombre`, `especie`, `raza`, `sexo`, `fecha_nacimiento`, `genero`, `peso`, `numero_chip`, `color`, `microchip`, `alergias`, `enfermedades_cronicas`, `esterilizado`, `estado`, `foto`, `notas`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 'lulu', 'perro', 'french poodle', NULL, '2024-06-12', 'hembra', 6.00, '2459241983', 'blanco', NULL, 'ninguna', 'diabetes', '1', 'activo', NULL, 'presenta dificultad al caminar', '2026-10-26 23:53:28', '2026-10-31 10:07:11', NULL),
(2, 1, 1, 'aldo', 'gato', 'angora', NULL, '2021-08-02', 'macho', 5.00, '321452345', 'negro', NULL, 'Ninguna', 'Ninguna', '0', 'activo', NULL, 'Ninguna', '2026-10-27 06:21:48', '2026-10-27 06:21:48', NULL),
(3, 1, 2, 'ramon', 'gato', 'angora', NULL, '2023-06-12', 'macho', 3.00, '32345821', 'blanco', NULL, 'ninguna', 'Tiroides', '1', 'activo', NULL, 'Ninguna', '2026-10-29 21:49:55', '2026-10-29 21:49:55', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_100000_create_password_resets_table', 1),
(2, '2019_08_19_000000_create_failed_jobs_table', 1),
(3, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(4, '2024_01_01_000001_create_tenants_table', 1),
(5, '2024_01_01_000002_create_users_table', 1),
(6, '2024_01_01_000003_create_permission_tables', 1),
(7, '2024_01_01_000010_create_clientes_table', 1),
(8, '2024_01_01_000011_create_mascotas_table', 1),
(9, '2024_01_01_000012_create_citas_table', 1),
(10, '2024_01_01_000013_create_historias_clinicas_table', 1),
(11, '2024_01_01_000014_create_sedes_table', 1),
(12, '2024_01_01_000020_create_productos_table', 1),
(13, '2024_01_01_000023_create_ventas_table', 1),
(14, '2024_01_01_000024_create_compras_table', 1),
(15, '2026_08_28_024940_create_facturas_table', 1),
(16, '2026_08_28_040433_create_factura_detalles_table', 1),
(17, '2026_08_28_043843_create_factura_pagos_table', 1),
(18, '2026_09_03_003259_create_vacunas_table', 1),
(19, '2026_09_03_014528_create_vacuna_aplicaciones_table', 1),
(20, '2026_09_03_020137_create_desparasitantes_table', 1),
(21, '2026_09_03_020845_create_desparasitacion_aplicaciones_table', 1),
(22, '2026_09_08_170816_create_proveedores_table', 1),
(23, '2026_09_09_020636_create_cajas_table', 1),
(24, '2026_09_09_023853_create_caja_aperturas_table', 1),
(25, '2026_09_09_024718_create_caja_movimientos_table', 1),
(26, '2026_09_15_200009_create_categorias_productos_table', 1),
(27, '2026_09_18_021657_add_pago_detalles_to_ventas_table', 1),
(28, '2026_09_22_041240_create__ventadetalle_table', 1),
(29, '2026_10_06_223723_create_examenes_table', 1),
(30, '2026_10_07_223534_create_recetas_tables', 1),
(31, '2026_10_20_000001_agregar_campos_pago_a_ventas', 1),
(32, '2026_09_22_041240_create_ventadetalle_table', 2),
(33, '2026_10_25_193946_add_missing_columns_to_categorias_productos_table', 2),
(34, '2026_10_26_165732_add_genero_to_clientes_table', 3),
(35, '2026_10_26_180547_add_missing_columns_to_mascotas_table', 4),
(36, '2026_10_26_184125_add_missing_fields_to_mascotas_table', 5),
(37, '2026_10_27_181324_create_veterinarios_table', 6),
(38, '2026_10_27_184553_update_citas_veterinario_foreign_key', 7),
(39, '2026_11_11_055052_change_tipo_to_string_on_desparasitantes_table', 8),
(40, '2026_11_17_185839_add_items_json_to_compras_table', 9),
(41, '2026_11_20_014858_add_vendedor_to_ventas_table', 10),
(42, '2026_11_24_012257_create_reportes_table', 11),
(43, '2026_11_25_043826_create_configuracion_table', 12);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(3, 'App\\Models\\User', 2),
(4, 'App\\Models\\User', 3),
(4, 'App\\Models\\User', 5),
(5, 'App\\Models\\User', 4),
(6, 'App\\Models\\User', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'ver_dashboard', 'web', '2026-10-26 00:19:03', '2026-10-26 00:19:03'),
(2, 'ver_clientes', 'web', '2026-10-26 00:19:04', '2026-10-26 00:19:04'),
(3, 'crear_clientes', 'web', '2026-10-26 00:19:04', '2026-10-26 00:19:04'),
(4, 'editar_clientes', 'web', '2026-10-26 00:19:04', '2026-10-26 00:19:04'),
(5, 'eliminar_clientes', 'web', '2026-10-26 00:19:04', '2026-10-26 00:19:04'),
(6, 'ver_mascotas', 'web', '2026-10-26 00:19:05', '2026-10-26 00:19:05'),
(7, 'crear_mascotas', 'web', '2026-10-26 00:19:05', '2026-10-26 00:19:05'),
(8, 'editar_mascotas', 'web', '2026-10-26 00:19:05', '2026-10-26 00:19:05'),
(9, 'eliminar_mascotas', 'web', '2026-10-26 00:19:05', '2026-10-26 00:19:05'),
(10, 'ver_citas', 'web', '2026-10-26 00:19:05', '2026-10-26 00:19:05'),
(11, 'crear_citas', 'web', '2026-10-26 00:19:05', '2026-10-26 00:19:05'),
(12, 'editar_citas', 'web', '2026-10-26 00:19:05', '2026-10-26 00:19:05'),
(13, 'eliminar_citas', 'web', '2026-10-26 00:19:06', '2026-10-26 00:19:06'),
(14, 'ver_historias', 'web', '2026-10-26 00:19:06', '2026-10-26 00:19:06'),
(15, 'crear_historias', 'web', '2026-10-26 00:19:06', '2026-10-26 00:19:06'),
(16, 'editar_historias', 'web', '2026-10-26 00:19:06', '2026-10-26 00:19:06'),
(17, 'eliminar_historias', 'web', '2026-10-26 00:19:06', '2026-10-26 00:19:06'),
(18, 'ver_productos', 'web', '2026-10-26 00:19:07', '2026-10-26 00:19:07'),
(19, 'crear_productos', 'web', '2026-10-26 00:19:08', '2026-10-26 00:19:08'),
(20, 'editar_productos', 'web', '2026-10-26 00:19:09', '2026-10-26 00:19:09'),
(21, 'eliminar_productos', 'web', '2026-10-26 00:19:09', '2026-10-26 00:19:09'),
(22, 'ver_ventas', 'web', '2026-10-26 00:19:10', '2026-10-26 00:19:10'),
(23, 'crear_ventas', 'web', '2026-10-26 00:19:11', '2026-10-26 00:19:11'),
(24, 'editar_ventas', 'web', '2026-10-26 00:19:11', '2026-10-26 00:19:11'),
(25, 'eliminar_ventas', 'web', '2026-10-26 00:19:12', '2026-10-26 00:19:12'),
(26, 'ver_reportes', 'web', '2026-10-26 00:19:12', '2026-10-26 00:19:12'),
(27, 'ver_usuarios', 'web', '2026-10-26 00:19:12', '2026-10-26 00:19:12'),
(28, 'crear_usuarios', 'web', '2026-10-26 00:19:12', '2026-10-26 00:19:12'),
(29, 'editar_usuarios', 'web', '2026-10-26 00:19:13', '2026-10-26 00:19:13'),
(30, 'eliminar_usuarios', 'web', '2026-10-26 00:19:13', '2026-10-26 00:19:13'),
(31, 'ver_configuracion', 'web', '2026-10-26 00:19:13', '2026-10-26 00:19:13'),
(32, 'editar_configuracion', 'web', '2026-10-26 00:19:13', '2026-10-26 00:19:13'),
(33, 'ver_caja', 'web', '2026-10-26 00:19:13', '2026-10-26 00:19:13'),
(34, 'gestionar_caja', 'web', '2026-10-26 00:19:14', '2026-10-26 00:19:14'),
(35, 'ver_facturas', 'web', '2026-11-19 04:22:26', '2026-11-19 04:22:26'),
(36, 'crear_facturas', 'web', '2026-11-19 04:22:28', '2026-11-19 04:22:28'),
(37, 'editar_facturas', 'web', '2026-11-19 04:22:28', '2026-11-19 04:22:28'),
(38, 'eliminar_facturas', 'web', '2026-11-19 04:22:28', '2026-11-19 04:22:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

DROP TABLE IF EXISTS `productos`;
CREATE TABLE IF NOT EXISTS `productos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint UNSIGNED NOT NULL,
  `categoria_id` bigint UNSIGNED DEFAULT NULL,
  `codigo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `tipo` enum('producto','servicio','paquete') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'producto',
  `precio_compra` decimal(12,2) NOT NULL DEFAULT '0.00',
  `precio_venta` decimal(12,2) NOT NULL DEFAULT '0.00',
  `iva_porcentaje` decimal(5,2) NOT NULL DEFAULT '19.00',
  `stock` int NOT NULL DEFAULT '0',
  `stock_minimo` int NOT NULL DEFAULT '5',
  `unidad_medida` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagen` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `productos_categoria_id_foreign` (`categoria_id`),
  KEY `productos_tenant_id_nombre_index` (`tenant_id`,`nombre`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `tenant_id`, `categoria_id`, `codigo`, `nombre`, `descripcion`, `tipo`, `precio_compra`, `precio_venta`, `iva_porcentaje`, `stock`, `stock_minimo`, `unidad_medida`, `imagen`, `activo`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 'PROD-001', 'Concentrado Premium Perro Adulto 10kg', 'Alimento balanceado con pollo y arroz', 'producto', 60000.00, 85000.00, 19.00, 15, 5, NULL, NULL, 1, '2026-11-17 23:55:22', '2026-11-18 23:11:29', '2026-11-18 23:11:29'),
(2, 1, 1, 'PROD-002', 'Concentrado Gato Adulto 3kg', 'Alimento para gatos con control de bolas de pelo', 'producto', 30000.00, 45000.00, 19.00, 16, 5, NULL, NULL, 1, '2026-11-17 23:55:23', '2026-11-23 10:04:36', NULL),
(3, 1, 2, 'PROD-003', 'Desparasitante Bravecto 20-40kg', 'Tableta masticable contra pulgas y garrapatas', 'producto', 45000.00, 65000.00, 19.00, 4, 5, NULL, NULL, 1, '2026-11-17 23:55:23', '2026-11-23 10:04:37', NULL),
(4, 1, 2, 'PROD-004', 'Vacuna Antirrábica', 'Dosis única para perros y gatos', 'producto', 15000.00, 25000.00, 19.00, 50, 5, NULL, NULL, 1, '2026-11-17 23:55:23', '2026-11-18 07:04:19', '2026-11-18 07:04:19'),
(5, 1, 3, 'PROD-005', 'Juguete Kong Clásico Mediano', 'Juguete de caucho resistente para perros', 'producto', 20000.00, 35000.00, 19.00, 4, 5, NULL, NULL, 1, '2026-11-17 23:55:23', '2026-11-23 10:04:37', NULL),
(6, 1, 4, 'PROD-006', 'Shampoo Antipulgas 500ml', 'Shampoo medicado para baño canino', 'producto', 15000.00, 28000.00, 19.00, 21, 5, NULL, NULL, 1, '2026-11-17 23:55:23', '2026-11-20 09:31:16', NULL),
(7, 1, 5, 'SERV-001', 'Consulta Veterinaria General', 'Revisión completa de la mascota', 'servicio', 35000.00, 50000.00, 0.00, 999, 5, NULL, NULL, 1, '2026-11-17 23:55:23', '2026-11-18 07:03:59', NULL),
(8, 1, 6, 'SERV-002', 'Esterilización Canina (Hembra)', 'Procedimiento quirúrgico completo', 'servicio', 0.00, 350000.00, 0.00, 999, 5, NULL, NULL, 1, '2026-11-17 23:55:23', '2026-11-17 23:55:23', NULL),
(9, 1, 7, 'SERV-003', 'Baño y Corte de Raza Pequeña', 'Estética completa para mascotas de talla pequeña', 'paquete', 0.00, 45000.00, 19.00, 211, 5, NULL, NULL, 1, '2026-11-17 23:55:23', '2026-11-18 23:05:33', NULL),
(10, 1, 8, 'PAQ-001', 'Plan Cachorro (Vacunas + Consulta)', 'Incluye 2 consultas, 2 vacunas y desparasitación', 'paquete', 80000.00, 150000.00, 0.00, 999, 5, NULL, NULL, 1, '2026-11-17 23:55:23', '2026-11-17 23:55:23', NULL),
(11, 1, 4, '1032', 'cepillo dental suave', 'cepillo dental cerdas suabes', 'producto', 2400.00, 3500.00, 19.00, 48, 5, 'unidad', NULL, 1, '2026-11-18 07:18:48', '2026-11-24 05:48:53', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

DROP TABLE IF EXISTS `proveedores`;
CREATE TABLE IF NOT EXISTS `proveedores` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(90) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nit` varchar(18) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(18) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ciudad` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contacto_nombre` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contacto_telefono` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `proveedores_tenant_id_foreign` (`tenant_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id`, `tenant_id`, `nombre`, `nit`, `telefono`, `email`, `direccion`, `ciudad`, `contacto_nombre`, `contacto_telefono`, `activo`, `created_at`, `updated_at`) VALUES
(1, 1, 'Distribuidora Veterinaria El Sol', '900123456-1', '6012345678', 'ventas@elsolvet.com', 'Calle 15 # 45 - 20', 'Bogotá', 'Carlos Pérez', '3101234567', 1, '2026-11-17 21:25:51', '2026-11-17 21:25:51'),
(2, 1, 'Bayer Animal Health Colombia', '860014561-2', '6019876543', 'contacto@bayer.com', 'Avenida 68 # 22 - 10', 'Bogotá', 'María Gómez', '3159876543', 1, '2026-11-17 21:25:51', '2026-11-17 21:25:51'),
(3, 1, 'VetPharma Distribuciones', '901234567-3', '6023334444', 'compras@vetpharma.com', 'Carrera 4 # 10 - 50', 'Medellín', 'Juan Rodríguez', '3113334444', 1, '2026-11-17 21:25:51', '2026-11-17 21:25:51'),
(4, 1, 'Agroveterinaria Central', '890987654-4', '6025556666', 'ventas@agrocentral.com', 'Calle 80 # 50 - 30', 'Cali', 'Ana Martínez', '3205556666', 1, '2026-11-17 21:25:51', '2026-11-17 21:25:51'),
(5, 1, 'Zoetis Colombia S.A.S.', '830123456-5', '6017778888', 'pedidos@zoetis.com', 'Autopista Norte # 120 - 15', 'Bogotá', 'Pedro Silva', '3167778888', 1, '2026-11-17 21:25:51', '2026-11-17 21:25:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recetas`
--

DROP TABLE IF EXISTS `recetas`;
CREATE TABLE IF NOT EXISTS `recetas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint UNSIGNED NOT NULL,
  `mascota_id` bigint UNSIGNED NOT NULL,
  `historia_clinica_id` bigint UNSIGNED DEFAULT NULL,
  `veterinario_id` bigint UNSIGNED DEFAULT NULL,
  `fecha_emision` date NOT NULL,
  `indicaciones_generales` text COLLATE utf8mb4_unicode_ci,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recetas_mascota_id_foreign` (`mascota_id`),
  KEY `recetas_historia_clinica_id_foreign` (`historia_clinica_id`),
  KEY `recetas_veterinario_id_foreign` (`veterinario_id`),
  KEY `recetas_tenant_id_index` (`tenant_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `receta_medicamentos`
--

DROP TABLE IF EXISTS `receta_medicamentos`;
CREATE TABLE IF NOT EXISTS `receta_medicamentos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `receta_id` bigint UNSIGNED NOT NULL,
  `medicamento` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dosis` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `frecuencia` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duracion_dias` smallint UNSIGNED DEFAULT NULL,
  `instrucciones` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `receta_medicamentos_receta_id_foreign` (`receta_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reportes`
--

DROP TABLE IF EXISTS `reportes`;
CREATE TABLE IF NOT EXISTS `reportes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `tipo` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_desde` date DEFAULT NULL,
  `fecha_hasta` date DEFAULT NULL,
  `formato` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pantalla',
  `filtros` json DEFAULT NULL,
  `total_registros` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reportes_tenant_id_index` (`tenant_id`),
  KEY `reportes_user_id_index` (`user_id`),
  KEY `reportes_tipo_index` (`tipo`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reportes`
--

INSERT INTO `reportes` (`id`, `tenant_id`, `user_id`, `tipo`, `nombre`, `fecha_desde`, `fecha_hasta`, `formato`, `filtros`, `total_registros`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'productos_vendidos', 'Productos más vendidos', '2026-11-01', '2026-11-23', 'pdf', NULL, 5, '2026-11-24 07:49:46', '2026-11-24 07:49:46'),
(2, 1, 1, 'citas', 'Citas', '2026-08-04', '2026-11-23', 'pdf', NULL, 15, '2026-11-24 08:06:42', '2026-11-24 08:06:42'),
(3, 1, 1, 'citas', 'Citas', '2026-08-04', '2026-11-23', 'csv', NULL, 15, '2026-11-24 08:08:23', '2026-11-24 08:08:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'SuperAdmin', 'web', '2026-10-26 00:19:14', '2026-10-26 00:19:14'),
(2, 'Admin', 'web', '2026-10-26 00:19:14', '2026-10-26 00:19:14'),
(3, 'Veterinario', 'web', '2026-10-26 00:19:14', '2026-10-26 00:19:14'),
(4, 'Recepcionista', 'web', '2026-10-26 00:19:14', '2026-10-26 00:19:14'),
(5, 'Cajero', 'web', '2026-10-26 00:19:14', '2026-10-26 00:19:14'),
(6, 'Administrador', 'web', '2026-11-19 04:22:30', '2026-11-19 04:22:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(3, 1),
(3, 2),
(3, 3),
(3, 4),
(4, 1),
(4, 2),
(4, 3),
(4, 4),
(5, 1),
(5, 2),
(6, 1),
(6, 2),
(6, 3),
(6, 4),
(6, 5),
(7, 1),
(7, 2),
(7, 3),
(7, 4),
(8, 1),
(8, 2),
(8, 3),
(8, 4),
(9, 1),
(9, 2),
(10, 1),
(10, 2),
(10, 3),
(10, 4),
(10, 5),
(11, 1),
(11, 2),
(11, 3),
(11, 4),
(12, 1),
(12, 2),
(12, 3),
(12, 4),
(13, 1),
(13, 2),
(14, 1),
(14, 2),
(14, 3),
(15, 1),
(15, 2),
(15, 3),
(16, 1),
(16, 2),
(16, 3),
(17, 1),
(17, 2),
(18, 1),
(18, 2),
(18, 3),
(18, 4),
(18, 5),
(19, 1),
(19, 2),
(20, 1),
(20, 2),
(21, 1),
(21, 2),
(22, 1),
(22, 2),
(22, 3),
(22, 5),
(23, 1),
(23, 2),
(23, 5),
(24, 1),
(24, 2),
(24, 5),
(25, 1),
(25, 2),
(26, 1),
(26, 2),
(27, 1),
(27, 2),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(31, 2),
(32, 1),
(33, 1),
(34, 1),
(35, 6),
(36, 6),
(37, 6),
(38, 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sedes`
--

DROP TABLE IF EXISTS `sedes`;
CREATE TABLE IF NOT EXISTS `sedes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direccion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `principal` tinyint(1) NOT NULL DEFAULT '0',
  `activa` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sedes_tenant_id_foreign` (`tenant_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sedes`
--

INSERT INTO `sedes` (`id`, `tenant_id`, `nombre`, `direccion`, `principal`, `activa`, `created_at`, `updated_at`) VALUES
(1, 0, 'sede principal', 'cra 45 # 2 - 10', 1, 1, '2026-10-27 03:48:24', NULL),
(2, 1, 'Sede Principal', 'Calle 10 # 25 - 40, Centro', 1, 1, '2026-10-28 06:13:36', '2026-10-28 06:13:36'),
(3, 1, 'Sede Norte', 'Avenida 5 # 12 - 80, Barrio Norte', 0, 1, '2026-10-28 06:13:36', '2026-10-28 06:13:36'),
(4, 1, 'Sede Sur', 'Carrera 15 # 45 - 10, Barrio Sur', 0, 1, '2026-10-28 06:13:36', '2026-10-28 06:13:36');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tenants`
--

DROP TABLE IF EXISTS `tenants`;
CREATE TABLE IF NOT EXISTS `tenants` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre_clinica` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subdominio` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nit` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_contacto` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ciudad` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plan` enum('basico','profesional','premium') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'basico',
  `estado` enum('prueba','activo','suspendido') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'prueba',
  `fecha_inicio_prueba` date DEFAULT NULL,
  `fecha_fin_prueba` date DEFAULT NULL,
  `fecha_vencimiento_plan` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenants_subdominio_unique` (`subdominio`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `especialidad` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `es_super_admin` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_tenant_id_index` (`tenant_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `tenant_id`, `name`, `rol`, `email`, `email_verified_at`, `password`, `telefono`, `foto`, `especialidad`, `activo`, `es_super_admin`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 1, 'heberth mazuera', 'Super Admin', 'heberth.mazuera@gmail.com', NULL, '$2a$12$bLF4RNGmZl.L/cGCc.Pd7uu9/xMNAhXNeSaSqfBdVa6jG9eh4ekUO', '3170189729', NULL, 'Admon', 1, 0, NULL, NULL, NULL),
(2, NULL, 'Dr. Carlos Ramírez', 'veterinarian', 'veterinario@vetcloud.com', NULL, '$2y$10$1SoScz0XBxs320uBvOg5HOhiIZjeyWHF5USoXO78o2q9JDbcHoFPS', '3000000001', NULL, 'Medicina General', 1, 0, NULL, '2026-10-26 00:19:29', '2026-10-26 00:19:29'),
(3, NULL, 'Laura Gómez', 'receptionist', 'recepcionista@vetcloud.com', NULL, '$2y$10$V8enSeWcqXH.6iTnIOFMmeHXu5Ub7iQzSc1lVQDZOn2ik.KRtEx1i', '3000000002', NULL, NULL, 1, 0, NULL, '2026-10-26 00:19:30', '2026-10-26 00:19:30'),
(4, NULL, 'Pedro Martínez', 'cashier', 'cajero@vetcloud.com', NULL, '$2y$10$aSEk/eAX0AUw32dN2VFq.O/PGzd/3pu.GTxcPL6rCNd1kadYxpeR6', '3000000003', NULL, NULL, 1, 0, NULL, '2026-10-26 00:19:31', '2026-10-26 00:19:31'),
(5, 1, 'diana milena soto', 'Recepcionista', 'diana.soto@gmail.com', NULL, '$2y$10$qGY0EsGU82vCGq7U5IkuXebRg8jf5gA6hzWNePApZCttKugYpvzG.', '3124942176', NULL, 'secretaria recepcionista', 1, 0, NULL, '2026-11-24 08:57:13', '2026-11-24 08:58:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vacunas`
--

DROP TABLE IF EXISTS `vacunas`;
CREATE TABLE IF NOT EXISTS `vacunas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint UNSIGNED DEFAULT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `laboratorio` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lote` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `dosis_total` int NOT NULL DEFAULT '1',
  `intervalo_dias` int DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stock` int NOT NULL DEFAULT '0',
  `stock_minimo` int NOT NULL DEFAULT '5',
  `fecha_caducidad` date DEFAULT NULL,
  `via_aplicacion` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'subcutanea',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vacunas_tenant_id_foreign` (`tenant_id`),
  KEY `vacunas_nombre_activo_index` (`nombre`,`activo`),
  KEY `vacunas_fecha_caducidad_index` (`fecha_caducidad`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `vacunas`
--

INSERT INTO `vacunas` (`id`, `tenant_id`, `nombre`, `laboratorio`, `lote`, `descripcion`, `dosis_total`, `intervalo_dias`, `precio`, `stock`, `stock_minimo`, `fecha_caducidad`, `via_aplicacion`, `activo`, `created_at`, `updated_at`) VALUES
(1, 1, 'Vacuna Antirrábica', 'Pfizer', 'LOT-ANT-001', 'Vacuna para la prevención de la rabia en perros y gatos.', 1, 365, 25000.00, 50, 10, '2027-11-11', 'subcutanea', 1, '2026-11-11 07:41:59', '2026-11-11 07:41:59'),
(2, 1, 'Vacuna Parvovirus', 'Boehringer Ingelheim', 'LOT-PAR-002', 'Protección contra parvovirus canino.', 2, 21, 32000.00, 30, 5, '2027-09-11', 'subcutanea', 1, '2026-11-11 07:42:01', '2026-11-11 07:42:01'),
(3, 1, 'Vacuna Moquillo Canino', 'Zoetis', 'LOT-MOQ-003', 'Prevención del moquillo (Distemper).', 2, 21, 30000.00, 25, 5, '2027-07-11', 'subcutanea', 1, '2026-11-11 07:42:01', '2026-11-11 07:42:01'),
(4, 1, 'Triple Felina', 'Virbac', 'LOT-TRI-004', 'Protección contra rinotraqueítis, calicivirus y panleucopenia felina.', 2, 21, 45000.00, 15, 3, '2027-11-11', 'intramuscular', 1, '2026-11-11 07:42:01', '2026-11-11 07:42:01'),
(5, 1, 'Vacuna Leucemia Felina', 'Merial', 'LOT-LEU-005', 'Prevención del virus de la leucemia felina (FeLV).', 2, 21, 38000.00, 20, 5, '2027-05-11', 'subcutanea', 1, '2026-11-11 07:42:01', '2026-11-11 07:42:01'),
(6, 1, 'Vacuna Bordetella', 'Merck', 'LOT-BOR-006', 'Prevención de la tos de las perreras.', 1, 365, 28000.00, 10, 2, '2027-11-11', 'intranasal', 1, '2026-11-11 07:42:01', '2026-11-11 07:42:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vacuna_aplicaciones`
--

DROP TABLE IF EXISTS `vacuna_aplicaciones`;
CREATE TABLE IF NOT EXISTS `vacuna_aplicaciones` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `vacuna_id` bigint UNSIGNED NOT NULL,
  `mascota_id` bigint UNSIGNED NOT NULL,
  `usuario_id` bigint UNSIGNED NOT NULL,
  `fecha_aplicacion` date NOT NULL,
  `fecha_proxima` date DEFAULT NULL,
  `dosis_aplicada` decimal(10,2) NOT NULL DEFAULT '1.00',
  `lote` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `via_aplicacion` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'subcutanea',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `costo` decimal(10,2) NOT NULL DEFAULT '0.00',
  `estado` enum('aplicada','programada','cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aplicada',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vacuna_aplicaciones_usuario_id_foreign` (`usuario_id`),
  KEY `vacuna_aplicaciones_mascota_id_fecha_aplicacion_index` (`mascota_id`,`fecha_aplicacion`),
  KEY `vacuna_aplicaciones_vacuna_id_fecha_aplicacion_index` (`vacuna_id`,`fecha_aplicacion`),
  KEY `vacuna_aplicaciones_fecha_proxima_index` (`fecha_proxima`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `vacuna_aplicaciones`
--

INSERT INTO `vacuna_aplicaciones` (`id`, `vacuna_id`, `mascota_id`, `usuario_id`, `fecha_aplicacion`, `fecha_proxima`, `dosis_aplicada`, `lote`, `via_aplicacion`, `observaciones`, `costo`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 3, '2026-11-11', '2027-02-12', 1.00, '2391', 'subcutanea', 'ninguna', 8600.00, 'cancelada', '2026-11-11 08:00:47', '2026-11-14 00:41:37'),
(3, 1, 3, 3, '2026-11-13', '2027-05-12', 1.00, 'LOT-KLA-3201', 'subcutanea', 'ninguna', 6500.00, 'aplicada', '2026-11-14 00:59:23', '2026-11-14 00:59:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventadetalle`
--

DROP TABLE IF EXISTS `ventadetalle`;
CREATE TABLE IF NOT EXISTS `ventadetalle` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `venta_id` bigint UNSIGNED NOT NULL,
  `producto_id` bigint UNSIGNED DEFAULT NULL,
  `nombre_producto` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio_unitario` decimal(12,2) NOT NULL DEFAULT '0.00',
  `cantidad` decimal(12,2) NOT NULL DEFAULT '1.00',
  `iva_porcentaje` decimal(5,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ventadetalle_venta_id_index` (`venta_id`),
  KEY `ventadetalle_producto_id_index` (`producto_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ventadetalle`
--

INSERT INTO `ventadetalle` (`id`, `venta_id`, `producto_id`, `nombre_producto`, `precio_unitario`, `cantidad`, `iva_porcentaje`, `subtotal`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'Concentrado Gato Adulto 3kg', 45000.00, 1.00, 19.00, 45000.00, '2026-11-20 04:02:09', '2026-11-20 04:02:09'),
(2, 1, 3, 'Desparasitante Bravecto 20-40kg', 65000.00, 2.00, 19.00, 130000.00, '2026-11-20 04:02:10', '2026-11-20 04:02:10'),
(3, 1, 5, 'Juguete Kong Clásico Mediano', 35000.00, 2.00, 19.00, 70000.00, '2026-11-20 04:02:10', '2026-11-20 04:02:10'),
(4, 1, 6, 'Shampoo Antipulgas 500ml', 28000.00, 1.00, 19.00, 28000.00, '2026-11-20 04:02:10', '2026-11-20 04:02:10'),
(5, 2, 2, 'Concentrado Gato Adulto 3kg', 45000.00, 1.00, 19.00, 45000.00, '2026-11-20 04:23:06', '2026-11-20 04:23:06'),
(6, 2, 3, 'Desparasitante Bravecto 20-40kg', 65000.00, 1.00, 19.00, 65000.00, '2026-11-20 04:23:06', '2026-11-20 04:23:06'),
(7, 2, 5, 'Juguete Kong Clásico Mediano', 35000.00, 1.00, 19.00, 35000.00, '2026-11-20 04:23:06', '2026-11-20 04:23:06'),
(8, 2, 6, 'Shampoo Antipulgas 500ml', 28000.00, 1.00, 19.00, 28000.00, '2026-11-20 04:23:06', '2026-11-20 04:23:06'),
(9, 3, 2, 'Concentrado Gato Adulto 3kg', 45000.00, 1.00, 19.00, 45000.00, '2026-11-20 09:31:15', '2026-11-20 09:31:15'),
(10, 3, 3, 'Desparasitante Bravecto 20-40kg', 65000.00, 2.00, 19.00, 130000.00, '2026-11-20 09:31:16', '2026-11-20 09:31:16'),
(11, 3, 5, 'Juguete Kong Clásico Mediano', 35000.00, 3.00, 19.00, 105000.00, '2026-11-20 09:31:16', '2026-11-20 09:31:16'),
(12, 3, 6, 'Shampoo Antipulgas 500ml', 28000.00, 2.00, 19.00, 56000.00, '2026-11-20 09:31:16', '2026-11-20 09:31:16'),
(13, 4, 2, 'Concentrado Gato Adulto 3kg', 45000.00, 1.00, 19.00, 45000.00, '2026-11-23 10:04:36', '2026-11-23 10:04:36'),
(14, 4, 3, 'Desparasitante Bravecto 20-40kg', 65000.00, 1.00, 19.00, 65000.00, '2026-11-23 10:04:37', '2026-11-23 10:04:37'),
(15, 4, 5, 'Juguete Kong Clásico Mediano', 35000.00, 2.00, 19.00, 70000.00, '2026-11-23 10:04:37', '2026-11-23 10:04:37'),
(16, 4, 11, 'cepillo dental', 3500.00, 2.00, 19.00, 7000.00, '2026-11-23 10:04:37', '2026-11-23 10:04:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

DROP TABLE IF EXISTS `ventas`;
CREATE TABLE IF NOT EXISTS `ventas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint UNSIGNED NOT NULL,
  `caja_id` bigint UNSIGNED DEFAULT NULL,
  `consecutivo` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cliente_id` bigint UNSIGNED DEFAULT NULL,
  `mascota_id` bigint UNSIGNED DEFAULT NULL,
  `usurio_id` bigint UNSIGNED NOT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `descuento_porcentaje` decimal(5,2) NOT NULL DEFAULT '0.00',
  `descuento_valor` decimal(12,2) NOT NULL DEFAULT '0.00',
  `iva_valor` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `metodo_pago` enum('efectivo','tarjeta','transferencia','mixto') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'efectivo',
  `referencia_pago` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `detalle_pago` json DEFAULT NULL,
  `monto_efectivo` decimal(12,2) DEFAULT NULL,
  `monto_otro` decimal(12,2) DEFAULT NULL,
  `cambio` decimal(12,2) DEFAULT NULL,
  `estado` enum('pagada','anulada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pagada',
  `observacion` text COLLATE utf8mb4_unicode_ci,
  `factura_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `vendedor_nombre` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ventas_tenant_id_consecutivo_unique` (`tenant_id`,`consecutivo`),
  KEY `ventas_caja_id_foreign` (`caja_id`),
  KEY `ventas_cliente_id_foreign` (`cliente_id`),
  KEY `ventas_mascota_id_foreign` (`mascota_id`),
  KEY `ventas_usurio_id_foreign` (`usurio_id`),
  KEY `ventas_factura_id_foreign` (`factura_id`),
  KEY `ventas_tenant_id_created_at_index` (`tenant_id`,`created_at`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `tenant_id`, `caja_id`, `consecutivo`, `cliente_id`, `mascota_id`, `usurio_id`, `subtotal`, `descuento_porcentaje`, `descuento_valor`, `iva_valor`, `total`, `metodo_pago`, `referencia_pago`, `detalle_pago`, `monto_efectivo`, `monto_otro`, `cambio`, `estado`, `observacion`, `factura_id`, `created_at`, `updated_at`, `vendedor_nombre`) VALUES
(1, 1, 1, 'POS-000001', 3, NULL, 1, 273000.00, 0.00, 0.00, 51870.00, 324870.00, 'efectivo', NULL, '{\"cambio\": 25130, \"monto_recibido\": 350000}', 324870.00, 0.00, 25130.00, 'pagada', NULL, NULL, '2026-11-20 04:02:07', '2026-11-20 04:02:07', 'heberth mazuera'),
(2, 1, 1, 'POS-000002', 2, NULL, 1, 173000.00, 0.00, 0.00, 32870.00, 205870.00, 'efectivo', NULL, '{\"cambio\": 4130, \"monto_recibido\": 210000}', 205870.00, 0.00, 4130.00, 'pagada', NULL, NULL, '2026-11-20 04:23:06', '2026-11-20 04:23:06', 'heberth mazuera'),
(3, 1, 1, 'POS-000003', 1, NULL, 1, 336000.00, 0.00, 0.00, 63840.00, 399840.00, 'efectivo', NULL, '{\"cambio\": 160, \"monto_recibido\": 400000}', 399840.00, 0.00, 160.00, 'pagada', NULL, NULL, '2026-11-20 09:31:15', '2026-11-20 09:31:15', 'heberth mazuera'),
(4, 1, 1, 'POS-000004', 3, NULL, 1, 187000.00, 0.00, 0.00, 35530.00, 222530.00, 'efectivo', NULL, '{\"cambio\": 2470, \"monto_recibido\": 225000}', 222530.00, 0.00, 2470.00, 'pagada', NULL, NULL, '2026-11-23 10:04:34', '2026-11-23 10:04:34', 'heberth mazuera');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_items`
--

DROP TABLE IF EXISTS `venta_items`;
CREATE TABLE IF NOT EXISTS `venta_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `venta_id` bigint UNSIGNED NOT NULL,
  `producto_id` bigint UNSIGNED DEFAULT NULL,
  `nombre_producto` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio_unitario` decimal(12,2) NOT NULL,
  `cantidad` int NOT NULL DEFAULT '1',
  `iva_porcentaje` decimal(5,2) NOT NULL DEFAULT '19.00',
  `subtotal` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `venta_items_venta_id_foreign` (`venta_id`),
  KEY `venta_items_producto_id_foreign` (`producto_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `veterinarios`
--

DROP TABLE IF EXISTS `veterinarios`;
CREATE TABLE IF NOT EXISTS `veterinarios` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `tenant_id` bigint UNSIGNED DEFAULT NULL,
  `cedula` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `celular` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `especialidad` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `veterinarios_cedula_unique` (`cedula`),
  KEY `veterinarios_user_id_foreign` (`user_id`),
  KEY `veterinarios_tenant_id_foreign` (`tenant_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `veterinarios`
--

INSERT INTO `veterinarios` (`id`, `user_id`, `tenant_id`, `cedula`, `nombre`, `apellidos`, `celular`, `email`, `especialidad`, `activo`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, 1, '1020304050', 'Carlos Andrés', 'Gómez Restrepo', '3101112233', 'carlos.gomez@veterinaria.com', 'Medicina Interna', 1, '2026-10-28 00:09:24', '2026-10-28 00:09:24', NULL),
(2, NULL, 1, '1020304051', 'María Fernanda', 'López Martínez', '3202223344', 'maria.lopez@veterinaria.com', 'Cirugía y Ortopedia', 1, '2026-10-28 00:09:24', '2026-10-28 00:09:24', NULL),
(3, NULL, 1, '1020304052', 'Javier', 'Rodríguez Silva', '3153334455', 'javier.rodriguez@veterinaria.com', 'Dermatología', 1, '2026-10-28 00:09:24', '2026-10-28 00:09:24', NULL),
(4, NULL, 1, '1020304053', 'Lorena', 'Pérez Castro', '3114445566', 'lorena.perez@veterinaria.com', 'Oftalmología', 1, '2026-10-28 00:09:24', '2026-10-28 00:09:24', NULL),
(5, NULL, 1, '1020304054', 'Andrés Felipe', 'Ramírez Ospina', '3185556677', 'andres.ramirez@veterinaria.com', 'Odontología', 1, '2026-10-28 00:09:24', '2026-10-28 00:09:24', NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
