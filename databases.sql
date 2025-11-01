-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 31, 2025 at 03:57 PM
-- Server version: 8.0.40
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `company_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$60l8qq0zJIsBAQd9AdOL2O0gNZDtduT//bMr/oIw9Ng3WPvUHml6a');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
CREATE TABLE IF NOT EXISTS `attendance` (
  `id` int NOT NULL AUTO_INCREMENT,
  `employee_id` int NOT NULL,
  `date` date NOT NULL,
  `check_in_time` time DEFAULT NULL,
  `check_out_time` time DEFAULT NULL,
  `total_worked` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_employee_date` (`employee_id`,`date`)
) ENGINE=MyISAM AUTO_INCREMENT=126 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `employee_id`, `date`, `check_in_time`, `check_out_time`, `total_worked`) VALUES
(45, 7, '2025-08-12', '04:05:37', '10:05:37', NULL),
(46, 1, '2025-08-12', '15:09:26', '10:05:37', NULL),
(47, 3, '2025-08-12', '15:12:53', '10:05:37', NULL),
(48, 1, '2025-08-13', '06:15:47', '12:30:19', NULL),
(49, 3, '2025-08-13', '18:05:09', '19:43:37', NULL),
(50, 5, '2025-08-13', '18:08:26', '19:43:37', NULL),
(51, 7, '2025-08-13', '06:34:21', '18:31:55', NULL),
(52, 1, '2025-08-14', '20:25:40', NULL, NULL),
(53, 1, '2025-09-04', '06:34:07', '18:31:55', NULL),
(54, 9, '2025-09-04', '06:10:21', '12:30:20', NULL),
(55, 1, '2025-09-05', '13:24:33', '19:42:14', NULL),
(56, 3, '2025-09-05', '13:25:08', '19:42:43', NULL),
(57, 5, '2025-09-05', '13:26:02', '19:43:12', NULL),
(58, 7, '2025-09-05', '13:26:31', '19:43:37', NULL),
(59, 1, '2025-09-06', '18:32:14', '19:43:37', NULL),
(60, 3, '2025-09-06', '06:14:02', '12:30:20', NULL),
(61, 5, '2025-09-06', '06:15:52', '12:30:20', NULL),
(62, 7, '2025-09-06', '06:16:44', '12:30:20', NULL),
(63, 11, '2025-09-06', '06:18:11', '12:30:20', NULL),
(64, 12, '2025-09-06', '06:18:39', '12:30:20', NULL),
(65, 13, '2025-09-06', '06:19:14', '12:30:20', NULL),
(66, 18, '2025-09-06', '06:19:40', '12:30:20', NULL),
(67, 5, '2025-09-07', '13:05:44', '19:11:29', NULL),
(68, 1, '2025-09-07', '13:06:07', '19:11:00', NULL),
(69, 3, '2025-09-07', '13:06:37', '19:10:38', NULL),
(70, 7, '2025-09-07', '13:07:17', '19:11:46', NULL),
(71, 11, '2025-09-07', '13:08:18', '19:15:42', NULL),
(72, 12, '2025-09-07', '13:08:44', '19:16:01', NULL),
(73, 13, '2025-09-07', '13:09:08', '19:16:22', NULL),
(74, 18, '2025-09-07', '13:09:31', '19:16:44', NULL),
(75, 20, '2025-09-07', '13:09:56', '19:17:02', NULL),
(76, 22, '2025-09-07', '13:10:24', '19:17:18', NULL),
(77, 23, '2025-09-07', '13:10:50', '19:17:35', NULL),
(78, 5, '2025-09-04', '18:35:23', NULL, NULL),
(79, 3, '2025-09-04', '18:37:25', NULL, NULL),
(80, 7, '2025-09-04', '18:39:44', NULL, NULL),
(81, 1, '2025-09-03', '18:41:39', '19:18:44', NULL),
(82, 3, '2025-09-03', '18:42:15', '19:19:04', NULL),
(83, 5, '2025-09-03', '18:43:52', '19:19:25', NULL),
(84, 7, '2025-09-03', '18:46:30', '19:19:41', NULL),
(85, 9, '2025-09-03', '18:51:45', '19:18:29', NULL),
(86, 11, '2025-09-03', '19:19:56', NULL, NULL),
(87, 13, '2025-09-03', '19:22:12', '19:22:18', NULL),
(88, 13, '2025-09-02', '19:23:21', NULL, NULL),
(89, 1, '2025-09-02', '19:24:30', '19:24:33', NULL),
(90, 3, '2025-09-02', '19:29:48', '19:29:56', NULL),
(91, 9, '2025-09-02', '19:35:02', NULL, NULL),
(92, 1, '2025-09-08', '13:46:41', '19:17:35', NULL),
(93, 3, '2025-09-08', '13:47:43', '19:17:35', NULL),
(94, 5, '2025-09-08', '13:48:17', '19:17:35', NULL),
(95, 7, '2025-09-08', '14:02:54', '19:17:35', NULL),
(96, 9, '2025-09-08', '14:03:37', '19:17:35', NULL),
(97, 11, '2025-09-08', '14:04:19', '19:17:35', NULL),
(98, 1, '2025-09-10', '13:51:16', '19:17:35', NULL),
(99, 3, '2025-09-10', '17:53:17', '19:43:37', NULL),
(100, 1, '2025-09-11', '13:41:59', '19:43:37', NULL),
(101, 7, '2025-09-15', '11:56:19', NULL, NULL),
(102, 5, '2025-09-15', '11:58:57', NULL, NULL),
(103, 1, '2025-09-15', '12:43:44', NULL, NULL),
(104, 3, '2025-09-15', '12:44:17', NULL, NULL),
(105, 9, '2025-09-15', '12:45:20', NULL, NULL),
(106, 11, '2025-09-15', '12:45:46', NULL, NULL),
(107, 1, '2025-10-03', '12:01:35', NULL, NULL),
(108, 1, '2025-10-04', '13:32:02', NULL, NULL),
(109, 1, '2025-10-15', '18:06:28', NULL, NULL),
(110, 1, '2025-10-19', '21:42:24', NULL, NULL),
(111, 3, '2025-10-19', '21:42:50', NULL, NULL),
(112, 5, '2025-10-19', '21:43:25', NULL, NULL),
(113, 7, '2025-10-19', '21:43:50', NULL, NULL),
(114, 9, '2025-10-19', '21:44:21', NULL, NULL),
(115, 11, '2025-10-19', '21:44:57', NULL, NULL),
(116, 12, '2025-10-19', '21:45:17', NULL, NULL),
(117, 13, '2025-10-19', '21:46:04', NULL, NULL),
(118, 18, '2025-10-19', '21:47:31', NULL, NULL),
(119, 20, '2025-10-19', '21:48:35', NULL, NULL),
(120, 22, '2025-10-19', '21:48:58', NULL, NULL),
(121, 23, '2025-10-19', '21:49:21', NULL, NULL),
(122, 25, '2025-10-19', '21:50:18', NULL, NULL),
(123, 24, '2025-10-19', '21:56:09', NULL, NULL),
(124, 1, '2025-10-28', '18:43:18', NULL, NULL),
(125, 26, '2025-10-31', '21:22:12', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

DROP TABLE IF EXISTS `candidates`;
CREATE TABLE IF NOT EXISTS `candidates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `contact` varchar(15) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `qualification` varchar(100) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `resume` varchar(255) DEFAULT NULL,
  `experience_status` enum('Yes','No') DEFAULT NULL,
  `experience_details` text,
  `employee_type` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(20) DEFAULT 'Pending',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`id`, `full_name`, `email`, `password`, `contact`, `gender`, `qualification`, `dob`, `photo`, `resume`, `experience_status`, `experience_details`, `employee_type`, `status`) VALUES
(3, 'archana rajesh dhara', 'archana@gmail.com', '$2y$10$sCjnRMsY5cS5piGcfwMKpe0y3zrKdf/jyKmw2Ff8V0okuTjFEiTNa', '9867543432', 'Female', 'PhD', '2005-06-04', '1751292165_photo_2.jpg', '1751292165_resume_inernship.docx', 'No', '', 0, 'Pending'),
(4, 'varsha murlimohan nageli', 'varsha@gmail.com', '$2y$10$Wpfvcl5E48kMudl1ZSrO1e.xoOw/AHrjjQIGDrxW5V8HBLrWOwHJy', '7645793898', 'Female', 'Graduation', '2003-08-15', '2.jpg', 'whole project.docx', 'Yes', 'experiance in python ', 0, 'Pending'),
(18, 'madhukar d mandwad', 'madhukar@gmail.com', '$2y$10$60p9YPeMN99WBEMbfkDPP.NNho4qJabBWqXEOVvImDqfvgWqA3Ami', '7058463071', 'Male', 'Graduation', '2005-07-20', '31.jpg', '1751292165_resume_inernship.docx', 'Yes', '3 years', 0, 'Pending'),
(11, 'sanjana rajesh dhara', 'sanjana@gmail.com', '$2y$10$Hq5Y540UiOBm.DMHyThbZeDUjzB6eQ2m5SiC2NICHaNULtPhc6GEi', '9876543219', 'Female', 'Diploma', '2006-06-20', '1751292165_photo_2.jpg', '1751292165_resume_inernship.docx', 'Yes', '2 years', 0, 'Pending'),
(21, 'Arch rajesh dhara', 'dhara@gmail.com', '$2y$10$ifDtFMGMzdSNzqRpqTZeEeXD52RdHe8UXX1MjDPxrCVdVtw4k0yiu', '8767678991', 'Female', 'PhD', '2004-11-24', '2.jpg', 'uploads/1757170739_DB5.pdf', 'No', '', 0, 'Pending'),
(22, 'Shubh Arvind Kyama', 'shubh@gmail.com', '$2y$10$mP3OgRpoL5ipadGQFjmtsOy675iMqVE/3VdqE6YdN/1MmxryxfHdm', '8877669990', 'Female', 'PhD', '2005-06-14', '2.jpg', 'uploads/1757171190_1757165181_DB5.pdf', 'Yes', '2yrs', 0, 'Pending'),
(13, 'manjunath s manure', 'manure@gmail.com', '$2y$10$0gp3SyhI9/ixPRSPpFPRjOEEPna1pIQ5P6ojcwf1htZZ4tAR/BIQm', '7645793898', 'Male', 'Diploma', '1996-03-21', '31.jpg', '1751292165_resume_inernship.docx', 'No', '', 0, 'Pending'),
(14, 'Anita M Rooge', 'anita@gmail.com', '$2y$10$COYrXji5hCdbI.V2HFPgOOMyP9brh5FROj5c5RpaVG7fY6ugEcp9m', '9876543219', 'Female', 'Post Graduation', '1994-06-15', '2.jpg', '1751292165_resume_inernship.docx', 'No', '', 0, 'Pending'),
(23, 'anusha ra rajul', 'anusha@gmail.com', '$2y$10$7jJQjBw0PCYp3.Y6qZvfpuzt4wzyNlFYiTcsWS8Zt2LFm7zPbWOwa', '8877996655', 'Female', 'Post Graduation', '2002-06-25', 'uploads/photos/68c171ff9e5ba_2.jpg', 'uploads/1757171543_1757165181_DB5.pdf', 'No', '', 0, 'Promoted'),
(24, 'varshaa m nagelli', 'varshaa1@gmail.com', '$2y$10$kKnKWdrhquIbXTrPvOvuxeKRe5MycuxDtGFJCo5jVWH.WXSkcJeSO', '7645793898', 'Female', 'Diploma', '2004-08-15', '2.jpg', 'inernship.docx', 'Yes', 'yess 2years ', 0, 'Pending'),
(25, 'Sakshi M Mandwad', 'sakshi@gmail.com', '$2y$10$.k9ZcPvXE9SaELG5zjhmU.zBnjjGoVgmJZ6rR44gK9v81y8gXlm8e', '7645793898', 'Female', '12th', '2004-05-17', '2.jpg', 'NGO Report.docx', 'No', '', 0, 'Pending'),
(26, 'archana r dhara', 'arch04@gmail.com', '$2y$10$9cZ2hafMbAT8quJj27IjAeiaDk.DHCsTXmKPjmV9wHpQsDLC.Us3q', '7645793898', 'Female', 'Graduation', '2005-06-04', '2.jpg', '1755094504_The Kerala Travel Agent.docx', 'No', '', 0, 'Pending'),
(27, 'varsha m nagelli', 'varsha7@gmail.com', '$2y$10$fGiFVTLyCETOO4BgtMRv4OSaUm5FehXG38Qd2KPVSQh9Kk2FsQ/1O', '7645793898', 'Female', 'Graduation', '2003-08-15', '2.jpg', '1755004741_The Kerala Travel Agent.docx', 'Yes', '3 yrs', 0, 'Pending'),
(28, 'shubhangi A kyama', 'shubh30@gmail.com', '$2y$10$luQWReanNgi3VGTYwXP/7e4lEtCuWVb5LPkFCdZEiOyHYY1GAUZtq', '7058463071', 'Female', 'Graduation', '2004-12-30', '2.jpg', 'whole project.docx', 'No', '', 0, 'Pending'),
(29, 'sanjana m mandwad', 'sanjana20@gmail.com', '$2y$10$OVfr5Q/a8SHcIjZ1ldAg8edJi2nLfAdnRdUCiE0XRPrcWpuMuXrnW', '7058463071', 'Female', 'Graduation', '2006-06-20', '2.jpg', 'NGO Report.docx', 'No', '', 0, 'Pending'),
(30, 'archana R dhara', 'DArchana04@gmail.com', '$2y$10$65I74jORD.rTIC1dU8VFuOo5opDnzWyrmb0Au4j8G47TIud1S4Jz.', '9876543219', 'Female', 'Graduation', '2005-06-04', '2.jpg', 'Quesstionaries.docx', 'No', '', 0, 'Promoted'),
(31, 'manekari rahul k', 'rahul@gmail.com', '$2y$10$o5Uf1kI90FKovlDUaz0jIun5KdtKkrCS2zmLGB460wzcQG7b6lXlq', '7058463071', 'Male', 'Post Graduation', '1998-03-29', '31.jpg', 'ProjectReport.pdf', 'Yes', 'IT software testing', 0, 'Promoted'),
(32, 'Aishwarya V Mandwad', 'aishwarya@gmail.com', '$2y$10$Z8HUYdeGLrwMFQSm1XHZiuW.vqMVagFC0mX6z5EB08Al.AoW2YnaO', '7645793898', 'Female', 'Graduation', '2003-06-27', '2.jpg', 'uploads/1761658553_ProjectReport.pdf', 'No', '', 0, 'Promoted');

--
-- Triggers `candidates`
--
DROP TRIGGER IF EXISTS `trg_after_employee_type_update`;
DELIMITER $$
CREATE TRIGGER `trg_after_employee_type_update` AFTER UPDATE ON `candidates` FOR EACH ROW BEGIN
  -- Only act if employee_type changed to 1
  IF NEW.employee_type = 1 AND OLD.employee_type != 1 THEN

    INSERT INTO employees
    (
      employee_id,
      candidate_id,
      full_name,
      email,
      password, -- plain text 'HR123'
      contact,
      gender,
      qualification,
      dob,
      photo
    )
    VALUES
    (
      CONCAT('EMP', LPAD(NEW.id, 3, '0')),
      NEW.id,
      NEW.full_name,
      NEW.email,
      'HR123',
      NEW.contact,
      NEW.gender,
      NEW.qualification,
      NEW.dob,
      NEW.photo
    );

    DELETE FROM candidates WHERE id = NEW.id;

  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `created_at`) VALUES
(1, 'sanjana', 'sanjanamandwad2@gmail.com', 'hello', 'hello world...', '2025-07-25 08:10:57'),
(2, 'sanjana', 'sanjanamandwad2@gmail.com', 'hello', 'hjhjhc', '2025-07-25 08:12:21'),
(3, 'sanjana', 'Sanju@gmail.com', 'hello', 'hi', '2025-07-25 08:16:36');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
CREATE TABLE IF NOT EXISTS `employees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(50) DEFAULT NULL,
  `candidate_id` int DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `contact` varchar(15) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `qualification` varchar(100) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `experience_status` enum('Yes','No') DEFAULT NULL,
  `experience_details` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `job_id` int DEFAULT NULL,
  `account_no` varchar(30) DEFAULT NULL,
  `ifsc_code` varchar(15) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_id` (`employee_id`),
  UNIQUE KEY `email` (`email`),
  KEY `candidate_id` (`candidate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `employee_id`, `candidate_id`, `full_name`, `email`, `password`, `contact`, `gender`, `qualification`, `dob`, `photo`, `experience_status`, `experience_details`, `created_at`, `job_id`, `account_no`, `ifsc_code`, `bank_name`) VALUES
(1, 'EMP001', 10, 'Shubhangi Arvind kyama', 'shubhangi12@gmail.com', 'HR123', '7058463071', 'Female', 'Graduation', '2004-12-31', '1751292165_photo_2.jpg', 'Yes', 'hello', '2025-08-02 19:48:15', 2, NULL, NULL, NULL),
(3, 'EMP002', 15, 'dipak m mandwad', 'dipak@gmail.com', 'HR123', '7645793898', 'Male', 'Graduation', '2003-03-09', '31.jpg', 'No', '', '2025-08-02 19:56:54', 8, NULL, NULL, NULL),
(5, 'EMP003', 16, 'varsha govind nagelli', 'varsha07@gmail.com', 'HR123', '7058463071', 'Female', 'Diploma', '2003-08-14', '2.jpg', 'Yes', '2', '2025-08-02 20:07:06', 5, NULL, NULL, NULL),
(7, 'EMP004', 17, 'soundarya ashok mandwad', 'soundaryamandwad@gmail.com', 'HR123', '7645793898', 'Female', 'Post Graduation', '1998-09-09', '2.jpg', 'No', '0', '2025-08-03 21:49:01', 2, NULL, NULL, NULL),
(9, 'EMP005', 7, 'sanjana madhukar mandwad', 'sanjanamandwad2@gmail.com', 'HR123', '7058463071', 'Female', 'Post Graduation', '2006-06-20', 'uploads/photos/689dfd86aa6d8_g1.jpg', 'Yes', '3', '2025-09-04 18:28:58', 5, '875875969', 'hjnfkfjfk', 'bankofmaharshra'),
(11, 'EMP006', 19, 'Shubham Arvind Kyama', 'shubham@gmail.com', 'HR123', '8876543209', 'Male', 'PhD', '2002-03-25', '31.jpg', 'Yes', '4', '2025-09-06 19:10:44', 7, NULL, NULL, NULL),
(12, 'EMP007', 20, 'archana rajesh dhara', 'archana04@gmail.com', 'HR123', '7645793898', 'Female', 'Graduation', '2005-06-04', '2.jpg', 'No', '0', '2025-09-06 19:20:33', 3, NULL, NULL, NULL),
(13, 'EMP008', 4, 'varsha murlimohan nageli', 'varsha@gmail.com', 'HR123', '7645793898', 'Female', 'Graduation', '2003-08-15', '2.jpg', 'Yes', '0', '2025-09-06 20:05:44', 1, NULL, NULL, NULL),
(18, 'EMP009', 12, 'sanjana madhukar kyama', 'kyama@gmail.com', 'HR123', '9876543219', 'Female', 'Post Graduation', '2006-06-20', '2.jpg', 'Yes', '4', '2025-09-06 20:18:44', 5, NULL, NULL, NULL),
(20, 'EMP010', 21, 'Arch rajesh dhara', 'dhara@gmail.com', 'HR123', '8767678991', 'Female', 'PhD', '2004-11-24', '2.jpg', 'No', '0', '2025-09-06 20:30:58', 1, NULL, NULL, NULL),
(22, 'EMP011', 22, 'Shubh Arvind Kyama', 'shubh@gmail.com', 'HR123', '8877669990', 'Female', 'PhD', '2005-06-14', '2.jpg', 'Yes', '2', '2025-09-06 20:37:58', 7, NULL, NULL, NULL),
(23, 'EMP012', 23, 'anusha ra rajul', 'anusha@gmail.com', 'HR123', '8877996655', 'Female', 'Post Graduation', '2002-06-25', '1751292165_photo_2.jpg', 'No', '0', '2025-09-06 20:44:00', 7, NULL, NULL, NULL),
(24, 'EMP013', 30, 'archana R dhara', 'DArchana04@gmail.com', 'HR123', '9876543219', 'Female', 'Graduation', '2005-06-04', '2.jpg', 'No', '0', '2025-10-04 12:41:01', 3, NULL, NULL, NULL),
(25, 'EMP014', 31, 'manekari rahul k', 'rahul@gmail.com', 'HR123', '7058463071', 'Male', 'Post Graduation', '1998-03-29', '31.jpg', 'Yes', '0', '2025-10-14 18:39:34', 5, NULL, NULL, NULL),
(26, 'EMP015', 32, 'Aishwarya V Mandwad', 'aishwarya@gmail.com', 'HR123', '7645793898', 'Female', 'Graduation', '2003-06-27', '2.jpg', 'No', '0', '2025-10-28 19:08:52', 3, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employee_bank_details`
--

DROP TABLE IF EXISTS `employee_bank_details`;
CREATE TABLE IF NOT EXISTS `employee_bank_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `employee_id` int NOT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `ifsc_code` varchar(20) DEFAULT NULL,
  `branch_name` varchar(100) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `employee_bank_details`
--

INSERT INTO `employee_bank_details` (`id`, `employee_id`, `bank_name`, `account_number`, `ifsc_code`, `branch_name`, `updated_at`) VALUES
(1, 26, 'bankofmaharshra', '768635943793', '56677', 'hsdkjhakjf', '2025-10-31 06:37:16'),
(2, 1, 'state bank of maharashtra', '899873628746', '56677', 'state', '2025-10-31 06:59:18'),
(3, 3, 'state bank of maharashtra', '899873628746', 'SBIN0001234', 'state', '2025-10-31 07:01:42'),
(4, 9, 'bankofmaharshra', '899873628746', 'SBIN0001234', 'state', '2025-10-31 07:06:53'),
(5, 13, 'bankofmaharshra', '899873628746', 'SBIN0001234', 'state', '2025-10-31 12:58:03'),
(6, 25, 'bankofmaharshra', '899873628746', 'SBIN0001234', 'state', '2025-10-31 13:19:18'),
(7, 18, 'bankofmaharshra', '899873628746', 'SBIN0001234', 'state', '2025-10-31 13:23:59');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `job_id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `posted_date` date NOT NULL DEFAULT (curdate()),
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`job_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`job_id`, `title`, `description`, `posted_date`, `from_date`, `to_date`, `image`) VALUES
(1, 'Software Developer', 'Manage recruitment and employee engagement activities.', '2025-07-13', '2025-07-16', '2025-11-30', '13.webp'),
(2, 'HR Executive', 'Manage recruitment and employee engagement activities.', '2025-07-12', '2025-07-12', '2025-11-29', '16.webp'),
(3, 'Digital Marketing ', 'Manage recruitment and employee engagement activities.', '2025-07-10', '2025-07-10', '2025-11-25', '11.webp'),
(4, 'UI/UX designer', 'Manage recruitment and employee engagement activities.', '2025-06-20', '2025-06-20', '2025-11-30', '15.webp'),
(5, 'web designer', 'Manage recruitment and employee engagement activities.', '2025-06-25', '2025-06-25', '2025-11-26', '12.webp'),
(7, 'data science', 'Manage recruitment and employee engagement activities.', '2025-07-16', '2025-07-16', '2025-11-30', '14.webp'),
(8, 'A Language', 'Manage recruitment and employee engagement activities....', '2025-08-01', '2025-08-02', '2025-12-31', '14.webp'),
(9, 'python', 'Manage recruitment and employee engagement activities....', '2025-08-02', '2025-08-04', '2025-11-19', '688e1430cc599_12.webp'),
(10, 'Java Developer', 'Manage recruitment and employee engagement activities....', '2025-09-05', '2025-09-07', '2025-12-15', '68ba7bee8074b_13.webp');

-- --------------------------------------------------------

--
-- Table structure for table `job_applications`
--

DROP TABLE IF EXISTS `job_applications`;
CREATE TABLE IF NOT EXISTS `job_applications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `candidate_id` int DEFAULT NULL,
  `job_id` int DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `resume` varchar(255) DEFAULT NULL,
  `cover_letter` text,
  `applied_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(50) DEFAULT 'Pending',
  `reason` text,
  `interview_date` date DEFAULT NULL,
  `interview_time` time DEFAULT NULL,
  `applied_on` datetime DEFAULT CURRENT_TIMESTAMP,
  `meet_link` varchar(255) DEFAULT NULL,
  `interview_status` enum('Pending','Attended','Not Attended') DEFAULT 'Pending',
  `selection_status` enum('Pending','Selected','Rejected') DEFAULT 'Pending',
  `rejection_reason` text,
  `offer_letter` varchar(255) DEFAULT NULL,
  `interview_letter_path` varchar(255) DEFAULT NULL,
  `offer_status` enum('Pending','Accepted','Rejected') DEFAULT 'Pending',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `job_applications`
--

INSERT INTO `job_applications` (`id`, `candidate_id`, `job_id`, `name`, `email`, `phone`, `resume`, `cover_letter`, `applied_at`, `status`, `reason`, `interview_date`, `interview_time`, `applied_on`, `meet_link`, `interview_status`, `selection_status`, `rejection_reason`, `offer_letter`, `interview_letter_path`, `offer_status`) VALUES
(1, 2, 4, 'Shubhangi Arvind Kyama', 'shubhangi@gmail.com', '9876543210', '1751292165_resume_inernship.docx', '', '2025-07-15 13:57:49', 'Shortlisted', '', '2025-08-08', '11:15:00', '2025-07-20 20:35:21', 'https://meet.google.com/4b51869468', 'Attended', 'Selected', '', 'offer_letters/offer_1.pdf', NULL, 'Pending'),
(2, 2, 2, 'Shubhangi Arvind Kyama', 'shubhangi@gmail.com', '9876543210', '1751292165_resume_inernship.docx', 'hello', '2025-07-15 15:11:34', 'Shortlisted', '', '2025-08-08', '15:00:00', '2025-07-20 20:35:21', 'https://meet.google.com/7e837e5913', 'Attended', 'Rejected', '                                not applicable                                                                        ', NULL, NULL, 'Pending'),
(3, 1, 1, 'sanjana madhukar mandwad', 'Sanju@gmail.com', '7058463071', '1751292165_resume_inernship.docx', '', '2025-07-16 13:39:42', 'Rejected', 'you are not applicable', '2025-07-29', '08:30:00', '2025-07-20 20:35:21', 'https://meet.google.com/9fc387298d', 'Attended', 'Selected', '', 'offer_letters/offer_3.pdf', NULL, 'Pending'),
(4, 6, 7, 'prabhakar yangandul ruchita', 'ruchita@gmail.com', '7878787878', '1751292165_resume_inernship.docx', 'if you satisfied approve my application...', '2025-07-26 16:06:06', 'Shortlisted', '', '2025-08-20', '08:00:00', '2025-07-26 21:36:06', 'https://meet.google.com/9a1671e3ae', 'Attended', 'Selected', '', NULL, NULL, 'Pending'),
(17, 4, 1, 'varsha murlimohan nageli', 'varsha@gmail.com', '7645793898', 'whole project.docx', '', '2025-09-06 14:11:30', 'Shortlisted', NULL, '2025-09-08', '11:00:00', '2025-09-06 19:41:30', 'https://meet.google.com/73809bd68c', 'Attended', 'Selected', '', NULL, NULL, 'Pending'),
(6, 10, 2, 'Shubhangi Arvind kyama', 'shubhangi12@gmail.com', '7058463071', '1751292165_resume_inernship.docx', 'hello', '2025-07-30 12:38:21', 'pending', '', '2025-08-13', '08:11:00', '2025-07-30 18:08:21', 'https://meet.google.com/faf6ed7038', 'Attended', 'Selected', '', NULL, NULL, 'Pending'),
(7, 11, 5, 'sanjana rajesh dhara', 'sanjana@gmail.com', '9876543219', '1751292165_resume_inernship.docx', '', '2025-08-01 12:49:33', 'Rejected', 'not applicable', '2025-08-15', '17:30:00', '2025-08-01 18:19:33', 'https://meet.google.com/016eb76e70', 'Attended', 'Rejected', 'not applicable', NULL, NULL, 'Pending'),
(15, 19, 7, 'Shubham Arvind Kyama', 'shubham@gmail.com', '8876543209', 'uploads/1757165181_DB5.pdf', '', '2025-09-06 13:26:21', 'Shortlisted', NULL, '2025-09-06', '17:59:00', '2025-09-06 18:56:21', 'https://meet.google.com/9765113a41', 'Attended', 'Selected', '', NULL, NULL, 'Pending'),
(16, 20, 3, 'archana rajesh dhara', 'archana04@gmail.com', '7645793898', '1755005651_The Kerala Travel Agent.docx', '', '2025-09-06 13:48:50', 'Shortlisted', NULL, '2025-09-07', '11:59:00', '2025-09-06 19:18:50', 'https://meet.google.com/d67c69bfbf', 'Attended', 'Selected', '', NULL, NULL, 'Pending'),
(10, 15, 8, 'dipak m mandwad', 'dipak@gmail.com', '7645793898', 'The Kerala Travel Agent.docx', '', '2025-08-02 14:22:27', 'Shortlisted', NULL, '2025-08-14', '15:50:00', '2025-08-02 19:52:27', 'https://meet.google.com/f35aa89d38', 'Attended', 'Selected', '', NULL, NULL, 'Pending'),
(11, 16, 5, 'varsha govind nagelli', 'varsha07@gmail.com', '7058463071', '1751292165_resume_inernship.docx', '', '2025-08-02 14:35:50', 'Shortlisted', NULL, '2025-08-22', '16:30:00', '2025-08-02 20:05:50', 'https://meet.google.com/42a7f919f4', 'Attended', 'Selected', '', NULL, NULL, 'Pending'),
(12, 17, 2, 'soundarya ashok mandwad', 'soundaryamandwad@gmail.com', '7645793898', '1751292165_resume_inernship.docx', '', '2025-08-03 16:14:40', 'Shortlisted', NULL, '2025-08-20', '16:00:00', '2025-08-03 21:44:40', 'https://meet.google.com/eddf9c5a4f', 'Attended', 'Selected', '', NULL, NULL, 'Pending'),
(13, 18, 1, 'madhukar d mandwad', 'madhukar@gmail.com', '7058463071', '1751292165_resume_inernship.docx', '', '2025-08-03 16:23:32', 'Shortlisted', NULL, '2025-08-20', '17:00:00', '2025-08-03 21:53:32', 'https://meet.google.com/18f13c2f67', 'Attended', 'Rejected', 'not applicable', NULL, NULL, 'Pending'),
(14, 7, 5, 'sanjana madhukar mandwad', 'sanjanamandwad2@gmail.com', '7058463071', '1751292165_resume_inernship.docx', '', '2025-09-04 12:53:06', 'Shortlisted', NULL, '2025-09-15', '17:00:00', '2025-09-04 18:23:06', 'https://meet.google.com/4b89b7b37d', 'Attended', 'Selected', '', NULL, NULL, 'Pending'),
(18, 12, 5, 'sanjana madhukar kyama', 'kyama@gmail.com', '9876543219', '1751292165_resume_inernship.docx', '', '2025-09-06 14:46:54', 'Shortlisted', NULL, '2025-09-07', '12:00:00', '2025-09-06 20:16:54', 'https://meet.google.com/a55f14b510', 'Attended', 'Selected', '', NULL, NULL, 'Accepted'),
(19, 21, 1, 'Arch rajesh dhara', 'dhara@gmail.com', '8767678991', 'uploads/1757170739_DB5.pdf', '', '2025-09-06 14:58:59', 'Shortlisted', NULL, '2025-09-06', '17:30:00', '2025-09-06 20:28:59', 'https://meet.google.com/b41a209e32', 'Attended', 'Selected', '', NULL, NULL, 'Accepted'),
(20, 22, 7, 'Shubh Arvind Kyama', 'shubh@gmail.com', '8877669990', 'uploads/1757171190_1757165181_DB5.pdf', '', '2025-09-06 15:06:30', 'Shortlisted', NULL, '2025-09-06', '15:40:00', '2025-09-06 20:36:30', 'https://meet.google.com/1492c767ff', 'Attended', 'Selected', '', NULL, NULL, 'Accepted'),
(21, 23, 7, 'anusha ra rajul', 'anusha@gmail.com', '8877996655', 'uploads/1757171543_1757165181_DB5.pdf', '', '2025-09-06 15:12:23', 'Shortlisted', NULL, '2025-09-06', '17:43:00', '2025-09-06 20:42:23', 'https://meet.google.com/b1ab72b214', 'Attended', 'Selected', '', NULL, NULL, 'Accepted'),
(22, 24, 2, 'varshaa m nagelli', 'varshaa1@gmail.com', '7645793898', 'inernship.docx', '', '2025-09-07 07:13:21', 'Pending', NULL, NULL, NULL, '2025-09-07 12:43:21', NULL, 'Pending', 'Pending', NULL, NULL, NULL, 'Pending'),
(23, 25, 1, 'sakshi M mandwad', 'sakshi@gmail.com', '7645793898', 'NGO Report.docx', '', '2025-09-07 07:20:20', 'Rejected', 'Not applicable your qualifications for this job', NULL, NULL, '2025-09-07 12:50:20', NULL, 'Pending', 'Pending', NULL, NULL, NULL, 'Pending'),
(24, 29, 10, 'sanjana m mandwad', 'sanjana20@gmail.com', '7058463071', 'NGO Report.docx', '', '2025-09-15 08:19:44', 'Shortlisted', NULL, '2025-10-06', '14:45:00', '2025-09-15 13:49:44', 'https://meet.google.com/dcf8060b44', 'Pending', 'Pending', NULL, NULL, NULL, 'Pending'),
(25, 29, 10, 'sanjana m mandwad', 'sanjana20@gmail.com', '7058463071', 'NGO Report.docx', '', '2025-09-15 08:20:04', 'Pending', NULL, NULL, NULL, '2025-09-15 13:50:04', NULL, 'Pending', 'Pending', NULL, NULL, NULL, 'Pending'),
(26, 30, 3, 'archana R dhara', 'DArchana04@gmail.com', '9876543219', 'Quesstionaries.docx', '', '2025-10-04 07:08:21', 'Shortlisted', NULL, '2025-10-06', '16:30:00', '2025-10-04 12:38:21', 'https://meet.google.com/2d452b12db', 'Attended', 'Selected', '', NULL, NULL, 'Accepted'),
(27, 31, 5, 'manekari rahul k', 'rahul@gmail.com', '7058463071', 'ProjectReport.pdf', '', '2025-10-14 13:06:20', 'Shortlisted', NULL, '2025-10-15', '10:30:00', '2025-10-14 18:36:20', 'https://meet.google.com/7e8b83c36e', 'Attended', 'Selected', '', NULL, NULL, 'Accepted'),
(28, 32, 3, 'Aishwarya V Mandwad', 'aishwarya@gmail.com', '7645793898', 'uploads/1761658553_ProjectReport.pdf', '', '2025-10-28 13:35:53', 'Shortlisted', NULL, '2025-10-28', '16:10:00', '2025-10-28 19:05:53', 'https://meet.google.com/7265792e59', 'Attended', 'Selected', '', NULL, NULL, 'Accepted');

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

DROP TABLE IF EXISTS `leave_requests`;
CREATE TABLE IF NOT EXISTS `leave_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `employee_id` int DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `leave_reason` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `applied_on` datetime DEFAULT CURRENT_TIMESTAMP,
  `decision_date` date DEFAULT NULL,
  `is_notified` tinyint(1) DEFAULT '0',
  `rejection_reason` text,
  `seen` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `leave_requests`
--

INSERT INTO `leave_requests` (`id`, `employee_id`, `from_date`, `to_date`, `leave_reason`, `status`, `applied_on`, `decision_date`, `is_notified`, `rejection_reason`, `seen`) VALUES
(1, 1, '2025-08-10', '2025-08-12', 'Testing manual insert', 'Approved', '2025-08-04 20:36:37', '2025-08-04', 1, NULL, 1),
(2, 7, '2025-08-13', '2025-08-16', 'sick leave', 'Approved', '2025-08-13 18:37:07', '2025-08-13', 0, NULL, 1),
(3, 3, '2025-08-13', '2025-08-16', 'family issue', 'rejected', '2025-08-13 18:52:15', '2025-08-15', NULL, 'not approved', 1),
(4, 3, '2025-08-13', '2025-08-18', 'family issue', 'Approved', '2025-08-13 18:54:11', '2025-08-13', 1, NULL, 1),
(5, 5, '2025-08-13', '2025-08-15', 'sick leave', 'Approved', '2025-08-13 18:55:54', '2025-08-15', 1, NULL, 1),
(6, 5, '2025-08-13', '2025-08-16', 'sick leave', 'Pending', '2025-08-13 18:57:42', NULL, 1, NULL, 1),
(7, 5, '2025-08-13', '2025-08-16', 'sick leave', 'Rejected', '2025-08-13 18:57:42', '2025-08-15', 1, 'not approved', 1),
(8, 5, '2025-08-13', '2025-08-15', 'sick leave', 'Approved', '2025-08-13 18:58:31', '2025-08-15', 1, NULL, 1),
(9, 1, '2025-08-13', '2025-08-23', 'sick leave', 'Pending', '2025-08-13 19:15:13', NULL, 1, NULL, 1),
(10, 1, '2025-12-31', '2029-12-31', 'sick leave', 'Rejected', '2025-08-14 20:29:09', '2025-08-14', 1, NULL, 1),
(11, 9, '2025-09-05', '2025-09-07', 'sick leave', 'Approved', '2025-09-05 13:27:27', '2025-09-05', 0, NULL, 1),
(12, 5, '2025-09-08', '2025-09-09', 'sick leave', 'Pending', '2025-09-07 13:05:22', NULL, 0, NULL, 1),
(13, 7, '2025-09-16', '2025-09-17', 'family issue', 'Approved', '2025-09-15 11:56:56', '2025-09-15', 0, NULL, 1),
(14, 5, '2025-09-16', '2025-09-17', 'sick leave', 'Approved', '2025-09-15 11:59:15', '2025-09-15', 0, NULL, 1),
(15, 1, '2025-10-15', '2025-10-16', 'family issue', 'Approved', '2025-10-15 18:07:23', '2025-10-15', 0, NULL, 1),
(16, 24, '2025-10-20', '2025-10-23', 'Event holiday', 'Approved', '2025-10-19 21:58:06', '2025-10-19', 0, NULL, 1),
(17, 9, '2025-10-20', '2025-10-23', 'family issue', 'Rejected', '2025-10-19 22:00:16', '2025-10-19', 0, 'your project is on going you cant take leave', 1);

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

DROP TABLE IF EXISTS `payroll`;
CREATE TABLE IF NOT EXISTS `payroll` (
  `id` int NOT NULL AUTO_INCREMENT,
  `employee_id` int DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `basic_salary` decimal(10,2) DEFAULT NULL,
  `allowance` decimal(10,2) DEFAULT NULL,
  `deduction` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `comment` text,
  `generated_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `message` text,
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payroll`
--

INSERT INTO `payroll` (`id`, `employee_id`, `full_name`, `title`, `basic_salary`, `allowance`, `deduction`, `total`, `comment`, `generated_at`, `created_at`, `message`) VALUES
(16, 9, NULL, NULL, 50000.00, 100.00, 0.00, 50100.00, NULL, '2025-10-31 19:30:33', '2025-10-31 19:30:33', NULL),
(17, 26, NULL, NULL, 60000.00, 6000.00, 100.00, 65900.00, NULL, '2025-10-31 20:09:49', '2025-10-31 20:09:49', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payslips`
--

DROP TABLE IF EXISTS `payslips`;
CREATE TABLE IF NOT EXISTS `payslips` (
  `id` int NOT NULL AUTO_INCREMENT,
  `employee_id` int NOT NULL,
  `month` varchar(50) DEFAULT NULL,
  `total_salary` decimal(10,2) DEFAULT NULL,
  `generated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payslips`
--

INSERT INTO `payslips` (`id`, `employee_id`, `month`, `total_salary`, `generated_on`) VALUES
(1, 26, 'October 2025', 87800.00, '2025-10-31 20:27:52'),
(2, 1, 'October 2025', 0.00, '2025-10-31 20:29:29'),
(3, 3, 'October 2025', 0.00, '2025-10-31 20:29:29'),
(4, 9, 'October 2025', 51500.00, '2025-10-31 20:29:29'),
(5, 13, 'October 2025', 0.00, '2025-10-31 20:29:29'),
(6, 25, 'October 2025', 0.00, '2025-10-31 20:29:29'),
(7, 18, 'October 2025', 0.00, '2025-10-31 20:29:29');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
CREATE TABLE IF NOT EXISTS `projects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `employee_id` int NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `progress` int DEFAULT '0',
  `progress_file` varchar(255) DEFAULT NULL,
  `remarks` text,
  `file_path` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'In Progress',
  `description` text,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `employee_id`, `project_name`, `progress`, `progress_file`, `remarks`, `file_path`, `status`, `description`, `updated_at`) VALUES
(1, 7, 'DevelopWebApplication', 100, '', 'hello', 'uploads/progress/1761835561_WhatsApp_Unknown_2025-10-12_at_7.19.46_PM.zip', 'Completed', 'heloo', '2025-10-30 20:16:01'),
(2, 3, 'TalentAcquisition', 50, NULL, 'hello', 'uploads/progress/1761883258_Angular_Notes.docx', 'In Progress', 'helloo.....', '2025-10-31 09:30:58'),
(3, 7, 'E-Commerce Platform', 0, NULL, NULL, NULL, 'In Progress', 'jsjaNjkkldl', '2025-10-31 09:36:25'),
(4, 26, 'Social Media Content Campaign', 100, NULL, 'hello', 'uploads/progress/1761925789_WhatsApp_Unknown_2025-10-12_at_7.19.46_PM.zip', 'Completed', 'hello', '2025-10-31 21:19:49');

-- --------------------------------------------------------

--
-- Table structure for table `user_tokens`
--

DROP TABLE IF EXISTS `user_tokens`;
CREATE TABLE IF NOT EXISTS `user_tokens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `expiry` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_tokens`
--

INSERT INTO `user_tokens` (`id`, `user_id`, `token_hash`, `expiry`, `created_at`) VALUES
(1, 1, '$2y$10$q4fc.TmmqtQibpKGLa1Y/OsYM2hr1X5Xqs7PlsaOWASHnKlIlhz1i', '2025-08-26 14:31:50', '2025-07-27 14:31:50');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `employee_bank_details`
--
ALTER TABLE `employee_bank_details`
  ADD CONSTRAINT `employee_bank_details_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `payslips`
--
ALTER TABLE `payslips`
  ADD CONSTRAINT `payslips_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
