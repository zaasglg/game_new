-- phpMyAdmin SQL Dump
-- version 5.2.1-1.el8
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Май 31 2025 г., 22:40
-- Версия сервера: 8.0.36-cll-lve
-- Версия PHP: 7.2.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `panelhos_dbvalor`
--

-- --------------------------------------------------------

--
-- Структура таблицы `historial`
--

CREATE TABLE `historial` (
  `id` int NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `transacciones_data` datetime NOT NULL,
  `transacciones_monto` decimal(15,2) NOT NULL,
  `estado` varchar(20) DEFAULT 'esperando',
  `transacción_number` varchar(20) DEFAULT NULL,
  `método_de_pago` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'Transferencia bancaria',
  `amount_usd` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `historial`
--

INSERT INTO `historial` (`id`, `user_id`, `transacciones_data`, `transacciones_monto`, `estado`, `transacción_number`, `método_de_pago`, `amount_usd`, `created_at`) VALUES
(23, '47167', '2025-05-20 16:39:19', 2000.00, 'esperando', '№217666232', 'Banco de Argentina', NULL, '2025-05-31 14:23:50'),
(25, '47167', '2025-05-20 16:49:37', 500.00, 'esperando', '№305004859', 'Banco de Argentina', NULL, '2025-05-31 14:23:50'),
(26, '47167', '2025-05-20 16:53:30', 2000.00, 'esperando', '№763608003', 'Banco de Argentina', NULL, '2025-05-31 14:23:50'),
(40, '66160', '2025-05-20 22:55:18', 2000.00, 'esperando', '№467678089', 'Banco de Argentina', NULL, '2025-05-31 14:23:50'),
(41, '3333', '2025-05-21 02:05:59', 4000.00, 'esperando', '№878995502', 'Unknown', NULL, '2025-05-31 14:23:50'),
(42, '39274', '2025-05-21 02:54:31', 10.00, 'esperando', '39274', 'Unknown', NULL, '2025-05-31 14:23:50'),
(45, '39274', '2025-05-21 03:07:23', 10.00, 'esperando', '№722185687', NULL, NULL, '2025-05-31 14:23:50'),
(46, '39274', '2025-05-21 03:23:36', 1000.00, 'esperando', '№400586849', 'Unknown', NULL, '2025-05-31 14:23:50'),
(47, '16546', '2025-05-21 03:25:11', 5000.00, 'esperando', '№118127321', 'Unknown', NULL, '2025-05-31 14:23:50'),
(48, '16546', '2025-05-21 03:29:11', 500.00, 'esperando', '№913406476', 'Unknown', NULL, '2025-05-31 14:23:50'),
(49, '16546', '2025-05-21 03:30:46', 5000.00, 'completed', '№665387590', 'Transferencia bancaria', NULL, '2025-05-31 14:23:50');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `historial`
--
ALTER TABLE `historial`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transacción_number` (`transacción_number`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `transacciones_data` (`transacciones_data`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `historial`
--
ALTER TABLE `historial`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;