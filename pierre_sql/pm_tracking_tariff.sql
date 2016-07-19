-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 19, 2016 at 02:35 PM
-- Server version: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `apic`
--

-- --------------------------------------------------------

--
-- Table structure for table `pm_tracking_tariff`
--

CREATE TABLE IF NOT EXISTS `pm_tracking_tariff` (
`id` int(11) NOT NULL,
  `code` text NOT NULL,
  `option` int(11) NOT NULL COMMENT '1= origin 2=destination',
  `fk_location_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pm_tracking_tariff`
--

INSERT INTO `pm_tracking_tariff` (`id`, `code`, `option`, `fk_location_id`) VALUES
(8, '2323', 1, 1),
(15, 'EZ322', 2, 4),
(16, '1234', 2, 5),
(17, 'QWER', 1, 1),
(18, '322', 1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pm_tracking_tariff`
--
ALTER TABLE `pm_tracking_tariff`
 ADD PRIMARY KEY (`id`), ADD KEY `fk_location_id` (`fk_location_id`), ADD KEY `fk_location_id_2` (`fk_location_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pm_tracking_tariff`
--
ALTER TABLE `pm_tracking_tariff`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=19;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `pm_tracking_tariff`
--
ALTER TABLE `pm_tracking_tariff`
ADD CONSTRAINT `tracking_tariff_fk_location_id	` FOREIGN KEY (`fk_location_id`) REFERENCES `pm_tracking_location` (`id`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
