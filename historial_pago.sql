-- phpMyAdmin SQL Dump
-- version 5.2.1-1.el8
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Июн 07 2025 г., 14:00
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
-- Структура таблицы `historial_pagos`
--

CREATE TABLE `historial_pagos` (
  `id` int NOT NULL,
  `user_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `transacciones_data` datetime NOT NULL,
  `transacciones_monto` decimal(15,2) NOT NULL,
  `estado` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'esperando',
  `transacción_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `método_de_pago` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'Transferencia bancaria',
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `cuenta_corriente` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `numero_de_cuenta` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `tipo_de_documento` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `numero_documento` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `banco` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `historial_pagos`
--

INSERT INTO `historial_pagos` (`id`, `user_id`, `transacciones_data`, `transacciones_monto`, `estado`, `transacción_number`, `método_de_pago`, `phone`, `cuenta_corriente`, `numero_de_cuenta`, `tipo_de_documento`, `numero_documento`, `banco`) VALUES
(14, '47167', '2025-06-01 03:17:23', 111.00, 'declined', '№618177357', 'Transferencia bancaria', '2', '', '', 'd', '', 'd'),
(15, '74190', '2025-06-02 17:06:11', 10000.00, 'declined', '№141654724', 'Transferencia bancaria', '0958891616', '', '', 'Cédula ', '', 'Banco de Pichincha'),
(16, '8090335', '2025-06-02 19:05:15', 10000.00, 'declined', '№340812349', 'Transferencia bancaria', '0988476450', '', '', 'Cédula ', '', 'Banco Pichincha '),
(17, '8090335', '2025-06-02 19:27:52', 10.00, 'declined', '№507962951', 'Transferencia bancaria', '0988476450', '', '', 'Cédula ', '', 'Banco Pichincha '),
(18, '8090335', '2025-06-02 19:46:56', 5000.00, 'declined', '№498539509', 'Transferencia bancaria', '0988476450', '', '', 'Cédula ', '', 'Banco Pichincha '),
(19, '8090335', '2025-06-02 19:56:15', 10.00, 'declined', '№689530316', 'Transferencia bancaria', '0988476450', '', '', 'Cédula ', '', 'Banco Pichincha '),
(20, '8090335', '2025-06-02 19:57:07', 10000.00, 'declined', '№277172010', 'Transferencia bancaria', '0988476450', '', '', 'Cédula ', '', 'Banco Pichincha ');
--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `historial_pagos`
--
ALTER TABLE `historial_pagos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transacción_number` (`transacción_number`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `transacciones_data` (`transacciones_data`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `historial_pagos`
--
ALTER TABLE `historial_pagos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;