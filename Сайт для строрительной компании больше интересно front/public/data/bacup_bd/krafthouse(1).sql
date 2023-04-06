-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Ноя 05 2021 г., 06:46
-- Версия сервера: 5.7.31
-- Версия PHP: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `krafthouse`
--

-- --------------------------------------------------------

--
-- Структура таблицы `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `square` text NOT NULL,
  `calendar` text NOT NULL,
  `layer` text NOT NULL,
  `total` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `jobs`
--

INSERT INTO `jobs` (`id`, `title`, `square`, `calendar`, `layer`, `total`) VALUES
(1, '1-комнатная квартира метро марино', '80м', '4 дня', '20мм', '1581 000 рублей');

-- --------------------------------------------------------

--
-- Структура таблицы `job_photo`
--

DROP TABLE IF EXISTS `job_photo`;
CREATE TABLE IF NOT EXISTS `job_photo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_id` int(11) NOT NULL,
  `src` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `job_id` (`job_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `job_photo`
--

INSERT INTO `job_photo` (`id`, `job_id`, `src`) VALUES
(1, 1, 'пример'),
(2, 1, 'пример_2');

-- --------------------------------------------------------

--
-- Структура таблицы `map`
--

DROP TABLE IF EXISTS `map`;
CREATE TABLE IF NOT EXISTS `map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coordinate_x` double NOT NULL,
  `coordinate_y` double NOT NULL,
  `title` text NOT NULL,
  `img` text,
  `param_one` text,
  `param_two` text,
  `param_three` text,
  `param_four` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `map`
--

INSERT INTO `map` (`id`, `coordinate_x`, `coordinate_y`, `title`, `img`, `param_one`, `param_two`, `param_three`, `param_four`) VALUES
(1, 55.661574, 37.573856, '1-комнатная квартира метро марино', 'пример_min.jpg', 'Площадь: <b> 80м </b>', 'Срок выполнения: <b> 4 дня </b>', 'Средний слой: <b> 20мм </b>', 'Итого: <b> 1581 000 рублей </b>'),
(2, 55.661574, 37.573856, '1-комнатная квартира метро марино', 'пример_min.jpg', 'Площадь: <b> 80м </b>', 'Срок выполнения: <b> 4 дня </b>', 'Средний слой: <b> 20мм </b>', 'Итого: <b> 1581 000 рублей </b>');

-- --------------------------------------------------------

--
-- Структура таблицы `review`
--

DROP TABLE IF EXISTS `review`;
CREATE TABLE IF NOT EXISTS `review` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` text NOT NULL,
  `src` text NOT NULL,
  `title_when` text,
  `title_company` text,
  `rating` int(11) DEFAULT NULL,
  `h3` text NOT NULL,
  `p` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `review`
--

INSERT INTO `review` (`id`, `type`, `src`, `title_when`, `title_company`, `rating`, `h3`, `p`) VALUES
(1, 'video', 'https://www.youtube.com/embed/arbsitYF0rI', NULL, NULL, NULL, 'Быстро и качественно, огромная благодарность всей команде!', 'Новый знак российского рубля, утвержденный 11 декабря 2013 года, представляет собой русскую букву «Р», перечеркнутую посередине горизонтальной чертой. Зачем нужен символ российской валюте.'),
(2, 'video', 'https://www.youtube.com/embed/arbsitYF0rI', NULL, NULL, NULL, 'Быстро и качественно, огромная благодарность всей команде!', 'Новый знак российского рубля, утвержденный 11 декабря 2013 года, представляет собой русскую букву «Р», перечеркнутую посередине горизонтальной чертой. Зачем нужен символ российской валюте.'),
(3, 'text', 'yell.svg', '4 месяца назад', 'Отзыв оставлен на сайте yell.ru', 5, 'Быстро и качественно, огромная благодарность всей команде!', 'Новый знак российского рубля, утвержденный 11 декабря 2013 года, представляет собой русскую букву «Р», перечеркнутую посередине горизонтальной чертой. Зачем нужен символ российской валюте.'),
(4, 'text', 'yell.svg', '4 месяца назад', 'Отзыв оставлен на сайте yell.ru', 5, 'Быстро и качественно, огромная благодарность всей команде!', 'Новый знак российского рубля, утвержденный 11 декабря 2013 года, представляет собой русскую букву «Р», перечеркнутую посередине горизонтальной чертой. Зачем нужен символ российской валюте.');

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `job_photo`
--
ALTER TABLE `job_photo`
  ADD CONSTRAINT `job_photo_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
