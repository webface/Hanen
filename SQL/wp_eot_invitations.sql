-- phpMyAdmin SQL Dump
-- version 4.7.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 19, 2017 at 03:32 PM
-- Server version: 5.6.35
-- PHP Version: 5.5.38

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eot_v5`
--

-- --------------------------------------------------------

--
-- Table structure for table `wp_eot_invitations`
--

CREATE TABLE `wp_eot_invitations` (
  `id` int(11) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `org_id` int(11) DEFAULT NULL,
  `subscription_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `date_signed_up` datetime DEFAULT NULL,
  `type` enum('user','course','camp') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `wp_eot_invitations`
--

INSERT INTO `wp_eot_invitations` (`id`, `code`, `org_id`, `subscription_id`, `course_id`, `user_email`, `date`, `date_signed_up`, `type`) VALUES
(1, 'b7954261ff50b54ae3b777f2301a79f9', 692, 26, 15, '4@intellitechent.com', '2017-05-17 00:00:00', NULL, 'user'),
(2, '102c46c928b447cab2275f1197ba7c39', 692, 26, 0, '7@intellitechent.com', '2017-05-17 00:00:00', '2017-05-17 00:00:00', 'user'),
(3, 'b267cc4e68c01f267f2da097b342ab27', 692, 26, 0, '8@intellitechent.com', '2017-05-17 00:00:00', NULL, 'user'),
(4, '6ca9046ec3637409405edfa779fbdac3', 692, 26, 0, '9@intellitechent.com', '2017-05-17 00:00:00', NULL, 'user'),
(5, 'b96e34825de6e2245672a9ef02e69962', 692, 26, 0, '10@intellitechent.com', '2017-05-17 00:00:00', NULL, 'user'),
(6, 'b1249f73c709393a47fd8efa728534e715', 692, 26, 15, NULL, '2017-05-17 00:00:00', '2017-05-17 00:00:00', 'course'),
(7, '5895e696455aba2099424b1567a6fed4', 692, 26, 15, '20@intellitechent.com', '2017-05-18 00:00:00', NULL, 'user'),
(8, '1d4603cfcc72872c609f9692a56fca51', 692, 26, 15, '21@intellitechent.com', '2017-05-18 00:00:00', NULL, 'user'),
(9, '9157637b4ef4b02e27363b4dd06e4925', 692, 26, 15, '22@intellitechent.com', '2017-05-18 00:00:00', NULL, 'user'),
(10, '896cf81165d064df1efe1ad74d387300', 692, 26, 15, '23@intellitechent.com', '2017-05-18 00:00:00', NULL, 'user'),
(11, '2625e916607cf6bf1137e89047662b68', 692, 26, 15, '24@intellitechent.com', '2017-05-18 00:00:00', '2017-05-18 00:00:00', 'user'),
(12, 'd849bf7695e2cd2f833bea3745d714ae', 692, 26, 15, '25@intellitechent.com', '2017-05-18 00:00:00', '2017-05-18 00:00:00', 'user'),
(13, 'dbc7d1c831c108815697f407d46b437e', 692, 38, 0, '26@intellitechent.com', '2017-05-18 00:00:00', '2017-05-18 00:00:00', 'user'),
(14, '6acd3a6cb0646a6905b2fb9f716691d4', 692, 38, 0, '27@intellitechent.com', '2017-05-18 00:00:00', '2017-05-18 00:00:00', 'user'),
(15, '36ec4c200dd0bed88a1187b1e1677d8d', 692, 38, 0, '28@intellitechent.com', '2017-05-18 00:00:00', NULL, 'user'),
(16, '3db2b36ad669572b12a62226ee607731', 692, 38, 0, '29@intellitechent.com', '2017-05-18 00:00:00', '2017-05-18 00:00:00', 'user'),
(17, '53d5177e0763389c6570849aeb9c8faf', 692, 38, 0, '30@intellitechent.com', '2017-05-18 00:00:00', NULL, 'user'),
(18, 'c7f69ac1d567a55e2615ae10cceb7acf', 692, 38, 19, '28@intellitechent.com', '2017-05-18 00:00:00', '2017-05-18 00:00:00', 'user'),
(19, 'b1249f73c709393a47fd8efa728534e719', 692, 38, 19, NULL, '2017-05-18 00:00:00', NULL, 'course'),
(20, 'fc5525fef7e3eae67232be0ebbc9450b', 692, 38, 19, '31@intellitechent.com', '2017-05-18 00:00:00', NULL, 'user'),
(21, '520ecd07950c9997ac2ac244b91e35ac', 692, 26, 13, '31@intellitechent.com', '2017-05-18 00:00:00', '2017-05-18 00:00:00', 'user'),
(22, '2966209c01753ad8c7adb8575e4a6346', 692, 26, 13, '32@intellitechent.com', '2017-05-18 00:00:00', '2017-05-18 00:00:00', 'user'),
(23, 'c190e82a82c8b9ef57747a3297026b9e', 692, 26, 16, '33@intellitechent.com', '2017-05-18 00:00:00', '2017-05-18 00:00:00', 'user'),
(24, '31732e0026c45dab0131bfc407e7c579', 2800, 57, 20, '40@intellitechent.com', '2017-05-18 00:00:00', '2017-05-18 00:00:00', 'user'),
(25, '500b77ce7f49fcb8ed82978d81624665', 2800, 57, 20, '41@intellitechent.com', '2017-05-18 00:00:00', NULL, 'user'),
(26, 'ed898a9eb927a4a85cff6b7a1af7aaa3', 2800, 57, 20, '42@intellitechent.com', '2017-05-18 00:00:00', NULL, 'user'),
(27, '0be6bd58baf57e36172f9ffdfca11b59', 2800, 57, 20, '43@intellitechent.com', '2017-05-18 00:00:00', NULL, 'user'),
(28, '4628a4a1bfd09f59198f34a1799b2acb', 2800, 57, 20, '44@intellitechent.com', '2017-05-18 00:00:00', NULL, 'user'),
(29, '9f1d7baf042929422e6ad329e3e749e8', 2800, 57, 20, '45@intellitechent.com', '2017-05-18 00:00:00', NULL, 'user'),
(30, '601c45a01eed09e5ec7675a4c161dfdf', 2825, 93, 96, 'bumblebee@intellitechent.com', '2017-05-24 00:00:00', '2017-05-24 00:00:00', 'user'),
(31, '077867378daf316c6b8fb50e16030505', 692, 26, 13, 'tommyadeniyi@gmail.com', '2017-06-06 00:00:00', NULL, 'user'),
(32, '6199661a3d04889c7d2e3b5552e30fa3', 692, 26, 0, 'tommyadeniyi@hotmail.com', '2017-06-12 00:00:00', NULL, 'user'),
(33, 'd31cdf0f25e0845b505ecb75c142172b', 692, 26, 0, 'tommyadeniyi@home.com', '2017-06-12 00:00:00', NULL, 'user'),
(34, 'fe89acb0f2a04a2b47476cc41065c0d0', 692, 26, 0, 'suleyman@intellitechent.com', '2017-06-12 00:00:00', NULL, 'user'),
(35, 'a7f31a523f4dace60c078b9ab3c40a66', 692, 26, 0, 'megatron@gmail.com', '2017-06-12 00:00:00', NULL, 'user'),
(36, '351707564df9b1d737ca9c683288d7b1', 692, 26, 13, 'sam@intellitechent.com', '2017-06-12 00:00:00', NULL, 'user'),
(37, 'b3db2afa4fae3b7207600bb8b771ba65', 692, 26, 13, 'john@sample.com', '2017-06-12 00:00:00', NULL, 'user'),
(38, '1ac11cc017b4940b33f41a89fc37f0f8', 692, 26, 13, 'jane@email.com', '2017-06-12 00:00:00', NULL, 'user'),
(39, 'b3db2afa4fae3b7207600bb8b771ba65', 692, 26, 13, 'john@sample.com', '2017-06-12 00:00:00', NULL, 'user'),
(40, '1ac11cc017b4940b33f41a89fc37f0f8', 692, 26, 13, 'jane@email.com', '2017-06-12 00:00:00', NULL, 'user'),
(41, '8d79b0805fa4622d395a4b53bcbdad20', 692, 26, 13, 'tommyadeniyi@hotmail.com', '2017-06-12 00:00:00', NULL, 'user'),
(42, '7fe4dd99be5f74cf5651c11308d582da', 692, 26, 0, 'tommy@intellitechent.com', '2017-06-12 00:00:00', NULL, 'user'),
(43, 'd7d354eab44045dcabcae906a6756571', 692, 26, 15, 'john@sample.com', '2017-06-12 00:00:00', NULL, 'user'),
(44, 'd9fe754cc8793604bea27f418a3a2241', 692, 26, 15, 'jane@email.com', '2017-06-12 00:00:00', NULL, 'user'),
(45, 'deb93d546b87738c78f478e3aa562b59', 692, 26, 0, 'john@sample.com', '2017-06-12 00:00:00', NULL, 'user'),
(46, '619711a6aed46599cce0627793db8124', 692, 26, 0, 'jane@email.com', '2017-06-12 00:00:00', NULL, 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `wp_eot_invitations`
--
ALTER TABLE `wp_eot_invitations`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `wp_eot_invitations`
--
ALTER TABLE `wp_eot_invitations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
