-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: db:3306
-- Generation Time: Oct 31, 2022 at 02:38 PM
-- Server version: 8.0.31
-- PHP Version: 8.0.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `lamp_trivia_game`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `name` varchar(16) NOT NULL,
  `username` varchar(16) NOT NULL,
  `password` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

INSERT INTO `users` (`user_id`, `name`, `username`, `password`) VALUES
(1, 'Julian', 'julian', 'julianpass');

--
-- Table structure for table `guesses`
--

CREATE TABLE `guesses` (
  `guess_id` int NOT NULL,
  `user_id` int NOT NULL,
  `date` varchar(16) NOT NULL DEFAULT 'CURDATE()',
  `correct` tinyint(1) NOT NULL,
  CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

ALTER TABLE `guesses`
  ADD PRIMARY KEY (`guess_id`);

ALTER TABLE `guesses`
  MODIFY `guess_id` int NOT NULL AUTO_INCREMENT;
COMMIT;

INSERT INTO `guesses` (`guess_id`, `user_id`, `date`, `correct`) VALUES (NULL, '1', 'CURDATE()', '0');

INSERT INTO `guesses` (`guess_id`, `user_id`, `date`, `correct`) VALUES (NULL, '1', 'CURDATE()', '1');