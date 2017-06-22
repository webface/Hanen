-- phpMyAdmin SQL Dump
-- version 4.7.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 22, 2017 at 03:31 PM
-- Server version: 5.6.35
-- PHP Version: 5.5.38

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `eot_v5`
--

-- --------------------------------------------------------

--
-- Table structure for table `wp_eot_modules_resources`
--

CREATE TABLE `wp_eot_modules_resources` (
  `id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `resource_type` varchar(45) DEFAULT NULL,
  `order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `wp_eot_modules_resources`
--

INSERT INTO `wp_eot_modules_resources` (`id`, `module_id`, `resource_id`, `resource_type`, `order`) VALUES
(1, 15, 1, 'exam', 1),
(2, 19, 2, 'exam', 1),
(3, 83, 3, 'exam', 1),
(4, 87, 4, 'exam', 1),
(5, 125, 5, 'exam', 1),
(6, 129, 6, 'exam', 1),
(7, 133, 7, 'exam', 1),
(8, 142, 8, 'exam', 1),
(9, 152, 9, 'exam', 1),
(10, 157, 10, 'exam', 1),
(11, 165, 11, 'exam', 1),
(12, 169, 13, 'exam', 1),
(13, 173, 14, 'exam', 1),
(14, 230, 15, 'exam', 1),
(15, 234, 16, 'exam', 1),
(16, 314, 17, 'exam', 1),
(17, 367, 18, 'exam', 1),
(18, 372, 19, 'exam', 1),
(19, 420, 20, 'exam', 1),
(20, 447, 21, 'exam', 1),
(21, 451, 22, 'exam', 1),
(22, 35, 23, 'exam', 1),
(23, 39, 24, 'exam', 1),
(24, 71, 25, 'exam', 1),
(25, 75, 26, 'exam', 1),
(26, 101, 27, 'exam', 1),
(27, 105, 28, 'exam', 1),
(28, 109, 29, 'exam', 1),
(29, 113, 30, 'exam', 1),
(30, 147, 31, 'exam', 1),
(31, 200, 32, 'exam', 1),
(32, 206, 33, 'exam', 1),
(33, 210, 34, 'exam', 1),
(34, 244, 35, 'exam', 1),
(35, 278, 36, 'exam', 1),
(36, 307, 37, 'exam', 1),
(37, 324, 38, 'exam', 1),
(38, 331, 39, 'exam', 1),
(39, 343, 40, 'exam', 1),
(40, 357, 41, 'exam', 1),
(41, 410, 42, 'exam', 1),
(42, 475, 43, 'exam', 1),
(43, 59, 44, 'exam', 1),
(44, 63, 45, 'exam', 1),
(45, 93, 46, 'exam', 1),
(46, 97, 47, 'exam', 1),
(47, 121, 48, 'exam', 1),
(48, 239, 49, 'exam', 1),
(49, 248, 50, 'exam', 1),
(50, 255, 51, 'exam', 1),
(51, 261, 52, 'exam', 1),
(52, 270, 53, 'exam', 1),
(53, 274, 54, 'exam', 1),
(54, 297, 55, 'exam', 1),
(55, 338, 56, 'exam', 1),
(56, 398, 57, 'exam', 1),
(57, 405, 58, 'exam', 1),
(58, 2, 59, 'exam', 1),
(59, 9, 62, 'exam', 1),
(60, 43, 61, 'exam', 1),
(61, 53, 62, 'exam', 1),
(62, 117, 63, 'exam', 1),
(63, 137, 64, 'exam', 1),
(64, 163, 65, 'exam', 1),
(65, 189, 66, 'exam', 1),
(66, 194, 67, 'exam', 1),
(67, 217, 68, 'exam', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `wp_eot_modules_resources`
--
ALTER TABLE `wp_eot_modules_resources`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `wp_eot_modules_resources`
--
ALTER TABLE `wp_eot_modules_resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;