-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Sep 15, 2015 at 09:40 AM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `iderly`
--
CREATE DATABASE IF NOT EXISTS `iderly` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `iderly`;

-- --------------------------------------------------------

--
-- Table structure for table `caregiver`
--

DROP TABLE IF EXISTS `caregiver`;
CREATE TABLE IF NOT EXISTS `caregiver` (
  `user_id` int(64) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Truncate table before insert `caregiver`
--

TRUNCATE TABLE `caregiver`;
--
-- Triggers `caregiver`
--
DROP TRIGGER IF EXISTS `caregiver_on_insert`;
DELIMITER //
CREATE TRIGGER `caregiver_on_insert` BEFORE INSERT ON `caregiver`
 FOR EACH ROW IF NEW.date_created = 0 THEN SET NEW.date_created = NOW(); END IF
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `game_result`
--

DROP TABLE IF EXISTS `game_result`;
CREATE TABLE IF NOT EXISTS `game_result` (
  `id` int(64) NOT NULL AUTO_INCREMENT,
  `time_start` datetime NOT NULL,
  `time_end` datetime NOT NULL,
  `score` int(64) NOT NULL DEFAULT '0',
  `user_id` int(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Truncate table before insert `game_result`
--

TRUNCATE TABLE `game_result`;
--
-- Triggers `game_result`
--
DROP TRIGGER IF EXISTS `game_result_on_insert`;
DELIMITER //
CREATE TRIGGER `game_result_on_insert` BEFORE INSERT ON `game_result`
 FOR EACH ROW BEGIN

IF NEW.time_end = 0 THEN
  SET NEW.time_end = NOW();
END IF;

IF NEW.time_start = 0 THEN
  SET NEW.time_start = NOW();
END IF;

END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `photo`
--

DROP TABLE IF EXISTS `photo`;
CREATE TABLE IF NOT EXISTS `photo` (
  `id` int(64) NOT NULL AUTO_INCREMENT,
  `attachment` mediumtext NOT NULL COMMENT 'max 16MB',
  `user_id` int(64) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Truncate table before insert `photo`
--

TRUNCATE TABLE `photo`;
--
-- Triggers `photo`
--
DROP TRIGGER IF EXISTS `photo_on_insert`;
DELIMITER //
CREATE TRIGGER `photo_on_insert` BEFORE INSERT ON `photo`
 FOR EACH ROW IF NEW.date_created = 0 THEN SET NEW.date_created = NOW(); END IF
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(64) NOT NULL AUTO_INCREMENT,
  `device_id` varchar(255) NOT NULL,
  `date_created` datetime NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `device_id` (`device_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Truncate table before insert `user`
--

TRUNCATE TABLE `user`;
--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `device_id`, `date_created`, `name`) VALUES
(1, '12345', '2015-09-15 15:14:23', ''),
(2, '123456', '0000-00-00 00:00:00', ''),
(3, '1234321', '2015-09-15 15:29:40', '');

--
-- Triggers `user`
--
DROP TRIGGER IF EXISTS `user_on_insert`;
DELIMITER //
CREATE TRIGGER `user_on_insert` BEFORE INSERT ON `user`
 FOR EACH ROW IF NEW.date_created = 0 THEN SET NEW.date_created = NOW(); END IF
//
DELIMITER ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `caregiver`
--
ALTER TABLE `caregiver`
  ADD CONSTRAINT `caregiver_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `game_result`
--
ALTER TABLE `game_result`
  ADD CONSTRAINT `game_result_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `photo`
--
ALTER TABLE `photo`
  ADD CONSTRAINT `photo_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
