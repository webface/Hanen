-- phpMyAdmin SQL Dump
-- version 4.7.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 22, 2017 at 03:28 PM
-- Server version: 5.6.35
-- PHP Version: 5.5.38

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `eot_v5`
--

-- --------------------------------------------------------

--
-- Table structure for table `wp_eot_quiz`
--

CREATE TABLE `wp_eot_quiz` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` mediumtext,
  `org_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `passing_score` int(11) DEFAULT NULL,
  `num_attempts` int(11) DEFAULT NULL,
  `time_limit` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `wp_eot_quiz`
--

INSERT INTO `wp_eot_quiz` (`id`, `name`, `description`, `org_id`, `user_id`, `date_created`, `passing_score`, `num_attempts`, `time_limit`) VALUES
(1, 'Advanced Skills for Working with Difficult Parents, Part I', 'Advanced Skills for Working with Difficult Parents, Part I', 692, 88, '2017-06-13 00:00:00', NULL, 0, '00:20:00'),
(2, 'Advanced Skills for Working with Difficult Parents, Part II', 'Advanced Skills for Working with Difficult Parents, Part II', 692, 88, '2017-06-13 00:00:00', NULL, 0, '00:20:00'),
(3, 'Best Boys, Part I', 'Best Boys, Part I', 692, 88, '2017-06-13 00:00:00', NULL, 0, '00:20:00'),
(4, 'Best Boys, Part II', 'Best Boys, Part II', 692, 88, '2017-06-13 00:00:00', NULL, 0, '00:20:00'),
(5, 'Christian Perspectives, Part 1', 'Christian Perspectives, Part 1', 692, 88, '2017-06-13 00:00:00', NULL, 0, '00:20:00'),
(6, 'Christian Perspectives, Part 2', 'Christian Perspectives, Part 2', 692, 88, '2017-06-13 00:00:00', NULL, 0, '00:20:00'),
(7, 'Classic Problem Solving', 'Classic Problem Solving', 692, 88, '2017-06-13 00:00:00', NULL, 0, '00:20:00'),
(8, 'Cracking Kids’ Secret Code', 'Cracking Kids’ Secret Code', 692, 88, '2017-06-14 00:00:00', NULL, 0, '00:20:00'),
(9, 'Cultural Competence in Youth Programs, Part I', 'Cultural Competence in Youth Programs, Part I', 692, 88, '2017-06-14 00:00:00', NULL, 0, '00:20:00'),
(10, 'Cultural Competence in Youth Programs, Part II', 'Cultural Competence in Youth Programs, Part II', 692, 88, '2017-06-14 00:00:00', NULL, 0, '00:20:00'),
(11, 'Day Camp Dynamics, Part I', 'Day Camp Dynamics, Part I', 692, 88, '2017-06-14 00:00:00', 0, 0, '00:20:00'),
(13, 'Day Camp Dynamics, Part II', 'Day Camp Dynamics, Part II', 692, 88, '2017-06-14 00:00:00', NULL, 0, '00:20:00'),
(14, 'Day Camp Dynamics, Part III', 'Day Camp Dynamics, Part III', 692, 88, '2017-06-14 00:00:00', NULL, 0, '00:20:00'),
(15, 'Get the Best and Forget the Rest', 'Get the Best and Forget the Rest', 692, 88, '2017-06-14 00:00:00', NULL, 0, '00:20:00'),
(16, 'Girl Power: Guiding Principles of Strong Female Leadership', 'Girl Power: Guiding Principles of Strong Female Leadership', 692, 88, '2017-06-14 00:00:00', NULL, 0, '00:20:00'),
(17, 'No Losers', 'No Losers', 692, 88, '2017-06-14 00:00:00', NULL, 0, '00:20:00'),
(18, 'Respect is Learned, Not Given', 'Respect is Learned, Not Given', 692, 88, '2017-06-14 00:00:00', NULL, 0, '00:20:00'),
(19, 'Rules Were Made to Be Positive', 'Rules Were Made to Be Positive', 692, 88, '2017-06-14 00:00:00', NULL, 0, '00:20:00'),
(20, 'Stop Yelling: Get the HINT', 'Stop Yelling: Get the HINT', 692, 88, '2017-06-14 00:00:00', NULL, 0, '00:20:00'),
(21, 'We Squashed It! - Physical Aggression', 'We Squashed It! - Physical Aggression', 692, 88, '2017-06-14 00:00:00', NULL, 0, '00:20:00'),
(22, 'We Squashed it! - Relational Aggression', 'We Squashed it! - Relational Aggression', 692, 88, '2017-06-14 00:00:00', NULL, 0, '00:20:00'),
(23, 'Afterschool Program Success, Part I', 'Afterschool Program Success, Part I', 692, 88, '2017-06-14 00:00:00', NULL, 0, '00:20:00'),
(24, 'Afterschool Program Success, Part II', 'Afterschool Program Success, Part II', 692, 88, '2017-06-14 00:00:00', NULL, 0, '00:20:00'),
(25, 'Becoming a Youth Development Professional, Part I', 'Becoming a Youth Development Professional, Part I', 692, 88, '2017-06-14 00:00:00', 0, 0, '00:20:00'),
(26, 'Becoming a Youth Development Professional, Part II', 'Becoming a Youth Development Professional, Part II', 692, 88, '2017-06-14 00:00:00', NULL, 0, '00:20:00'),
(27, 'Burn Specific Programming, Part I', 'Burn Specific Programming, Part I', 692, 88, '2017-06-15 00:00:00', NULL, 0, '00:20:00'),
(28, 'Burn Specific Programming, Part 2', 'Burn Specific Programming, Part 2', 692, 88, '2017-06-15 00:00:00', NULL, 0, '00:20:00'),
(29, 'Canoeing Success, Part I', 'Canoeing Success, Part I', 692, 88, '2017-06-15 00:00:00', 0, 0, '00:20:00'),
(30, 'Canoeing Success, Part II', 'Canoeing Success, Part II', 692, 88, '2017-06-15 00:00:00', NULL, 0, '00:20:00'),
(31, 'Cultivating Patience', 'Effective Debriefing Tools and Techniques', 692, 88, '2017-06-15 00:00:00', NULL, 0, '00:20:00'),
(32, 'Effective Debriefing Tools and Techniques', 'Effective Debriefing Tools and Techniques', 692, 88, '2017-06-15 00:00:00', NULL, 0, '00:20:00'),
(33, 'Equity and Diversity', 'Equity and Diversity', 692, 88, '2017-06-15 00:00:00', NULL, 0, '00:20:00'),
(34, 'Face-to-Face (Differently Abled Youth)', 'Face-to-Face (Differently Abled Youth)', 692, 88, '2017-06-15 00:00:00', NULL, 0, '00:20:00'),
(35, 'Hello Games', 'Hello Games', 692, 88, '2017-06-15 00:00:00', NULL, 0, '00:20:00'),
(36, 'Jewish Perspectives on Staff Happiness and Stamina', 'Jewish Perspectives on Staff Happiness and Stamina', 692, 88, '2017-06-15 00:00:00', NULL, 0, '00:20:00'),
(37, 'Move It Like You Mean It', 'Move It Like You Mean It', 692, 88, '2017-06-15 00:00:00', NULL, 0, '00:20:00'),
(38, 'Outdoor Cooking with Youth', 'Outdoor Cooking with Youth', 692, 88, '2017-06-16 00:00:00', NULL, 0, '00:20:00'),
(39, 'Playing with a Full Deck', 'Playing with a Full Deck', 692, 88, '2017-06-16 00:00:00', NULL, 0, '00:20:00'),
(40, 'Programming For All (Differently Abled Youth)', 'Programming For All (Differently Abled Youth)', 692, 88, '2017-06-16 00:00:00', NULL, 0, '00:20:00'),
(41, 'Rainy Day Games', 'Rainy Day Games', 692, 88, '2017-06-16 00:00:00', NULL, 0, '00:20:00'),
(42, 'Speaking of Camp', 'Speaking of Camp', 692, 88, '2017-06-16 00:00:00', NULL, 0, '00:20:00'),
(43, 'Youth Inspired', 'Youth Inspired', 692, 88, '2017-06-16 00:00:00', NULL, 0, '00:20:00'),
(44, 'Attention Deficit Hyperactivity Disorder', 'Attention Deficit Hyperactivity Disorder', 692, 88, '2017-06-16 00:00:00', NULL, 0, '00:20:00'),
(45, 'Autism Spectrum Disorder', 'Autism Spectrum Disorder', 692, 88, '2017-06-16 00:00:00', NULL, 0, '00:20:00'),
(46, 'Bullies and Targets, Part I', 'Bullies and Targets, Part I', 692, 88, '2017-06-16 00:00:00', NULL, 0, '00:20:00'),
(47, 'Bullies and Targets, Part II', 'Bullies and Targets, Part II', 692, 88, '2017-06-16 00:00:00', NULL, 0, '00:20:00'),
(48, 'Children with Verbal Learning Disabilities', 'Children with Verbal Learning Disabilities', 692, 88, '2017-06-16 00:00:00', NULL, 0, '00:20:00'),
(49, 'Good Sportsmanship vs. Foul Play', 'Good Sportsmanship vs. Foul Play', 692, 88, '2017-06-16 00:00:00', NULL, 0, '00:20:00'),
(50, 'Helping Awkward Children Fit In', 'Helping Awkward Children Fit In', 692, 88, '2017-06-19 00:00:00', NULL, 0, '00:20:00'),
(51, 'Homesickness at Day & Resident Camps, Part I', 'Homesickness at Day & Resident Camps, Part I', 692, 88, '2017-06-19 00:00:00', NULL, 0, '00:20:00'),
(52, 'Homesickness at Day & Resident Camps, Part II', 'Homesickness at Day & Resident Camps, Part II', 692, 88, '2017-06-19 00:00:00', NULL, 0, '00:20:00'),
(53, 'Jewish Perspectives on Child-Staff Relationships, Part I', 'Jewish Perspectives on Child-Staff Relationships, Part I', 692, 88, '2017-06-19 00:00:00', NULL, 0, '00:20:00'),
(54, 'Jewish Perspectives on Child-Staff Relationships, Part II', 'Jewish Perspectives on Child-Staff Relationships, Part II', 692, 88, '2017-06-19 00:00:00', NULL, 0, '00:20:00'),
(55, 'Listening with More Than Ears', 'Listening with More Than Ears', 692, 88, '2017-06-19 00:00:00', NULL, 0, '00:20:00'),
(56, 'Preventing Gossip and Relational Aggression', 'Preventing Gossip and Relational Aggression', 692, 88, '2017-06-19 00:00:00', NULL, 0, '00:20:00'),
(57, 'Skillful Discipline, Part I', 'Skillful Discipline, Part I', 692, 88, '2017-06-19 00:00:00', NULL, 0, '00:20:00'),
(58, 'Skillful Discipline, Part II', 'Skillful Discipline, Part II', 692, 88, '2017-06-19 00:00:00', NULL, 0, '00:20:00'),
(59, '15 Passenger Van Safety', '15 Passenger Van Safety', 692, 88, '2017-06-19 00:00:00', NULL, 0, '00:20:00'),
(60, 'Active Lifeguarding', 'Active Lifeguarding', 692, 88, '2017-06-19 00:00:00', NULL, 0, '00:20:00'),
(61, 'Alcohol Beverage Laws', 'Alcohol Beverage Laws', 692, 88, '2017-06-19 00:00:00', NULL, 0, '00:20:00'),
(62, 'Anaphylactic Emergencies', 'Anaphylactic Emergencies', 692, 88, '2017-06-19 00:00:00', NULL, 0, '00:20:00'),
(63, 'Child Welfare and Protection', 'Child Welfare and Protection', 692, 88, '2017-06-20 00:00:00', NULL, 0, '00:20:00'),
(64, 'Confidentiality for Youth Professionals', 'Confidentiality for Youth Professionals', 692, 88, '2017-06-20 00:00:00', NULL, 0, '00:20:00'),
(65, 'Cyberbullying & Sexting', 'Cyberbullying & Sexting', 692, 88, '2017-06-20 00:00:00', NULL, 0, '00:20:00'),
(66, 'Duty of Care, Part I', 'Duty of Care, Part I', 692, 88, '2017-06-20 00:00:00', NULL, 0, '00:20:00'),
(67, 'Duty of Care, Part II', 'Duty of Care, Part II', 692, 88, '2017-06-20 00:00:00', NULL, 0, '00:20:00'),
(68, 'Fire Building and Fire Safety', 'Fire Building and Fire Safety', 692, 88, '2017-06-20 00:00:00', NULL, 0, '00:20:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `wp_eot_quiz`
--
ALTER TABLE `wp_eot_quiz`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `wp_eot_quiz`
--
ALTER TABLE `wp_eot_quiz`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;