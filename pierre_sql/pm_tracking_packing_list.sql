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
-- Table structure for table `pm_tracking_packing_list`
--

CREATE TABLE IF NOT EXISTS `pm_tracking_packing_list` (
`id` int(11) NOT NULL,
  `fk_sales_customer_id` int(11) NOT NULL,
  `fk_tariff_id` int(11) NOT NULL,
  `fk_trip_ticket_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `approved_by` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pm_tracking_packing_list`
--

INSERT INTO `pm_tracking_packing_list` (`id`, `fk_sales_customer_id`, `fk_tariff_id`, `fk_trip_ticket_id`, `date`, `approved_by`) VALUES
(23, 16, 18, 30, '2016-07-19', 4),
(24, 16, 18, 28, '2016-07-19', 4),
(25, 17, 17, 12, '2016-07-19', 4);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pm_tracking_packing_list`
--
ALTER TABLE `pm_tracking_packing_list`
 ADD PRIMARY KEY (`id`), ADD KEY `fk_sales_customer_id` (`fk_sales_customer_id`), ADD KEY `fk_tariff_id` (`fk_tariff_id`), ADD KEY `fk_trip_ticket_id` (`fk_trip_ticket_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pm_tracking_packing_list`
--
ALTER TABLE `pm_tracking_packing_list`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=26;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `pm_tracking_packing_list`
--
ALTER TABLE `pm_tracking_packing_list`
ADD CONSTRAINT `tracking_packing_list_fk_sales_customer_id` FOREIGN KEY (`fk_sales_customer_id`) REFERENCES `pm_sales_customer` (`id`) ON UPDATE CASCADE,
ADD CONSTRAINT `tracking_packing_list_fk_tariff_id` FOREIGN KEY (`fk_tariff_id`) REFERENCES `pm_tracking_tariff` (`id`) ON UPDATE CASCADE,
ADD CONSTRAINT `tracking_packing_list_fk_trip_ticket_id` FOREIGN KEY (`fk_trip_ticket_id`) REFERENCES `pm_tracking_trip_ticket` (`id`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
