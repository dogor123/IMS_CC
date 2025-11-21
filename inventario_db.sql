-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-11-2025 a las 22:38:43
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
-- Base de datos: `inventario_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `customer_name`, `customer_email`, `total_amount`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(2, 'ORD-20251031-0F0B93', 'ESTEBAN DIAZ VARGAS', 'estebandiaz@cotecnova.edu.co', 1600035.00, 'completed', 'que llegue antes del martes', '2025-10-31 02:53:20', '2025-10-31 04:49:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `unit_price`, `subtotal`) VALUES
(4, 2, 9, 1, 1600000.00, 1600000.00),
(5, 2, 2, 1, 35.00, 35.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `product_type` enum('physical','digital','service') NOT NULL,
  `cost_price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `sku` varchar(100) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `weight` decimal(8,2) DEFAULT NULL COMMENT 'Para productos físicos',
  `download_link` varchar(255) DEFAULT NULL COMMENT 'Para productos digitales',
  `duration_hours` int(11) DEFAULT NULL COMMENT 'Para servicios',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `product_type`, `cost_price`, `sale_price`, `stock`, `sku`, `image_url`, `weight`, `download_link`, `duration_hours`, `created_at`, `updated_at`) VALUES
(1, 'Laptop Dell XPS 13', 'Laptop ultradelgada de alto rendimiento', 'physical', 950000.00, 1600000.00, 14, 'LAP-DELL-001', '', 1.20, '', 1, '2025-10-30 18:12:06', '2025-10-30 20:42:20'),
(2, 'Mouse Inalámbrico Logitech', 'Mouse ergonómico con conectividad Bluetooth', 'physical', 50000.00, 115000.00, 49, 'MOU-LOG-001', '', 0.10, '', 1, '2025-10-30 18:12:06', '2025-10-31 04:46:28'),
(3, 'Teclado Mecánico RGB', 'Teclado gaming con switches mecánicos', 'physical', 45000.00, 89900.00, 29, 'KEY-MEC-001', '', 0.80, '', 1, '2025-10-30 18:12:06', '2025-10-31 04:46:42'),
(4, 'Curso de PHP Avanzado', 'Curso completo de desarrollo web con PHP', 'digital', 20000.00, 97000.00, 999, 'CURSO-PHP-001', '', 0.00, 'https://example.com/curso-php', 1, '2025-10-30 18:12:06', '2025-10-31 04:47:10'),
(5, 'eBook: Patrones de Diseño', 'Libro digital sobre patrones de software', 'digital', 5000.00, 29900.00, 999, 'EBOOK-PAT-001', '', 0.00, 'https://example.com/ebook-patrones', 1, '2025-10-30 18:12:06', '2025-10-31 04:47:22'),
(6, 'Consultoría de Software', 'Asesoría personalizada en desarrollo de software', 'service', 30000.00, 100000.00, 999, 'SERV-CONS-001', '', 0.00, '', 2, '2025-10-30 18:12:06', '2025-10-31 04:47:40'),
(7, 'Soporte Técnico Premium', 'Soporte técnico prioritario 24/7', 'service', 50000.00, 150000.00, 999, 'SERV-SUPP-001', '', 0.00, '', 1, '2025-10-30 18:12:06', '2025-10-31 04:47:57'),
(9, 'Laptop Dell XPS 13 (Copia)', 'Laptop ultradelgada de alto rendimiento', 'physical', 950000.00, 1600000.00, 13, 'LAP-DELL-001-COPY-1CA9', '', 1.20, NULL, NULL, '2025-10-31 02:52:19', '2025-10-31 02:53:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `role` enum('admin','employee') DEFAULT 'employee',
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `role`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(6, 'admin', '$2y$10$YyhDWyVVn9PB9x5VH.P/GuO6ad.86GXx0LiuLqnDxlA1qDE5kvuQm', 'admin@inventario.com', 'Administrador del Sistema', 'admin', 1, '2025-11-01 21:37:29', '2025-10-30 22:21:52', '2025-11-01 21:37:29'),
(7, 'empleado', '$2y$10$YyhDWyVVn9PB9x5VH.P/GuO6ad.86GXx0LiuLqnDxlA1qDE5kvuQm', 'empleado@inventario.com', 'Empleado de Ventas', 'employee', 1, '2025-10-31 04:57:25', '2025-10-30 22:21:52', '2025-10-31 04:57:25'),
(8, 'monica', '$2y$10$RIkS0aiV91bcnQGoMe/kveSuZc/h1VZe5JTFpyiIKGRD.dYFF4BeG', 'monica@cotecnova.edu.co', 'Mónica Alejandra Parra Quintero', 'admin', 1, '2025-11-01 21:35:37', '2025-11-01 21:22:31', '2025-11-01 21:35:37'),
(9, 'saray', '$2y$10$O90qOZXLbn.4fNAivHhtpeqZFMlXQ8SdfbSxvd2FLEAhtIjEAKS0W', 'saray@cotecnova.edu.co', 'Saray Foronda Restrepo', 'admin', 1, '2025-11-01 21:35:47', '2025-11-01 21:23:15', '2025-11-01 21:35:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`);

--
-- Indices de la tabla `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indices de la tabla `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_token` (`session_token`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Filtros para la tabla `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
