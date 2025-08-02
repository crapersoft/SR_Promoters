-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 12, 2025 at 04:51 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `emi`
--

-- --------------------------------------------------------

--
-- Table structure for table `agent`
--

CREATE TABLE `agent` (
  `agent_id` int(11) NOT NULL,
  `agent_name` varchar(300) NOT NULL,
  `mobile_number` bigint(20) NOT NULL,
  `emil_id` varchar(300) NOT NULL,
  `agent_address` text NOT NULL,
  `agent_aadhar_no` varchar(100) NOT NULL,
  `created_at` date NOT NULL DEFAULT current_timestamp(),
  `status` varchar(100) NOT NULL DEFAULT 'active',
  `adding_date` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agent`
--

INSERT INTO `agent` (`agent_id`, `agent_name`, `mobile_number`, `emil_id`, `agent_address`, `agent_aadhar_no`, `created_at`, `status`, `adding_date`) VALUES
(1, 'raja', 9994316533, 'raja@gmail', 'gbcdfgnf', '986568014954', '2024-12-31', 'active', '2024-12-31'),
(2, 'Mahesh', 9600780140, 'raja@gmail', 'bgfmxnhfzhntgf', '986568014954', '2024-12-31', 'active', '2024-12-31'),
(3, 'Owner', 8608380149, 'Rajusamiya8@gmail.com', 'fhbazfg', '364531894616', '2024-12-31', 'active', '2024-12-31'),
(4, 'gopal', 8144714777, 'raja@gmail', ' bnZDGNzF]', '986568014954', '2024-12-31', 'active', '2024-12-31'),
(5, 'Rajendran ', 9843080847, 'raja@gmail', 'hfzbfsxngf', '986568014954', '2024-12-31', 'active', '2024-12-31'),
(6, 'Samugam', 9500376160, 'raja@gmail', 'mgdzjryf,fd', '986568014954', '2024-12-31', 'active', '2024-12-31'),
(7, 'Saravanan', 9943627119, 'raja@gmail', ',jgmdj,gchmghc', '986568014954', '2024-12-31', 'active', '2024-12-31');

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `bookingid` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL,
  `Buying_Sqft` decimal(15,2) NOT NULL,
  `pricePerSquareFeet` decimal(15,2) NOT NULL,
  `totalBuyingPrice` decimal(15,2) NOT NULL,
  `buyingDate` date NOT NULL,
  `agent_id` int(11) NOT NULL,
  `persantage` decimal(10,2) NOT NULL,
  `persantageAmount` decimal(15,2) NOT NULL,
  `addingDate` date NOT NULL DEFAULT current_timestamp(),
  `isEmi` tinyint(1) NOT NULL DEFAULT 0,
  `emi_id` int(11) NOT NULL,
  `siteno` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `emis`
--

CREATE TABLE `emis` (
  `emi_ids` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL,
  `emi_plan` text NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date NOT NULL,
  `total_payable` bigint(20) NOT NULL,
  `monthly_payable` bigint(20) NOT NULL,
  `paid_amount` bigint(20) NOT NULL,
  `balance_amount` bigint(20) NOT NULL,
  `nextDueDate` date DEFAULT NULL,
  `agentCommission` double(10,2) NOT NULL,
  `agentCommissionAmount` double(15,2) NOT NULL,
  `payment_count` int(11) NOT NULL,
  `total_payment_commissions` double(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fullpayments`
--

CREATE TABLE `fullpayments` (
  `fp_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL,
  `payment_amount` decimal(15,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` enum('cash','card','upi','net_banking') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `login_id` int(11) NOT NULL,
  `userName` varchar(200) NOT NULL,
  `password` text NOT NULL,
  `role` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`login_id`, `userName`, `password`, `role`) VALUES
(1, 'user', '$2y$10$8uD4c3NlGwsLGS3CJBeXf.wmeGfDVrUbJR2YBlCG7BGyGyYiExvNe', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `emi_id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `penalty` decimal(10,2) DEFAULT 0.00,
  `totalAmountAndPenalty` bigint(20) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) NOT NULL,
  `balance_amount` decimal(10,2) NOT NULL,
  `payment_mode` enum('cash','card','upi','net_banking') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `agent_commison` double(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `site`
--

CREATE TABLE `site` (
  `id` int(11) NOT NULL,
  `site_name` varchar(255) NOT NULL,
  `total_sqft` decimal(10,2) NOT NULL,
  `price_per_sqft` decimal(10,2) NOT NULL,
  `total_price_sqft` decimal(15,2) NOT NULL,
  `site_address` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `bookedSqft` decimal(10,2) NOT NULL,
  `avaliableSqft` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site`
--

INSERT INTO `site` (`id`, `site_name`, `total_sqft`, `price_per_sqft`, `total_price_sqft`, `site_address`, `created_at`, `bookedSqft`, `avaliableSqft`) VALUES
(1, 'Maharaja Nagar', 60000.00, 399.00, 23940000.00, 'artgnhrg', '2024-12-31 12:31:33', 0.00, 60000.00),
(2, 'VelayuthaSwamy Nagar', 60000.00, 407.00, 24420000.00, 'hgvfjygv', '2024-12-31 12:35:53', 0.00, 60000.00);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `userName` varchar(200) NOT NULL,
  `phoneNumber` varchar(20) NOT NULL,
  `email` varchar(250) NOT NULL,
  `address` text NOT NULL,
  `aadharNumber` varchar(50) NOT NULL,
  `created_at` date NOT NULL DEFAULT current_timestamp(),
  `status` varchar(100) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `persantage` double(15,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `userName`, `phoneNumber`, `email`, `address`, `aadharNumber`, `created_at`, `status`, `agent_id`, `persantage`) VALUES
(3, 'P Chinnasamy -Druga', '6381702105', 'sample@gmail.com', 'Vavipalayam-Guruvayurappan Nagar', 'SDBztr561496', '2024-12-31', 'active', 1, 13.900),
(4, 'karan', '9865680149', 'rajusamiya08@gmail.com', 'zgnfrftnhz', 'SDBztr561496', '2024-12-31', 'active', 3, 13.900);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agent`
--
ALTER TABLE `agent`
  ADD PRIMARY KEY (`agent_id`);

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`bookingid`);

--
-- Indexes for table `emis`
--
ALTER TABLE `emis`
  ADD PRIMARY KEY (`emi_ids`);

--
-- Indexes for table `fullpayments`
--
ALTER TABLE `fullpayments`
  ADD PRIMARY KEY (`fp_id`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`login_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site`
--
ALTER TABLE `site`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `agent`
--
ALTER TABLE `agent`
  MODIFY `agent_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `bookingid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emis`
--
ALTER TABLE `emis`
  MODIFY `emi_ids` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fullpayments`
--
ALTER TABLE `fullpayments`
  MODIFY `fp_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `login_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `site`
--
ALTER TABLE `site`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
