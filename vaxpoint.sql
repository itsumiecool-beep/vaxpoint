-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 02, 2026 at 09:50 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vaxpoint`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `password`, `email`) VALUES
(1, 'Umer', 'kkmdmkah', 'itsissa900@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `booking_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `child_id` int(11) DEFAULT NULL,
  `hospital_id` int(11) DEFAULT NULL,
  `vaccine_id` int(11) DEFAULT NULL,
  `booking_date` date DEFAULT NULL,
  `appointment_time` time DEFAULT NULL,
  `status` varchar(30) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `child`
--

CREATE TABLE `child` (
  `child_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `child_name` varchar(100) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `blood_group` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `child`
--

INSERT INTO `child` (`child_id`, `parent_id`, `child_name`, `gender`, `date_of_birth`, `blood_group`) VALUES
(3, 1, 'zain', 'Male', '2018-12-12', 'A'),
(4, 3, 'Ahtisham', 'Male', '2000-12-16', 'b'),
(5, 5, 'xyz', 'Male', '2025-12-21', 'b');

-- --------------------------------------------------------

--
-- Table structure for table `hospital`
--

CREATE TABLE `hospital` (
  `hospital_id` int(11) NOT NULL,
  `hospital_name` varchar(150) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hospital`
--

INSERT INTO `hospital` (`hospital_id`, `hospital_name`, `email`, `password`, `phone`, `address`, `location`, `status`) VALUES
(1, 'The cambridge Hospital', 'itsissa900@gmail.com', '$2y$10$SqAPK5aMrQDDx/VlOks6Ue1a04cO4YyriPSisdycgBscwIPmTx4nq', NULL, '123 street', 'Karachi', 'Active'),
(2, 'Abbasi', 'test@gmail.com', '$2y$10$955zakpeIKlHH8fHw9znXehv1d.G/yZQhASxXqv21syaLYVvY.fBC', NULL, 'New Nazimabad', 'nazimabad', 'Active'),
(3, 'Hamdard Hospital', 'testingg@gmail.com', '$2y$10$gZUcaihGrtvo2s5xSCoPpuL2P0FtAQgp25bL115Eq7J.6DR74KR1W', NULL, 'abccc', 'abc', 'Active'),
(5, 'ABCdef', 'ABCTEST@gmail.com', '$2y$10$L/uj9PbU2kzi5i7YVBoAp.BLRFC9Z5h/Ddj5fXaujjQMOuqqHJJ7.', NULL, 'ABCD', 'ABC', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `hospital_vaccine`
--

CREATE TABLE `hospital_vaccine` (
  `hospital_vaccine_id` int(11) NOT NULL,
  `hospital_id` int(11) NOT NULL,
  `vaccine_id` int(11) NOT NULL,
  `availability_status` enum('Available','Unavailable') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hospital_vaccine`
--

INSERT INTO `hospital_vaccine` (`hospital_vaccine_id`, `hospital_id`, `vaccine_id`, `availability_status`) VALUES
(1, 1, 1, 'Available'),
(2, 1, 2, 'Available'),
(3, 1, 3, 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `parent`
--

CREATE TABLE `parent` (
  `parent_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_pic` varchar(255) DEFAULT 'avatar.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parent`
--

INSERT INTO `parent` (`parent_id`, `name`, `email`, `password`, `phone`, `address`, `created_at`, `profile_pic`) VALUES
(1, 'umer Khan', 'itsissa900@gmail.com', '$2y$10$0GNrYPmy9cxOkOtf8.3g.udB.vJdaOYvPkbHFOnjkz42/274PFYRa', '03448222758', 'A/134,northnazimabad', '2025-12-27 08:43:10', 'avatar.png'),
(2, 'Zain khan', 'itsumie.cool@gmail.com', '$2y$10$vQ55SDweFh1IB3/Gj4xt7.PVUrcOpXx9UCQ08s/uC3yTg9UgvQwgC', '03667544329', 'nazimabad', '2025-12-27 08:50:10', 'avatar.png'),
(3, 'Tanzeel khan', 'test@gmail.com', '$2y$10$26Nc224u4Kp2BoG1tFapnOhn3vgwN.EK55HlGpoSUyE87zXUWJ9Sq', '0339849022', '123 street', '2025-12-29 10:26:14', 'avatar.png'),
(4, 'Tania shah', 'tania@gmail.com', '$2y$10$2c1I.ETDQ9qVjPHZsyKYkO4tQeYCqFmjkCVjP3OElXtUSIcybitS.', '0345676544', 'abc', '2025-12-30 07:11:48', 'avatar.png'),
(5, 'ABC', 'ABC@gmail.com', '$2y$10$uzOppS0zoSlkneG5xz35KOmp9PqnW9eApCYL6SHXxiqYkDvytrH6e', '0333333333333', 'ABC', '2025-12-31 10:14:05', 'avatar.png');

-- --------------------------------------------------------

--
-- Table structure for table `request`
--

CREATE TABLE `request` (
  `request_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `vaccine_id` int(11) NOT NULL,
  `hospital_id` int(11) NOT NULL,
  `requested_date` date NOT NULL,
  `requested_time` time DEFAULT NULL,
  `request_status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `request`
--

INSERT INTO `request` (`request_id`, `parent_id`, `child_id`, `vaccine_id`, `hospital_id`, `requested_date`, `requested_time`, `request_status`, `created_at`) VALUES
(1, 1, 3, 1, 1, '2025-12-31', NULL, 'Pending', '2025-12-31 13:23:11'),
(2, 1, 3, 1, 1, '2025-12-31', NULL, 'Pending', '2025-12-31 13:25:34'),
(3, 1, 3, 1, 1, '2025-12-31', NULL, 'Pending', '2025-12-31 13:26:20'),
(4, 1, 3, 2, 2, '2026-01-01', '18:30:00', 'Pending', '2025-12-31 13:27:02');

-- --------------------------------------------------------

--
-- Table structure for table `vaccination_report`
--

CREATE TABLE `vaccination_report` (
  `report_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `vaccination_date` date DEFAULT NULL,
  `status` varchar(30) DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vaccination_schedule`
--

CREATE TABLE `vaccination_schedule` (
  `schedule_id` int(11) NOT NULL,
  `child_id` int(11) DEFAULT NULL,
  `vaccine_id` int(11) DEFAULT NULL,
  `scheduled_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vaccine`
--

CREATE TABLE `vaccine` (
  `vaccine_id` int(11) NOT NULL,
  `vaccine_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `availability` varchar(20) DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vaccine`
--

INSERT INTO `vaccine` (`vaccine_id`, `vaccine_name`, `description`, `availability`) VALUES
(1, 'COVID-19', 'for all ages', 'Available'),
(2, 'dangue', 'suitable for all ages', 'Available'),
(3, 'ABC', 'ABC', 'Available');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `child_id` (`child_id`),
  ADD KEY `hospital_id` (`hospital_id`),
  ADD KEY `vaccine_id` (`vaccine_id`);

--
-- Indexes for table `child`
--
ALTER TABLE `child`
  ADD PRIMARY KEY (`child_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `hospital`
--
ALTER TABLE `hospital`
  ADD PRIMARY KEY (`hospital_id`);

--
-- Indexes for table `hospital_vaccine`
--
ALTER TABLE `hospital_vaccine`
  ADD PRIMARY KEY (`hospital_vaccine_id`),
  ADD KEY `hospital_id` (`hospital_id`),
  ADD KEY `vaccine_id` (`vaccine_id`);

--
-- Indexes for table `parent`
--
ALTER TABLE `parent`
  ADD PRIMARY KEY (`parent_id`);

--
-- Indexes for table `request`
--
ALTER TABLE `request`
  ADD PRIMARY KEY (`request_id`);

--
-- Indexes for table `vaccination_report`
--
ALTER TABLE `vaccination_report`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `vaccination_schedule`
--
ALTER TABLE `vaccination_schedule`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `child_id` (`child_id`),
  ADD KEY `vaccine_id` (`vaccine_id`);

--
-- Indexes for table `vaccine`
--
ALTER TABLE `vaccine`
  ADD PRIMARY KEY (`vaccine_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `child`
--
ALTER TABLE `child`
  MODIFY `child_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `hospital`
--
ALTER TABLE `hospital`
  MODIFY `hospital_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `hospital_vaccine`
--
ALTER TABLE `hospital_vaccine`
  MODIFY `hospital_vaccine_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `parent`
--
ALTER TABLE `parent`
  MODIFY `parent_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `request`
--
ALTER TABLE `request`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `vaccination_report`
--
ALTER TABLE `vaccination_report`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vaccination_schedule`
--
ALTER TABLE `vaccination_schedule`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vaccine`
--
ALTER TABLE `vaccine`
  MODIFY `vaccine_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `parent` (`parent_id`),
  ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`child_id`) REFERENCES `child` (`child_id`),
  ADD CONSTRAINT `booking_ibfk_3` FOREIGN KEY (`hospital_id`) REFERENCES `hospital` (`hospital_id`),
  ADD CONSTRAINT `booking_ibfk_4` FOREIGN KEY (`vaccine_id`) REFERENCES `vaccine` (`vaccine_id`);

--
-- Constraints for table `child`
--
ALTER TABLE `child`
  ADD CONSTRAINT `child_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `parent` (`parent_id`);

--
-- Constraints for table `hospital_vaccine`
--
ALTER TABLE `hospital_vaccine`
  ADD CONSTRAINT `hospital_vaccine_ibfk_1` FOREIGN KEY (`hospital_id`) REFERENCES `hospital` (`hospital_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_vaccine_ibfk_2` FOREIGN KEY (`vaccine_id`) REFERENCES `vaccine` (`vaccine_id`) ON DELETE CASCADE;

--
-- Constraints for table `vaccination_report`
--
ALTER TABLE `vaccination_report`
  ADD CONSTRAINT `vaccination_report_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`booking_id`);

--
-- Constraints for table `vaccination_schedule`
--
ALTER TABLE `vaccination_schedule`
  ADD CONSTRAINT `vaccination_schedule_ibfk_1` FOREIGN KEY (`child_id`) REFERENCES `child` (`child_id`),
  ADD CONSTRAINT `vaccination_schedule_ibfk_2` FOREIGN KEY (`vaccine_id`) REFERENCES `vaccine` (`vaccine_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
