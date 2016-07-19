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
-- Table structure for table `pm_tracking_tariff_details`
--

CREATE TABLE IF NOT EXISTS `pm_tracking_tariff_details` (
`id` int(11) NOT NULL,
  `fk_tariff_id` int(11) NOT NULL,
  `fk_location_id` int(11) NOT NULL,
  `rate` float NOT NULL,
  `kms` float NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pm_tracking_tariff_details`
--

INSERT INTO `pm_tracking_tariff_details` (`id`, `fk_tariff_id`, `fk_location_id`, `rate`, `kms`) VALUES
(4, 8, 3, 3, 4),
(31, 17, 5, 1, 2),
(32, 17, 4, 3, 4),
(33, 17, 3, 5, 6),
(35, 15, 1, 12, 21),
(39, 16, 1, 123, 443),
(40, 16, 4, 2323, 123),
(41, 16, 3, 23213, 213123),
(43, 18, 5, 1.23, 0.23),
(44, 18, 3, 3.22, 3.22);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pm_tracking_tariff_details`
--
ALTER TABLE `pm_tracking_tariff_details`
 ADD PRIMARY KEY (`id`), ADD KEY `fk_tariff_id` (`fk_tariff_id`), ADD KEY `fk_tariff_location_id` (`fk_location_id`), ADD KEY `fk_location_id` (`fk_location_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pm_tracking_tariff_details`
--
ALTER TABLE `pm_tracking_tariff_details`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=45;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `pm_tracking_tariff_details`
--
ALTER TABLE `pm_tracking_tariff_details`
ADD CONSTRAINT `tracking_tariff_details_fk_location_id` FOREIGN KEY (`fk_location_id`) REFERENCES `pm_tracking_location` (`id`) ON UPDATE CASCADE,
ADD CONSTRAINT `tracking_tariff_details_fk_tariff_id` FOREIGN KEY (`fk_tariff_id`) REFERENCES `pm_tracking_tariff` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
