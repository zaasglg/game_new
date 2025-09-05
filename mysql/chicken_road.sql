-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost:8889
-- Время создания: Сен 04 2025 г., 04:15
-- Версия сервера: 8.0.35
-- Версия PHP: 8.2.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `chicken_road`
--

-- --------------------------------------------------------

--
-- Структура таблицы `bets`
--

CREATE TABLE `bets` (
  `id` int NOT NULL,
  `user` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_german2_ci DEFAULT NULL,
  `sid` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_german2_ci DEFAULT NULL,
  `bet` decimal(10,2) DEFAULT '0.00',
  `cf` decimal(10,2) DEFAULT '0.00',
  `result` decimal(10,2) DEFAULT '0.00',
  `game` int DEFAULT '0',
  `type` enum('auto','manual') CHARACTER SET utf8mb3 COLLATE utf8mb3_german2_ci DEFAULT 'auto',
  `src` int DEFAULT '1',
  `status` int DEFAULT '2',
  `date` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_german2_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `cfs`
--

CREATE TABLE `cfs` (
  `id` int NOT NULL,
  `value` decimal(10,2) DEFAULT '1.00',
  `amount` decimal(10,2) DEFAULT '1.00',
  `group` int DEFAULT '0',
  `status` int DEFAULT '1',
  `date` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_german2_ci;

--
-- Дамп данных таблицы `cfs`
--

INSERT INTO `cfs` (`id`, `value`, `amount`, `group`, `status`, `date`) VALUES
(39, 1.03, 1.00, 1, 1, '2025-09-03 22:34:50'),
(40, 1.07, 1.00, 1, 1, '2025-09-03 22:34:50'),
(41, 1.12, 1.00, 1, 1, '2025-09-03 22:34:50'),
(42, 1.17, 1.00, 1, 1, '2025-09-03 22:34:50'),
(43, 1.23, 1.00, 1, 1, '2025-09-03 22:34:50'),
(44, 1.29, 1.00, 1, 1, '2025-09-03 22:34:50'),
(45, 1.36, 1.00, 1, 1, '2025-09-03 22:34:50'),
(46, 1.44, 1.00, 1, 1, '2025-09-03 22:34:50'),
(47, 1.53, 1.00, 1, 1, '2025-09-03 22:34:50'),
(48, 1.63, 1.00, 1, 1, '2025-09-03 22:34:50'),
(49, 1.12, 1.00, 2, 1, '2025-09-03 22:34:50'),
(50, 1.28, 1.00, 2, 1, '2025-09-03 22:34:50'),
(51, 1.47, 1.00, 2, 1, '2025-09-03 22:34:50'),
(52, 1.70, 1.00, 2, 1, '2025-09-03 22:34:50'),
(53, 1.98, 1.00, 2, 1, '2025-09-03 22:34:50'),
(54, 2.33, 1.00, 2, 1, '2025-09-03 22:34:50'),
(55, 2.76, 1.00, 2, 1, '2025-09-03 22:34:50'),
(56, 3.32, 1.00, 2, 1, '2025-09-03 22:34:50'),
(57, 4.03, 1.00, 2, 1, '2025-09-03 22:34:50'),
(58, 4.96, 1.00, 2, 1, '2025-09-03 22:34:50'),
(59, 1.23, 1.00, 3, 1, '2025-09-03 22:34:50'),
(60, 1.55, 1.00, 3, 1, '2025-09-03 22:34:50'),
(61, 1.98, 1.00, 3, 1, '2025-09-03 22:34:50'),
(62, 2.56, 1.00, 3, 1, '2025-09-03 22:34:50'),
(63, 3.36, 1.00, 3, 1, '2025-09-03 22:34:50'),
(64, 4.49, 1.00, 3, 1, '2025-09-03 22:34:50'),
(65, 5.49, 1.00, 3, 1, '2025-09-03 22:34:50'),
(66, 7.53, 1.00, 3, 1, '2025-09-03 22:34:50'),
(67, 10.56, 1.00, 3, 1, '2025-09-03 22:34:50'),
(68, 15.21, 1.00, 3, 1, '2025-09-03 22:34:50'),
(69, 1.63, 1.00, 4, 1, '2025-09-03 22:34:50'),
(70, 2.80, 1.00, 4, 1, '2025-09-03 22:34:50'),
(71, 4.95, 1.00, 4, 1, '2025-09-03 22:34:50'),
(72, 9.08, 1.00, 4, 1, '2025-09-03 22:34:50'),
(73, 15.21, 1.00, 4, 1, '2025-09-03 22:34:50'),
(74, 30.12, 1.00, 4, 1, '2025-09-03 22:34:50'),
(75, 62.96, 1.00, 4, 1, '2025-09-03 22:34:50'),
(76, 140.24, 1.00, 4, 1, '2025-09-03 22:34:50'),
(77, 337.19, 1.00, 4, 1, '2025-09-03 22:34:50'),
(78, 890.19, 1.00, 4, 1, '2025-09-03 22:34:50');

-- --------------------------------------------------------

--
-- Структура таблицы `games`
--

CREATE TABLE `games` (
  `id` int NOT NULL,
  `cf` int DEFAULT '1',
  `status` int DEFAULT '1',
  `finish` datetime DEFAULT NULL,
  `date` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_german2_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `uid` varchar(8) CHARACTER SET utf8mb3 COLLATE utf8mb3_german2_ci DEFAULT NULL,
  `host_id` int NOT NULL DEFAULT '0',
  `name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_german2_ci,
  `real_name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_german2_ci NOT NULL,
  `img` text CHARACTER SET utf8mb3 COLLATE utf8mb3_german2_ci,
  `balance` decimal(10,2) DEFAULT '500.00',
  `status` int DEFAULT NULL,
  `date` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_german2_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `uid`, `host_id`, `name`, `real_name`, `img`, `balance`, `status`, `date`) VALUES
(13340, 'demo123', 0, 'Demo', 'Demo Player', '1', 1000.00, 1, '2025-09-03 08:46:10'),
(13341, '49563c75', 0, '4...7', '', '14', 500.00, 2, '2025-09-03 08:51:10'),
(13342, '441c5e8e', 0, '4...8', '', '35', 500.00, 2, '2025-09-03 08:51:16'),
(13343, '34019fbd', 0, '3...b', '', '48', 500.00, 2, '2025-09-03 08:53:53'),
(13344, '63c1717a', 12770156, 't...m', 'taptap44', '3', 2758308.00, 2, '2025-09-03 08:55:01'),
(13345, 'f616e204', 0, 'f...0', '', '38', 500.00, 2, '2025-09-03 17:10:51'),
(13346, '153ff4ab', 0, '1...a', '', '50', 500.00, 2, '2025-09-03 17:12:00'),
(13347, '64822a42', 0, '6...4', '', '1', 500.00, 2, '2025-09-03 17:18:15'),
(13348, 'd3c3fb3d', 0, 'd...3', '', '65', 500.00, 2, '2025-09-03 22:07:08'),
(13349, '1668ebfa', 0, '1...f', '', '62', 500.00, 2, '2025-09-03 22:24:53'),
(13350, 'fa204049', 0, 'f...4', '', '53', 500.00, 2, '2025-09-03 22:28:09'),
(13351, 'b00d81c2', 0, 'b...c', '', '38', 500.00, 2, '2025-09-03 22:35:28'),
(13352, 'a605f3e3', 0, 'a...e', '', '68', 500.00, 2, '2025-09-03 22:37:29'),
(13353, 'b6b731fe', 0, 'b...f', '', '19', 500.00, 2, '2025-09-03 22:38:23'),
(13354, 'e68fb020', 0, 'e...2', '', '38', 500.00, 2, '2025-09-03 22:38:40'),
(13355, '2677158d', 0, '2...8', '', '23', 500.00, 2, '2025-09-04 00:50:57'),
(13356, '95424c02', 0, '9...0', '', '57', 500.00, 2, '2025-09-04 00:52:26'),
(13357, '8139ec25', 0, '8...2', '', '33', 500.00, 2, '2025-09-04 00:52:58'),
(13358, '38c2223a', 0, '3...3', '', '40', 500.00, 2, '2025-09-04 00:55:10'),
(13359, '8c81a53f', 0, '8...3', '', '60', 500.00, 2, '2025-09-04 00:55:37'),
(13360, 'b8c1a6f3', 0, 'b...f', '', '48', 500.00, 2, '2025-09-04 00:56:41'),
(13361, 'b73b2c44', 0, 'b...4', '', '42', 500.00, 2, '2025-09-04 01:00:20'),
(13362, 'be03ca94', 0, 'b...9', '', '26', 500.00, 2, '2025-09-04 01:01:50'),
(13363, '9dbf8a4a', 0, '9...4', '', '61', 500.00, 2, '2025-09-04 01:03:13'),
(13364, 'f42046b7', 0, 'f...b', '', '66', 500.00, 2, '2025-09-04 01:05:09'),
(13365, '31a18733', 0, '3...3', '', '15', 500.00, 2, '2025-09-04 01:06:29'),
(13366, '53d95665', 0, '5...6', '', '34', 500.00, 2, '2025-09-04 01:07:17'),
(13367, '98aaafbd', 0, '9...b', '', '27', 500.00, 2, '2025-09-04 01:07:42'),
(13368, '0fc1cf44', 0, '0...4', '', '42', 500.00, 2, '2025-09-04 01:08:19'),
(13369, 'e55a04b3', 0, 'e...b', '', '51', 500.00, 2, '2025-09-04 01:10:05');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `bets`
--
ALTER TABLE `bets`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `cfs`
--
ALTER TABLE `cfs`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `bets`
--
ALTER TABLE `bets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113799;

--
-- AUTO_INCREMENT для таблицы `cfs`
--
ALTER TABLE `cfs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT для таблицы `games`
--
ALTER TABLE `games`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13370;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
