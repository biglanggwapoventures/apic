-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Aug 07, 2016 at 04:23 PM
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
-- Table structure for table `pm_trucking_payment`
--

CREATE TABLE `pm_trucking_payment` (
  `id` int(11) NOT NULL,
  `fk_sales_customer_id` int(11) NOT NULL,
  `date` varchar(45) NOT NULL,
  `tracking_number_type` varchar(5) NOT NULL,
  `tracking_number` varchar(45) NOT NULL,
  `remarks` text NOT NULL,
  `status` int(2) NOT NULL DEFAULT '1',
  `is_locked` int(2) NOT NULL DEFAULT '0',
  `or_number` varchar(45) NOT NULL,
  `is_advance_payment` int(2) NOT NULL DEFAULT '0',
  `deposit_date` date DEFAULT NULL,
  `pay_to` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `pay_from` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pm_trucking_payment_check_transaction`
--

CREATE TABLE `pm_trucking_payment_check_transaction` (
  `id` int(11) NOT NULL,
  `fk_trucking_payment_id` int(11) NOT NULL,
  `fk_trucking_payment_detail_id` int(11) NOT NULL,
  `bank_account` varchar(100) NOT NULL,
  `pay_to` int(11) DEFAULT NULL,
  `check_number` varchar(45) NOT NULL,
  `check_date` varchar(45) NOT NULL,
  `deposit_date` varchar(45) NOT NULL,
  `amount` decimal(13,2) NOT NULL DEFAULT '0.00',
  `check_amount` decimal(13,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pm_trucking_payment_detail`
--

CREATE TABLE `pm_trucking_payment_detail` (
  `id` int(11) NOT NULL,
  `fk_trucking_payment_id` int(11) NOT NULL,
  `fk_tracking_packing_list_id` int(11) NOT NULL,
  `payment_method` varchar(5) NOT NULL DEFAULT 'Cash',
  `amount` decimal(13,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pm_trucking_payment`
--
ALTER TABLE `pm_trucking_payment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sales_customer_id` (`fk_sales_customer_id`);

--
-- Indexes for table `pm_trucking_payment_check_transaction`
--
ALTER TABLE `pm_trucking_payment_check_transaction`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_trucking_payment_id` (`fk_trucking_payment_id`),
  ADD KEY `fk_trucking_payment_detail_id` (`fk_trucking_payment_detail_id`);

--
-- Indexes for table `pm_trucking_payment_detail`
--
ALTER TABLE `pm_trucking_payment_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_trucking_payment_id` (`fk_trucking_payment_id`),
  ADD KEY `fk_tracking_packing_list_id` (`fk_tracking_packing_list_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pm_trucking_payment`
--
ALTER TABLE `pm_trucking_payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `pm_trucking_payment_check_transaction`
--
ALTER TABLE `pm_trucking_payment_check_transaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `pm_trucking_payment_detail`
--
ALTER TABLE `pm_trucking_payment_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
