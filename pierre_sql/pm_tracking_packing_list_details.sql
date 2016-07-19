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
-- Table structure for table `pm_tracking_packing_list_details`
--

CREATE TABLE IF NOT EXISTS `pm_tracking_packing_list_details` (
`id` int(11) NOT NULL,
  `fk_packing_list_id` int(11) NOT NULL,
  `pcs` float NOT NULL,
  `amount` float NOT NULL,
  `fk_tariff_details_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pm_tracking_packing_list_details`
--

INSERT INTO `pm_tracking_packing_list_details` (`id`, `fk_packing_list_id`, `pcs`, `amount`, `fk_tariff_details_id`) VALUES
(22, 23, 5, 6.15, 43),
(23, 24, 5, 6.15, 43),
(24, 24, 5, 16.1, 44),
(25, 25, 5, 25, 33),
(26, 25, 123, 615, 33);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pm_tracking_packing_list_details`
--
ALTER TABLE `pm_tracking_packing_list_details`
 ADD PRIMARY KEY (`id`), ADD KEY `fk_packing_list_id` (`fk_packing_list_id`), ADD KEY `fk_tariff_details_id` (`fk_tariff_details_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pm_tracking_packing_list_details`
--
ALTER TABLE `pm_tracking_packing_list_details`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=27;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `pm_tracking_packing_list_details`
--
ALTER TABLE `pm_tracking_packing_list_details`
ADD CONSTRAINT `tracking_packing_list_details_fk_packing_list_id	` FOREIGN KEY (`fk_packing_list_id`) REFERENCES `pm_tracking_packing_list` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `tracking_packing_list_details_fk_tariff_details_id` FOREIGN KEY (`fk_tariff_details_id`) REFERENCES `pm_tracking_tariff_details` (`id`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
