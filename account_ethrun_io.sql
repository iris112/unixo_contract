-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Май 29 2020 г., 17:49
-- Версия сервера: 8.0.19
-- Версия PHP: 7.4.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `account.ethrun.io`
--

-- --------------------------------------------------------

--
-- Структура таблицы `languages`
--

CREATE TABLE `languages` (
  `symbol` char(2) NOT NULL COMMENT 'Символ',
  `name` varchar(40) NOT NULL COMMENT 'Название'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Языки';

-- --------------------------------------------------------

--
-- Структура таблицы `levels`
--

CREATE TABLE `levels` (
  `id` int NOT NULL COMMENT '#',
  `price` float NOT NULL COMMENT 'Цена'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Уровни';

--
-- Дамп данных таблицы `levels`
--

INSERT INTO `levels` (`id`, `price`) VALUES
(1, 0.1),
(2, 0.2),
(3, 0.4),
(4, 0.8),
(5, 1.6),
(6, 3.2),
(7, 6.4),
(8, 12.8),
(9, 25.6),
(10, 51.2);

-- --------------------------------------------------------

--
-- Структура таблицы `sk_events`
--

CREATE TABLE `sk_events` (
  `tx` varchar(66) NOT NULL COMMENT 'Транзакция',
  `index` int NOT NULL COMMENT 'Индекс события в блоке',
  `type` enum('regLevelEvent','buyLevelEvent','prolongateLevelEvent','getMoneyForLevelEvent','lostMoneyForLevelEvent') NOT NULL COMMENT 'Тип',
  `user` varchar(42) NOT NULL COMMENT 'Пользователь',
  `ref` varchar(42) NOT NULL COMMENT 'Реферал',
  `level` int NOT NULL COMMENT 'Уровень',
  `time` int NOT NULL COMMENT 'Время'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='События в смартконтракта';

-- --------------------------------------------------------

--
-- Структура таблицы `storage`
--

CREATE TABLE `storage` (
  `key` varchar(255) NOT NULL COMMENT 'Ключ',
  `val` text NOT NULL COMMENT 'Значение',
  `time` int NOT NULL COMMENT 'Время смерти (0 - бесконечно)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Ключ-значение хранилище';

-- --------------------------------------------------------

--
-- Структура таблицы `translations`
--

CREATE TABLE `translations` (
  `lang` char(2) NOT NULL COMMENT 'Язык',
  `key` varchar(1000) NOT NULL COMMENT 'Ключ',
  `val` varchar(1000) NOT NULL COMMENT 'Значение'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Переводы';

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL COMMENT '#',
  `pid` int NOT NULL COMMENT '#  предка',
  `address` varchar(42) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Адрес'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Пользователи';

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `languages`
--
ALTER TABLE `languages`
  ADD UNIQUE KEY `symbol` (`symbol`);

--
-- Индексы таблицы `levels`
--
ALTER TABLE `levels`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `sk_events`
--
ALTER TABLE `sk_events`
  ADD UNIQUE KEY `tx` (`tx`,`index`),
  ADD KEY `user` (`user`,`type`) USING BTREE,
  ADD KEY `ref` (`ref`,`user`) USING BTREE,
  ADD KEY `time` (`time`);

--
-- Индексы таблицы `storage`
--
ALTER TABLE `storage`
  ADD PRIMARY KEY (`key`);

--
-- Индексы таблицы `translations`
--
ALTER TABLE `translations`
  ADD UNIQUE KEY `lang` (`lang`,`key`(255)) USING BTREE;

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `address` (`address`) USING BTREE,
  ADD KEY `pid` (`pid`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `levels`
--
ALTER TABLE `levels`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT '#', AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
