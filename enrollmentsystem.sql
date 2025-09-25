-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 24, 2025 at 11:20 AM
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
-- Database: `enrollmentsystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`) VALUES
(1, 'admin', '0192023a7bbd73250516f069df18b500');

-- --------------------------------------------------------

--
-- Table structure for table `enrolled_subjects`
--

CREATE TABLE `enrolled_subjects` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `section_id` int(11) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `day` varchar(20) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `status` varchar(20) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrolled_subjects`
--

INSERT INTO `enrolled_subjects` (`id`, `student_id`, `section_id`, `subject_name`, `day`, `start_time`, `end_time`, `status`) VALUES
(1, '2025001', 1, 'Math 1', 'Monday', '08:00:00', '09:30:00', 'Pending'),
(4, '2025', 1, 'IT Fundamentals', 'Tuesday', '10:00:00', '11:30:00', 'Pending'),
(5, '2025', 3, 'Database Systems', 'Monday', '08:00:00', '09:30:00', 'Pending'),
(6, '2025', 3, 'Statistics', 'Wednesday', '03:00:00', '04:30:00', 'Pending'),
(7, '2025', 0, 'Math 1', 'Monday', '08:00:00', '09:30:00', 'Pending'),
(8, '2025', 0, 'IT Fundamentals', 'Tuesday', '10:00:00', '11:30:00', 'Pending'),
(9, '2025', 0, 'Database Systems', 'Monday', '08:00:00', '09:30:00', 'Pending'),
(10, '2025', 0, 'Web Development', 'Tuesday', '11:00:00', '12:30:00', 'Pending'),
(11, '2025', 0, 'Math 1', 'Monday', '08:00:00', '09:30:00', 'Pending'),
(12, '2025', 0, 'IT Fundamentals', 'Tuesday', '10:00:00', '11:30:00', 'Pending'),
(13, '2025', 0, 'Database Systems', 'Monday', '08:00:00', '09:30:00', 'Pending'),
(14, '2025', 0, 'Web Development', 'Tuesday', '11:00:00', '12:30:00', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `onlineapplication`
--

CREATE TABLE `onlineapplication` (
  `application_id` varchar(20) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) NOT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `student_type` varchar(50) NOT NULL,
  `course` varchar(100) NOT NULL,
  `year_level` varchar(20) NOT NULL,
  `semester` varchar(20) NOT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `civil_status` varchar(20) DEFAULT NULL,
  `nationality` varchar(50) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `pob` varchar(255) DEFAULT NULL,
  `mobile_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `religion` varchar(50) DEFAULT NULL,
  `place` text DEFAULT NULL,
  `education` text DEFAULT NULL,
  `father_info` text DEFAULT NULL,
  `mother_info` text DEFAULT NULL,
  `guardian_info` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `onlineapplication`
--

INSERT INTO `onlineapplication` (`application_id`, `firstname`, `middlename`, `lastname`, `suffix`, `student_type`, `course`, `year_level`, `semester`, `gender`, `civil_status`, `nationality`, `dob`, `pob`, `mobile_number`, `email`, `religion`, `place`, `education`, `father_info`, `mother_info`, `guardian_info`, `created_at`, `status`) VALUES
('APP-4506-37905', 'Clarissa Mae', '', 'Navanes', '', 'Freshman', 'BS in Information Technology', '1st Year', '1st Semester', 'Male', 'Married', 'American', '2005-03-07', 'Dasmarinas Cavite', '09513634155', 'clarissamae537@gmail.com', 'Roman Catholic', ', 064518010, 064518000, 064500000, 060000000, 4100', '{\"primary_school\":\"Musically Elementary School\",\"primary_grad\":\"2016\",\"secondary_school\":\"Tiktok National High School\",\"secondary_grad\":\"2021\",\"strand\":\"ABM\",\"achievement\":\"With Honors\"}', '{\"lname\":\"\",\"fname\":\"\",\"mname\":\"\",\"address\":\"\",\"mobile\":\"\",\"occupation\":\"\"}', '{\"lname\":\"\",\"fname\":\"\",\"mname\":\"\",\"address\":\"\",\"mobile\":\"\",\"occupation\":\"\"}', '{\"lname\":\"Bagamasbad\",\"fname\":\"Ashley\",\"mname\":\"Navanes\",\"address\":\"Washington D.C\",\"mobile\":\"09876543210\",\"occupation\":\"Tiktoker\",\"relationship\":\"Mother\"}', '2025-09-23 05:55:11', 'Accepted'),
('APP-6714-25815', 'John', '', 'Doe', '', 'Freshman', 'BS in Information Technology', '1st Year', '1st Semester', 'Male', 'Single', 'American', '2004-05-07', 'Washington D.C', '09513634155', 'clarissanavanes@gmail.com', 'Roman Catholic', ', 101804011, 101804000, 101800000, 100000000, 4100', '{\"primary_school\":\"Musically Elementary School\",\"primary_grad\":\"2017\",\"secondary_school\":\"Tiktok National High School\",\"secondary_grad\":\"2021\",\"strand\":\"ABM\",\"achievement\":\"With High Honors\"}', '{\"lname\":\"\",\"fname\":\"\",\"mname\":\"\",\"address\":\"\",\"mobile\":\"\",\"occupation\":\"\"}', '{\"lname\":\"\",\"fname\":\"\",\"mname\":\"\",\"address\":\"\",\"mobile\":\"\",\"occupation\":\"\"}', '{\"lname\":\"Bagamasbad\",\"fname\":\"Ashley\",\"mname\":\"Navanes\",\"address\":\"Washington D.C\",\"mobile\":\"09876543210\",\"occupation\":\"Tiktoker\",\"relationship\":\"Aunt\"}', '2025-09-23 08:55:36', 'Accepted');

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `course` varchar(50) NOT NULL,
  `year_level` int(11) NOT NULL,
  `semester` varchar(20) NOT NULL,
  `subjects` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`subjects`)),
  `status` varchar(20) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`id`, `student_id`, `fullname`, `course`, `year_level`, `semester`, `subjects`, `status`, `created_at`) VALUES
(4, '2025-00001', 'Clarissa Mae Navanes', 'BS in Information Technology', 1, '1st Semester', '[{\"section_name\":\"Section A\",\"subject_name\":\"Math 1\",\"day\":\"Monday\",\"start_time\":\"08:00\",\"end_time\":\"09:30\",\"units\":2},{\"section_name\":\"Section A\",\"subject_name\":\"IT Fundamentals\",\"day\":\"Tuesday\",\"start_time\":\"10:00\",\"end_time\":\"11:30\",\"units\":2},{\"section_name\":\"Section C\",\"subject_name\":\"Web Development\",\"day\":\"Tuesday\",\"start_time\":\"11:00\",\"end_time\":\"12:30\",\"units\":3},{\"section_name\":\"Section C\",\"subject_name\":\"Statistics\",\"day\":\"Wednesday\",\"start_time\":\"03:00\",\"end_time\":\"04:30\",\"units\":2}]', 'approved', '2025-09-23 06:13:44'),
(8, '2025-00002', 'John Doe', 'BS in Information Technology', 1, '1st Semester', '[{\"section_name\":\"Section C\",\"subject_name\":\"Database Systems\",\"day\":\"Monday\",\"start_time\":\"08:00\",\"end_time\":\"09:30\",\"units\":3},{\"section_name\":\"Section C\",\"subject_name\":\"Web Development\",\"day\":\"Tuesday\",\"start_time\":\"11:00\",\"end_time\":\"12:30\",\"units\":3},{\"section_name\":\"Section C\",\"subject_name\":\"Statistics\",\"day\":\"Wednesday\",\"start_time\":\"03:00\",\"end_time\":\"04:30\",\"units\":2}]', 'approved', '2025-09-23 09:15:25');

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `section_name` varchar(50) NOT NULL,
  `year_level` varchar(20) NOT NULL,
  `course` varchar(100) NOT NULL,
  `semester` varchar(20) NOT NULL,
  `subjects` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`subjects`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `section_name`, `year_level`, `course`, `semester`, `subjects`, `created_at`) VALUES
(1, 'Section A', '1st Year', 'BS in Information Technology', '1st Semester', '[\r\n  {\"subject\": \"Math 1\", \"day\": \"Monday\", \"start\": \"08:00\", \"end\": \"09:30\", \"units\": 2},\r\n  {\"subject\": \"IT Fundamentals\", \"day\": \"Tuesday\", \"start\": \"10:00\", \"end\": \"11:30\", \"units\": 2},\r\n  {\"subject\": \"English 1\", \"day\": \"Wednesday\", \"start\": \"13:00\", \"end\": \"14:30\", \"units\": 1}\r\n]\r\n', '2025-09-19 09:57:32'),
(2, 'Section B', '1st Year', 'BS in Computer Engineering', '1st Semester', '[\r\n    {\"subject\": \"Physics 1\", \"day\": \"Monday\", \"units\": 2},\r\n    {\"subject\": \"Programming 1\", \"day\": \"Tuesday\", \"units\": 3},\r\n    {\"subject\": \"Calculus 1\", \"day\": \"Wednesday\", \"units\": 2}\r\n ]', '2025-09-19 09:57:32'),
(3, 'Section C', '1st Year', 'BS in Information Technology', '1st Semester', '[\n  {\"subject\": \"Database Systems\", \"day\": \"Monday\", \"start\": \"08:00\", \"end\": \"09:30\", \"units\": 3},\n  {\"subject\": \"Web Development\", \"day\": \"Tuesday\", \"start\": \"11:00\", \"end\": \"12:30\", \"units\": 3},\n  {\"subject\": \"Statistics\", \"day\": \"Wednesday\", \"start\": \"03:00\", \"end\": \"04:30\", \"units\": 2}\n]\n', '2025-09-19 09:57:32');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` varchar(20) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `course` varchar(150) DEFAULT NULL,
  `year_level` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `semester` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `surname`, `firstname`, `course`, `year_level`, `email`, `semester`, `created_at`) VALUES
('2025-00001', 'Navanes', 'Clarissa Mae', 'BS in Information Technology', '1st Year', 'clarissamae537@gmail.com', '1st Semester', '2025-09-23 05:55:11'),
('2025-00002', 'Doe', 'John', 'BS in Information Technology', '1st Year', 'clarissanavanes@gmail.com', '1st Semester', '2025-09-23 08:55:36');

-- --------------------------------------------------------

--
-- Table structure for table `student_payments`
--

CREATE TABLE `student_payments` (
  `id` int(11) NOT NULL,
  `registration_id` int(11) NOT NULL,
  `method` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `units` int(11) NOT NULL,
  `price_per_unit` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_name`, `units`, `price_per_unit`) VALUES
(1, 'Database Systems', 3, 500.00),
(2, 'Web Development', 3, 500.00),
(3, 'Statistics', 2, 500.00),
(4, 'Programming 1', 3, 500.00),
(5, 'Physics 1', 2, 500.00),
(6, 'Calculus 1', 2, 500.00),
(7, 'Math 1', 2, 500.00),
(8, 'IT Fundamentals', 2, 500.00),
(9, 'English 1', 1, 500.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `enrolled_subjects`
--
ALTER TABLE `enrolled_subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `onlineapplication`
--
ALTER TABLE `onlineapplication`
  ADD PRIMARY KEY (`application_id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `student_payments`
--
ALTER TABLE `student_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `enrolled_subjects`
--
ALTER TABLE `enrolled_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `student_payments`
--
ALTER TABLE `student_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
