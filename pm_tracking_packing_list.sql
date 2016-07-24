-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 24, 2016 at 10:23 AM
-- Server version: 10.1.13-MariaDB
-- PHP Version: 5.5.37

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `apic`
--

-- --------------------------------------------------------

--
-- Table structure for table `pm_tracking_packing_list`
--

CREATE TABLE `pm_tracking_packing_list` (
  `id` int(11) NOT NULL,
  `fk_sales_customer_id` int(11) NOT NULL,
  `fk_tariff_id` int(11) NOT NULL,
  `fk_trip_ticket_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `other_charges` decimal(13,2) NOT NULL,
  `adjustments` decimal(13,2) NOT NULL,
  `net_amount` decimal(13,2) NOT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `last_updated_by` int(11) NOT NULL,
  `km_reading_start` float(11,2) NOT NULL,
  `km_reading_end` float(11,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pm_tracking_packing_list`
--
ALTER TABLE `pm_tracking_packing_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sales_customer_id` (`fk_sales_customer_id`),
  ADD KEY `fk_tariff_id` (`fk_tariff_id`),
  ADD KEY `fk_trip_ticket_id` (`fk_trip_ticket_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pm_tracking_packing_list`
--
ALTER TABLE `pm_tracking_packing_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
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
