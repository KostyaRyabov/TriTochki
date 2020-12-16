-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Время создания: Дек 16 2020 г., 20:13
-- Версия сервера: 5.7.31-log
-- Версия PHP: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `tritochki`
--

-- --------------------------------------------------------

--
-- Структура таблицы `chat`
--

CREATE TABLE `chat` (
  `id_chat` int(6) NOT NULL COMMENT 'id чата',
  `id_user` int(6) NOT NULL COMMENT 'Создатель',
  `Name` varchar(20) CHARACTER SET utf8 NOT NULL COMMENT 'Название чата',
  `date_create` datetime NOT NULL COMMENT 'дата основания'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Структура таблицы `chat_knocked`
--

CREATE TABLE `chat_knocked` (
  `id_chat` int(100) NOT NULL,
  `id_user` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Структура таблицы `chat_users`
--

CREATE TABLE `chat_users` (
  `id_chat` int(6) NOT NULL COMMENT 'id чата',
  `id_user` int(6) NOT NULL COMMENT 'id пользователя, состоящего в чате'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Структура таблицы `message`
--

CREATE TABLE `message` (
  `id_message` int(10) NOT NULL COMMENT 'id сообщения',
  `id_chat` int(6) NOT NULL COMMENT 'id чата',
  `id_user` int(6) NOT NULL COMMENT 'id пользователя ',
  `content` varchar(1000) CHARACTER SET utf8 NOT NULL COMMENT 'содержимое сообщения',
  `data_create` datetime NOT NULL COMMENT 'дата добавления сообщения'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user` (
  `id_user` int(6) NOT NULL COMMENT 'id пользователя ',
  `Firstname` varchar(255) NOT NULL COMMENT 'Имя',
  `Lastname` varchar(255) NOT NULL COMMENT 'Фамилия ',
  `Login` varchar(255) NOT NULL COMMENT 'Логин',
  `Password` varchar(255) NOT NULL COMMENT 'Пароль',
  `Email` varchar(255) NOT NULL,
  `Sex` varchar(7) NOT NULL COMMENT 'Пол',
  `restore_code` varchar(255) DEFAULT NULL COMMENT 'Код для восстановления пароля'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `user_contacts`
--

CREATE TABLE `user_contacts` (
  `id` int(100) NOT NULL,
  `id_user` int(100) NOT NULL,
  `id_contact` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id_chat`),
  ADD KEY `id_user` (`id_user`);

--
-- Индексы таблицы `chat_knocked`
--
ALTER TABLE `chat_knocked`
  ADD KEY `id_chat` (`id_chat`),
  ADD KEY `id_user` (`id_user`);

--
-- Индексы таблицы `chat_users`
--
ALTER TABLE `chat_users`
  ADD KEY `id_chat` (`id_chat`),
  ADD KEY `id_user` (`id_user`);

--
-- Индексы таблицы `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id_message`),
  ADD KEY `id_chat` (`id_chat`),
  ADD KEY `id_user` (`id_user`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `Login` (`Login`);

--
-- Индексы таблицы `user_contacts`
--
ALTER TABLE `user_contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_contact` (`id_contact`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `chat`
--
ALTER TABLE `chat`
  MODIFY `id_chat` int(6) NOT NULL AUTO_INCREMENT COMMENT 'id чата';

--
-- AUTO_INCREMENT для таблицы `message`
--
ALTER TABLE `message`
  MODIFY `id_message` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id сообщения';

--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(6) NOT NULL AUTO_INCREMENT COMMENT 'id пользователя ';

--
-- AUTO_INCREMENT для таблицы `user_contacts`
--
ALTER TABLE `user_contacts`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `chat`
--
ALTER TABLE `chat`
  ADD CONSTRAINT `chat_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Ограничения внешнего ключа таблицы `chat_users`
--
ALTER TABLE `chat_users`
  ADD CONSTRAINT `chat_users_ibfk_1` FOREIGN KEY (`id_chat`) REFERENCES `chat` (`id_chat`);

--
-- Ограничения внешнего ключа таблицы `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`id_chat`) REFERENCES `chat` (`id_chat`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
