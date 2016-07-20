-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 20, 2016 at 02:27 PM
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
-- Table structure for table `pm_tracking_location`
--

CREATE TABLE IF NOT EXISTS `pm_tracking_location` (
`id` int(11) NOT NULL,
  `name` text NOT NULL,
  `last_updated_by` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pm_tracking_location`
--

INSERT INTO `pm_tracking_location` (`id`, `name`, `last_updated_by`) VALUES
(1, 'Canduman mandaue', 0),
(3, 'Talamban', 4),
(4, 'Sang Karlos', 0),
(5, 'Skina', 0);

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
  `approved_by` int(11) DEFAULT NULL,
  `last_updated_by` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pm_tracking_packing_list_details`
--

CREATE TABLE IF NOT EXISTS `pm_tracking_packing_list_details` (
`id` int(11) NOT NULL,
  `fk_packing_list_id` int(11) NOT NULL,
  `pcs` decimal(13,2) NOT NULL,
  `amount` text NOT NULL,
  `fk_location_id` int(11) NOT NULL,
  `rate` float(12,2) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pm_tracking_tariff`
--

CREATE TABLE IF NOT EXISTS `pm_tracking_tariff` (
`id` int(11) NOT NULL,
  `code` text NOT NULL,
  `option` int(11) NOT NULL COMMENT '1= origin 2=destination',
  `fk_location_id` int(11) NOT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `last_updated_by` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pm_tracking_tariff_details`
--

CREATE TABLE IF NOT EXISTS `pm_tracking_tariff_details` (
`id` int(11) NOT NULL,
  `fk_tariff_id` int(11) NOT NULL,
  `fk_location_id` int(11) NOT NULL,
  `rate` float(11,2) NOT NULL,
  `kms` float(11,2) NOT NULL,
  `deleted_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB AUTO_INCREMENT=142 DEFAULT CHARSET=latin1;

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
  `remarks` text NOT NULL,
  `last_updated_by` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pm_tracking_location`
--
ALTER TABLE `pm_tracking_location`
 ADD PRIMARY KEY (`id`), ADD KEY `id` (`id`);

--
-- Indexes for table `pm_tracking_packing_list`
--
ALTER TABLE `pm_tracking_packing_list`
 ADD PRIMARY KEY (`id`), ADD KEY `fk_sales_customer_id` (`fk_sales_customer_id`), ADD KEY `fk_tariff_id` (`fk_tariff_id`), ADD KEY `fk_trip_ticket_id` (`fk_trip_ticket_id`);

--
-- Indexes for table `pm_tracking_packing_list_details`
--
ALTER TABLE `pm_tracking_packing_list_details`
 ADD PRIMARY KEY (`id`), ADD KEY `fk_packing_list_id` (`fk_packing_list_id`);

--
-- Indexes for table `pm_tracking_tariff`
--
ALTER TABLE `pm_tracking_tariff`
 ADD PRIMARY KEY (`id`), ADD KEY `fk_location_id` (`fk_location_id`), ADD KEY `fk_location_id_2` (`fk_location_id`);

--
-- Indexes for table `pm_tracking_tariff_details`
--
ALTER TABLE `pm_tracking_tariff_details`
 ADD PRIMARY KEY (`id`), ADD KEY `fk_tariff_id` (`fk_tariff_id`), ADD KEY `fk_tariff_location_id` (`fk_location_id`), ADD KEY `fk_location_id` (`fk_location_id`);

--
-- Indexes for table `pm_tracking_trip_ticket`
--
ALTER TABLE `pm_tracking_trip_ticket`
 ADD PRIMARY KEY (`id`), ADD KEY `fk_sales_customer_id` (`fk_sales_customer_id`,`fk_sales_trucking_id`,`fk_trucking_assistant_id`), ADD KEY `fk_sales_trucking_id` (`fk_sales_trucking_id`), ADD KEY `fk_trucking_assistant_id` (`fk_trucking_assistant_id`), ADD KEY `fk_sales_customer_id_2` (`fk_sales_customer_id`), ADD KEY `fk_sales_trucking_id_2` (`fk_sales_trucking_id`), ADD KEY `fk_trucking_assistant_id_2` (`fk_trucking_assistant_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pm_tracking_location`
--
ALTER TABLE `pm_tracking_location`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `pm_tracking_packing_list`
--
ALTER TABLE `pm_tracking_packing_list`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `pm_tracking_packing_list_details`
--
ALTER TABLE `pm_tracking_packing_list_details`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT for table `pm_tracking_tariff`
--
ALTER TABLE `pm_tracking_tariff`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT for table `pm_tracking_tariff_details`
--
ALTER TABLE `pm_tracking_tariff_details`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=142;
--
-- AUTO_INCREMENT for table `pm_tracking_trip_ticket`
--
ALTER TABLE `pm_tracking_trip_ticket`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=31;
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

--
-- Constraints for table `pm_tracking_packing_list_details`
--
ALTER TABLE `pm_tracking_packing_list_details`
ADD CONSTRAINT `tracking_packing_list_details_fk_packing_list_id	` FOREIGN KEY (`fk_packing_list_id`) REFERENCES `pm_tracking_packing_list` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `pm_tracking_tariff`
--
ALTER TABLE `pm_tracking_tariff`
ADD CONSTRAINT `tracking_tariff_fk_location_id	` FOREIGN KEY (`fk_location_id`) REFERENCES `pm_tracking_location` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `pm_tracking_tariff_details`
--
ALTER TABLE `pm_tracking_tariff_details`
ADD CONSTRAINT `tracking_tariff_details_fk_location_id` FOREIGN KEY (`fk_location_id`) REFERENCES `pm_tracking_location` (`id`) ON UPDATE CASCADE,
ADD CONSTRAINT `tracking_tariff_details_fk_tariff_id` FOREIGN KEY (`fk_tariff_id`) REFERENCES `pm_tracking_tariff` (`id`) ON UPDATE CASCADE;

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
