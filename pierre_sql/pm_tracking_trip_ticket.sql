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
-- Table structure for table `pm_tracking_trip_ticket`
--

CREATE TABLE IF NOT EXISTS `pm_tracking_trip_ticket` (
`id` int(11) NOT NULL,
  `fk_sales_customer_id` int(11) NOT NULL,
  `fk_sales_trucking_id` int(11) NOT NULL,
  `fk_trucking_assistant_id` int(11) unsigned NOT NULL,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `trip_type` int(11) NOT NULL COMMENT '1=Chick Van 2=Havester 3=Dressed Chicken',
  `approved_by` int(11) DEFAULT NULL,
  `remarks` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pm_tracking_trip_ticket`
--

INSERT INTO `pm_tracking_trip_ticket` (`id`, `fk_sales_customer_id`, `fk_sales_trucking_id`, `fk_trucking_assistant_id`, `date`, `trip_type`, `approved_by`, `remarks`) VALUES
(10, 17, 5, 4, '2016-07-04', 3, 4, ''),
(11, 16, 5, 3, '2016-07-15', 2, 4, ''),
(12, 17, 5, 4, '2016-07-16', 1, 4, ''),
(13, 84, 4, 3, '2016-07-09', 1, 4, 'TESTETST'),
(14, 130, 6, 2, '2016-07-17', 3, 4, 'asdasd'),
(28, 16, 5, 4, '2016-07-17', 1, 4, 'qwertyui'),
(29, 16, 5, 4, '2016-07-17', 1, 4, 'sdafghjkl;'''),
(30, 16, 5, 4, '2016-07-19', 1, 4, 'heyhey');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pm_tracking_trip_ticket`
--
ALTER TABLE `pm_tracking_trip_ticket`
 ADD PRIMARY KEY (`id`), ADD KEY `fk_sales_customer_id` (`fk_sales_customer_id`,`fk_sales_trucking_id`,`fk_trucking_assistant_id`), ADD KEY `fk_sales_trucking_id` (`fk_sales_trucking_id`), ADD KEY `fk_trucking_assistant_id` (`fk_trucking_assistant_id`), ADD KEY `fk_sales_customer_id_2` (`fk_sales_customer_id`), ADD KEY `fk_sales_trucking_id_2` (`fk_sales_trucking_id`), ADD KEY `fk_trucking_assistant_id_2` (`fk_trucking_assistant_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pm_tracking_trip_ticket`
--
ALTER TABLE `pm_tracking_trip_ticket`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=31;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `pm_tracking_trip_ticket`
--
ALTER TABLE `pm_tracking_trip_ticket`
ADD CONSTRAINT `tracking_trip_ticket_fk_sales_customer_id` FOREIGN KEY (`fk_sales_customer_id`) REFERENCES `pm_sales_customer` (`id`) ON UPDATE CASCADE,
ADD CONSTRAINT `tracking_trip_ticket_fk_sales_trucking_id` FOREIGN KEY (`fk_sales_trucking_id`) REFERENCES `pm_sales_trucking` (`id`) ON UPDATE CASCADE,
ADD CONSTRAINT `tracking_trip_ticket_fk_trucking_assistant_id` FOREIGN KEY (`fk_trucking_assistant_id`) REFERENCES `pm_trucking_assistants` (`id`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
