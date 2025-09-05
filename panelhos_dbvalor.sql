-- phpMyAdmin SQL Dump
-- version 5.2.1-1.el8
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Май 19 2025 г., 23:47
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
  `método_de_pago` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `historial`
--

INSERT INTO `historial` (`id`, `user_id`, `transacciones_data`, `transacciones_monto`, `estado`, `transacción_number`, `método_de_pago`) VALUES
(6, '47167', '2025-05-19 23:42:18', 2000.00, 'esperando', '№412083036', 'Banco de Argentina'),
(7, '47167', '2025-05-19 23:45:31', 5000.00, 'esperando', '№667351644', 'Banco de Argentina');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `deposit` decimal(10,2) DEFAULT '0.00',
  `country` varchar(255) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `cumpleaños` date DEFAULT NULL,
  `sexo` enum('masculino','femenino') DEFAULT NULL,
  `dirección` varchar(255) DEFAULT NULL,
  `número_de_teléfono` varchar(20) DEFAULT NULL,
  `bonificaciones` decimal(10,2) DEFAULT '0.00',
  `registration_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('active','banned','pending') DEFAULT 'active',
  `positions_mine` varchar(255) DEFAULT '10,15,20'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `user_id`, `email`, `password`, `deposit`, `country`, `nombre`, `apellido`, `cumpleaños`, `sexo`, `dirección`, `número_de_teléfono`, `bonificaciones`, `registration_date`, `status`, `positions_mine`) VALUES
(1, '77777', 'admin', 'admin', 131810.00, 'Argentina', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, '2025-05-18 17:39:56', 'active', NULL),
(784, '47167', 'test@gmail.com', '111', 10000.00, 'Argentina', 'Neyton', 'Diaz', '2001-02-20', 'femenino', 'Dirección', '(123) 4567-89', 10.00, '2025-05-18 17:44:22', 'active', '2,8,15'),
(785, '37782', 'xerchman@mail.ru', '1234', 0.00, 'Argentina', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, '2025-05-19 19:02:21', 'active', '8,11,16'),
(786, '09080', 'xerch@mai.ru', 'geqri6-kypraJ-mitsid', 0.00, 'Argentina', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, '2025-05-19 19:08:24', 'active', '10,15,20'),
(787, '17576', 'xbxbx@mai.ru', 'fejfa4-diwwyz-pofbaZ', 0.00, 'Colombia', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, '2025-05-19 19:09:37', 'active', '10,15,20'),
(788, '75754', 'dndj@mail.ru', 'soxqik-Myfxe8-gugxyr', 0.00, 'Ecuador', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, '2025-05-19 19:10:34', 'active', '10,15,20'),
(789, '09813', 'orudjm77@gmail.com', 'Bibi23', 0.00, 'Argentina', '7777', '777', NULL, 'masculino', NULL, '(485) 4646-464', 0.00, '2025-05-19 19:16:59', 'active', '10,15,20'),
(790, '42639', 'hbgff@mail.ru', 'вовтв', 0.00, 'Colombia', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, '2025-05-19 19:21:05', 'active', '10,15,20'),
(791, '44280', 'privet@gmail.com', 'privet', 0.00, 'Argentina', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, '2025-05-19 19:25:25', 'active', '15,20,22'),
(792, '32990', 'prostofilya@gmail.ru', '12345678', 0.00, 'Argentina', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, '2025-05-19 19:32:02', 'active', '4,10,25'),
(793, '13301', 'manager1@latsteam.com', '123123', 0.00, 'Argentina', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, '2025-05-19 19:33:05', 'active', '2,12,18');

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
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `historial`
--
ALTER TABLE `historial`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=794;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
