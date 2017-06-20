-- phpMyAdmin SQL Dump
-- version 4.7.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 19, 2017 at 03:37 PM
-- Server version: 5.6.35
-- PHP Version: 5.5.38

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eot_china_dev`
--

-- --------------------------------------------------------

--
-- Table structure for table `wp_eot_enrollments`
--

CREATE TABLE `wp_eot_enrollments` (
  `id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `org_id` int(11) NOT NULL,
  `status` enum('not_started','in_progress','completed','passed','failed','pending_review') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `wp_eot_enrollments`
--

INSERT INTO `wp_eot_enrollments` (`id`, `course_id`, `email`, `user_id`, `org_id`, `status`) VALUES
(1, 9, 'tommyadeniyi@expertonlinetraining.net', 130, 199, 'not_started'),
(2, 9, 'santosh@intellitechent.com', 124, 199, 'not_started'),
(3, 9, 'robert@intellitechent.com', 32, 199, 'not_started'),
(4, 9, 'ok5@intellitechent.com', 119, 199, 'not_started'),
(5, 9, 'fayemiguel@expertonlinetraining.com', 108, 199, 'not_started'),
(6, 9, 'elija@expertonlinetraining.com', 109, 199, 'not_started'),
(7, 9, 'chadsmythe@expertonlinetraining.com', 107, 199, 'not_started'),
(8, 3, 'tommyadeniyi@expertonlinetraining.net', 130, 199, 'not_started'),
(9, 3, 'santosh@intellitechent.com', 124, 199, 'not_started'),
(10, 3, 'robert@intellitechent.com', 32, 199, 'not_started'),
(11, 2, 'tommyadeniyi@expertonlinetraining.net', 130, 199, 'not_started'),
(12, 2, 'santosh@intellitechent.com', 124, 199, 'not_started'),
(13, 2, 'chadsmythe@expertonlinetraining.com', 107, 199, 'not_started'),
(14, 5, 'chadsmythe@expertonlinetraining.com', 107, 199, 'not_started'),
(15, 5, 'elija@expertonlinetraining.com', 109, 199, 'not_started'),
(16, 6, 'tommyadeniyi@expertonlinetraining.net', 130, 199, 'not_started'),
(17, 7, 'tommyadeniyi@gmail.com', 131, 199, 'not_started'),
(18, 7, 'tommyadeniyi@expertonlinetraining.net', 130, 199, 'not_started'),
(19, 10, 'tommyadeniyi@gmail.com', 131, 199, 'not_started'),
(20, 10, 'tommyadeniyi@expertonlinetraining.net', 130, 199, 'not_started'),
(21, 11, 'tommyadeniyi@gmail.com', 131, 199, 'not_started'),
(22, 9, 'tommyadeniyi@gmail.com', 131, 199, 'not_started'),
(23, 2, 'tommyadeniyi@gmail.com', 131, 199, 'not_started'),
(24, 13, 'ted@home.com', 137, 692, 'not_started'),
(27, NULL, 'sam@home.com', 152, 199, 'not_started'),
(28, NULL, 'joe@home.com', 153, 199, 'not_started'),
(29, 2, 'johnny@home.com', 155, 199, 'not_started'),
(30, 14, 'seanolmec@gmail.com', 162, 199, 'not_started'),
(31, 14, 'tommyadeniyi@hotmail.com', 163, 199, 'not_started'),
(32, 14, 'tommy@kaimeramedia.com', 164, 199, 'not_started'),
(33, 4, 'tom@intellitechent.com', 165, 692, 'not_started'),
(34, 17, 'tom@expertonline.com', 142, 692, 'not_started'),
(35, 17, 'tom@intellitechent.com', 165, 692, 'not_started'),
(36, 17, 'ted@home.com', 137, 692, 'not_started'),
(37, 17, 'steve@microsoft.com', 133, 692, 'not_started'),
(38, 16, 'steve@microsoft.com', 133, 692, 'not_started'),
(39, 16, 'ted@home.com', 137, 692, 'not_started'),
(40, 16, 'jerry@kramer.com', 141, 692, 'not_started'),
(41, 17, 'tommyadeniyi@gmail.com', 177, 692, 'not_started'),
(42, 0, '2@intellitechent.com', 168, 692, 'not_started'),
(43, 0, '3@intellitechent.com', 169, 692, 'not_started'),
(44, 15, '4@intellitechent.com', 170, 692, 'not_started'),
(45, 15, '24@intellitechent.com', 177, 692, 'not_started'),
(46, 15, '25@intellitechent.com', 178, 692, 'not_started'),
(47, 0, '26@intellitechent.com', 179, 692, 'not_started'),
(48, 0, '27@intellitechent.com', 180, 692, 'not_started'),
(49, 19, '28@intellitechent.com', 181, 692, 'not_started'),
(50, 19, '30@intellitechent.com', 183, 692, 'not_started'),
(51, 13, '31@intellitechent.com', 184, 692, 'not_started'),
(52, 20, '40@intellitechent.com', 188, 2800, 'not_started'),
(53, 96, 'bumblebee@intellitechent.com', 213, 2825, 'not_started'),
(54, 15, 'jimmy@intellitechent.com', 229, 692, ''),
(55, 15, 'samuel@intellitechent.com', 230, 692, ''),
(56, 13, 'reneli@intellitechent.com', 231, 692, ''),
(57, 13, 'trillium@intellitechent.com', 232, 692, ''),
(58, 15, '301@intellitechent.com', 233, 692, ''),
(59, 13, '302@intellitechent.com', 234, 692, ''),
(60, 13, 'seanolmec@gmail.com', 235, 692, ''),
(61, 13, 'tommyadeniyi@hotmail.com', 236, 692, ''),
(62, 13, 'webfacemedia@gmail.com', 237, 692, ''),
(63, 13, 'seanolmec@gmail.com', 235, 692, ''),
(64, 13, 'tommyadeniyi@hotmail.com', 236, 692, ''),
(65, 13, 'webfacemedia@gmail.com', 237, 692, ''),
(66, 13, 'seanolmec@gmail.com', 235, 692, ''),
(67, 13, 'tommyadeniyi@hotmail.com', 236, 692, ''),
(68, 13, 'webfacemedia@gmail.com', 237, 692, ''),
(108, 13, '310@intellitechent.com', 265, 692, ''),
(109, 15, '311@intellitechent.com', 266, 692, ''),
(110, 13, '312@intellitechent.com', 267, 692, ''),
(111, 13, '313@intellitechent.com', 268, 692, ''),
(112, 15, '314@intellitechent.com', 269, 692, ''),
(113, 13, '315@intellitechent.com', 270, 692, ''),
(114, 13, '316@intellitechent.com', 271, 692, ''),
(115, 13, '320@intellitechent.com', 272, 692, ''),
(116, 13, '323@intel.com', 273, 692, ''),
(117, 13, '326@rogers.com', 274, 692, ''),
(118, 15, '327@rogers.com', 275, 692, ''),
(119, 13, '328@rogers.com', 276, 692, ''),
(120, 13, '329@waterworks.com', 277, 692, ''),
(121, 15, '330@waterworks.com', 278, 692, ''),
(122, 13, '331@waterworks.com', 279, 692, ''),
(123, 13, '332@rogers.com', 280, 692, ''),
(124, 15, '333@rogers.com', 281, 692, ''),
(125, 13, '334@rogers.com', 282, 692, ''),
(126, 13, '338.rogers.com', NULL, 692, ''),
(127, 15, '339.rogers.com', NULL, 692, ''),
(128, 13, '340.rogers.com', NULL, 692, ''),
(129, 13, '341@rogers.com', 286, 692, ''),
(130, 13, '342@yonge.com', 287, 692, ''),
(131, 15, '343@yonge.com', 288, 692, ''),
(132, 13, '345@tommy.com', 289, 692, ''),
(133, 15, '346@tommy.com', 290, 692, ''),
(134, 13, '347@water.com', 291, 692, ''),
(135, 15, '348@water.com', 292, 692, ''),
(136, 13, '349@home.com', 293, 692, ''),
(137, 15, '350@home.com', 294, 692, ''),
(138, 13, '351@york.ca', 295, 692, ''),
(139, 15, '352@york.ca', 296, 692, ''),
(140, 13, '353@york.ca', 297, 692, ''),
(141, 13, '354@thornhill.com', 298, 692, ''),
(142, 15, '355@thornhill.com', 299, 692, ''),
(143, 13, '356@thornhill.com', 300, 692, ''),
(144, 13, '357@toronto.net', 301, 692, ''),
(145, 15, '358@toronto.net', 302, 692, ''),
(146, 13, '359@toronto.net', 303, 692, ''),
(147, 13, '360@camp.com', 304, 692, ''),
(148, 15, '361@camp.com', 305, 692, ''),
(149, 13, '362@camp.com', 306, 692, ''),
(150, 16, '363@camp.com', 307, 692, ''),
(151, 16, '364@recksplace.com', 308, 692, ''),
(152, 242, '365@camp.com', 309, 199, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `wp_eot_enrollments`
--
ALTER TABLE `wp_eot_enrollments`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `wp_eot_enrollments`
--
ALTER TABLE `wp_eot_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
