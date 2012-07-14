-- phpMyAdmin SQL Dump
-- version 4.0.0-dev
-- http://www.phpmyadmin.net
--
-- Host: nxstudio.pl
-- Generation Time: Jul 14, 2012 at 06:15 AM
-- Server version: 5.1.62-log
-- PHP Version: 5.3.10-1ubuntu3.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `transys`
--

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE IF NOT EXISTS `client` (
  `client_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_type` enum('sender','receiver') COLLATE utf8_polish_ci DEFAULT NULL,
  `client_name` varchar(100) COLLATE utf8_polish_ci NOT NULL,
  `client_street` varchar(100) COLLATE utf8_polish_ci NOT NULL,
  `client_city` varchar(45) COLLATE utf8_polish_ci NOT NULL,
  `client_postal` varchar(6) COLLATE utf8_polish_ci NOT NULL,
  `client_email` varchar(100) COLLATE utf8_polish_ci DEFAULT NULL,
  `client_phone` varchar(20) COLLATE utf8_polish_ci DEFAULT NULL,
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=19 ;

--
-- Dumping data for table `client`
--

INSERT IGNORE INTO `client` (`client_id`, `client_type`, `client_name`, `client_street`, `client_city`, `client_postal`, `client_email`, `client_phone`) VALUES
(5, NULL, 'Natalia Roguś', 'Jarocka 77b/38', 'Olsztyn', '10-293', 'rogvc49@gmail.com', '513677678'),
(6, NULL, 'Adrian Piotrowicz', 'Jagiellończyka 8c/8', 'Olsztyn', '10-062', 'nexces@nxstudio.pl', '508217550'),
(7, NULL, 'Natalia Roguś', 'Jarocka 77b/38', 'Olsztyn', '10-690', 'rogvc49@gmail.com', ''),
(8, NULL, 'Adrian Piotrowicz', 'Jagiellończyka 8c/8', 'Olsztyn', '10-062', 'nexces@nxstudio.pl', '+48508217550'),
(9, NULL, 'Jakub Pastuszek', '1 Alana House, The Stiles Road, Clontarf', 'DUBLIN', '00-001', 'jpastuszek@gmail.com', ''),
(10, NULL, 'Zenon Brok', 'sda', 'fdsa', '12-213', 'zbrok@gmail.com', ''),
(11, NULL, 'Mister frędzel', 'Pokątna 5', 'miastowe', '00-547', '', ''),
(12, NULL, 'Adrian Piotrowicz', 'Jagiellończyka 8c/8', 'Olsztyn', '10-062', 'nexces@nxstudio.pl', '+48508217550'),
(13, NULL, 'Chrystian Bober', 'dfsa', 'sfda', '12-321', 'hb@bc.com', 'dfas'),
(14, NULL, 'Romuald Warzny', 'dfa', 'fdsa', '11-234', 'mw@wp.pl', ''),
(15, NULL, 'Hello Kitty', 'afsd', 'fds', '12-666', 'hk@disnay.com', ''),
(16, NULL, 'Rusałka Roman', 'dfsa', 'afds', '99-123', 'rr@dj.com', ''),
(17, NULL, 'Daba Diba', 'afsd', 'afs', '12-311', 'duda@gaba.pl', ''),
(18, NULL, 'Rufus Ił', 'dfas', 'afsd', '12-123', 'r.il@abc.eu', '');

-- --------------------------------------------------------

--
-- Table structure for table `package`
--

CREATE TABLE IF NOT EXISTS `package` (
  `package_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `package_tracking_code` varchar(10) COLLATE utf8_polish_ci NOT NULL,
  `sender_id` int(10) unsigned NOT NULL,
  `receiver_id` int(10) unsigned NOT NULL,
  `package_weight` float unsigned NOT NULL,
  `package_width` float unsigned NOT NULL,
  `package_height` float unsigned NOT NULL,
  `package_depth` float unsigned NOT NULL,
  `package_payment_method` varchar(45) COLLATE utf8_polish_ci NOT NULL,
  `courier_pick_id` int(10) unsigned DEFAULT NULL,
  `courier_deliver_id` int(10) unsigned DEFAULT NULL,
  `package_payment_received` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`package_id`),
  UNIQUE KEY `package_tracking_code_UNIQUE` (`package_tracking_code`),
  KEY `fk_package_sender` (`sender_id`),
  KEY `fk_package_receiver` (`receiver_id`),
  KEY `fk_package_user1` (`courier_pick_id`),
  KEY `fk_package_user2` (`courier_deliver_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=10 ;

--
-- Dumping data for table `package`
--

INSERT IGNORE INTO `package` (`package_id`, `package_tracking_code`, `sender_id`, `receiver_id`, `package_weight`, `package_width`, `package_height`, `package_depth`, `package_payment_method`, `courier_pick_id`, `courier_deliver_id`, `package_payment_received`) VALUES
(3, '3415354253', 6, 5, 53, 2, 3, 4, 'pick', 1, 2, 0),
(4, '3415358573', 8, 7, 20, 2, 11, 4, 'instant', 2, 1, 1),
(5, '3415486106', 10, 9, 3, 12, 11, 11, 'instant', 2, 1, 1),
(6, '3415486452', 12, 11, 14, 6, 2, 3, 'instant', NULL, NULL, 0),
(7, '3415503251', 14, 13, 2, 2, 2, 2, 'instant', NULL, NULL, 1),
(8, '3415504656', 16, 15, 1, 1, 1, 1, 'deliver', NULL, NULL, 0),
(9, '3415506256', 18, 17, 1, 1, 1, 1, 'instant', 1, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `package_log`
--

CREATE TABLE IF NOT EXISTS `package_log` (
  `package_log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `package_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `package_log_type` varchar(45) COLLATE utf8_polish_ci DEFAULT NULL,
  `package_log_time` int(10) unsigned NOT NULL,
  `package_log_info` text COLLATE utf8_polish_ci,
  PRIMARY KEY (`package_log_id`),
  KEY `fk_package_log_user1` (`user_id`),
  KEY `fk_package_log_package1` (`package_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=35 ;

--
-- Dumping data for table `package_log`
--

INSERT IGNORE INTO `package_log` (`package_log_id`, `package_id`, `user_id`, `package_log_type`, `package_log_time`, `package_log_info`) VALUES
(3, 3, NULL, 'created', 1342101603, NULL),
(4, 4, NULL, 'created', 1342104780, NULL),
(5, 4, 1, 'pick_error', 1342115111, 'Mieszkanie zamknięte'),
(10, 3, 3, 'picked', 1342175835, 'terefere'),
(11, 3, 3, 'picked_transit', 1342176913, 'brum brum'),
(12, 3, 3, 'warehouse', 1342177010, ''),
(13, 3, 3, 'deliver_transit', 1342177173, ''),
(14, 3, 3, 'delivered', 1342177185, 'bosko :D'),
(15, 4, 3, 'picked', 1342178954, ''),
(16, 4, 3, 'picked_transit', 1342179246, ''),
(17, 5, NULL, 'created', 1342181670, NULL),
(18, 6, NULL, 'created', 1342181954, NULL),
(19, 5, 4, 'picked_transit', 1342181979, 'Paczke jest różowa!'),
(20, 5, 4, 'warehouse', 1342181996, ''),
(21, 5, 4, 'deliver_transit', 1342182027, ''),
(22, 5, 4, 'delivered', 1342182044, ''),
(23, 7, NULL, 'created', 1342186165, NULL),
(24, 8, NULL, 'created', 1342187223, NULL),
(25, 9, NULL, 'created', 1342188357, NULL),
(26, 9, 4, 'picked_transit', 1342188428, ''),
(27, 9, 4, 'picked', 1342188447, ''),
(28, 9, 4, 'pick_error', 1342188454, ''),
(29, 9, 4, 'warehouse', 1342188463, ''),
(30, 9, 4, 'deliver_transit', 1342188471, ''),
(31, 9, 4, 'other_error', 1342188501, ''),
(32, 9, 4, 'other_error', 1342188517, 'Nie moge zaleźć adresu.'),
(33, 9, 4, 'delivered', 1342188523, ''),
(34, 8, 4, 'other_error', 1342204898, 'Żaden kurier nie chce tej paczki!!!!!!!!!!!!');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_login` varchar(45) COLLATE utf8_polish_ci NOT NULL,
  `user_password` varchar(40) COLLATE utf8_polish_ci NOT NULL,
  `user_name` varchar(45) COLLATE utf8_polish_ci NOT NULL,
  `user_type` varchar(45) COLLATE utf8_polish_ci NOT NULL,
  `user_email` varchar(45) COLLATE utf8_polish_ci DEFAULT NULL,
  `user_phone` varchar(45) COLLATE utf8_polish_ci DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `user`
--

INSERT IGNORE INTO `user` (`user_id`, `user_login`, `user_password`, `user_name`, `user_type`, `user_email`, `user_phone`) VALUES
(1, 'kurier1', '947adaf9557557994990ab3531df6298c163c45f', 'Franek Kimono', 'courier', NULL, NULL),
(2, 'kurier2', 'eaff17ceceeabeb471c84a721b537acb1dd56a9b', 'Janko Walski', 'courier', NULL, NULL),
(3, 'nexces', '794319694794daf61f80157d0990f12388ac53b4', 'Adrian Piotrowicz', 'admin', NULL, NULL),
(4, 'admin', '74913f5cd5f61ec0bcfdb775414c2fb3d161b620', 'Superaśny administrator', 'admin', NULL, NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `package`
--
ALTER TABLE `package`
  ADD CONSTRAINT `fk_package_sender` FOREIGN KEY (`sender_id`) REFERENCES `client` (`client_id`),
  ADD CONSTRAINT `fk_package_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `client` (`client_id`),
  ADD CONSTRAINT `fk_package_user1` FOREIGN KEY (`courier_pick_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_package_user2` FOREIGN KEY (`courier_deliver_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `package_log`
--
ALTER TABLE `package_log`
  ADD CONSTRAINT `fk_package_log_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `fk_package_log_package1` FOREIGN KEY (`package_id`) REFERENCES `package` (`package_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
