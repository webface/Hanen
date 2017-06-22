-- phpMyAdmin SQL Dump
-- version 4.7.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 22, 2017 at 04:26 PM
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
-- Table structure for table `wp_eot_resources`
--

CREATE TABLE `wp_eot_resources` (
  `id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `order` int(11) NOT NULL DEFAULT '10',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `date` date NOT NULL,
  `url` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `wp_eot_resources`
--

INSERT INTO `wp_eot_resources` (`id`, `module_id`, `video_id`, `name`, `order`, `active`, `date`, `url`) VALUES
(1, 2, 140, '15 Passenger Van Safety Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_15-Passenger-Van-Safety.pdf'),
(2, 5, 141, 'Achieving Supervisory Balance Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Achieving-Supervisory-Bal.pdf'),
(3, 9, 18, 'Active Lifegaurding', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Active-Lifeguarding.pdf'),
(4, 15, 19, 'Advanced Skills for Working with Difficult Parents, Part I Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Advanced-Skills-for-Parents-Parts-I-and-II.pdf'),
(5, 19, 20, 'Advanced Skills for Working with Difficult Parents, Part II Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Advanced-Skills-for-Parents-Parts-I-and-II.pdf'),
(6, 24, 15, 'Advanced Staff Supervision, Part I Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Advanced-Staff-Supervision-Parts-I-and-II.pdf'),
(7, 29, 16, 'Advanced Staff Supervision, Part II Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Advanced-Staff-Supervision-Parts-I-and-II.pdf'),
(8, 35, 118, 'Afterschool Program Success, Part I Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Afterschool-Program-Success-Part-I.pdf'),
(9, 39, 119, 'Afterschool Program Success, Part II Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Afterschool-Program-Success-Part-II.pdf'),
(10, 43, 77, 'Alcohol Beverage Laws Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Alcohol-Beverage-Laws.pdf'),
(11, 53, 114, 'Anaphylactic Emergencies Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Anaphylactic-Emergencies.pdf'),
(12, 59, 24, 'Attention Deficit Hyperactivity Disorder Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Attention-Deficit-Hyperactivity-Disorder.pdf'),
(13, 63, 126, 'Autism Spectrum Disorder Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Autism-Spectrum-Disorder.pdf'),
(14, 66, 146, 'Awesome Archery, Part I Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Awesome-Archery-Part-I.pdf'),
(15, 68, 147, 'Awesome Archery, Part II Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Awesome-Archery-Part-II.pdf'),
(16, 71, 21, 'Becoming a Youth Development Professional, Part I Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Becoming-a-Youth-Development-Professional-Part-I.pdf'),
(17, 75, 135, 'Becoming a Youth Development Professional, Part II Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Becoming-a-Youth-Development-Professional-Part-II.pdf'),
(18, 79, 90, 'Behavior-Based Interviewing Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Behavior-Based-Interviewing.pdf'),
(19, 83, 91, 'Best Boys, Part I Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Best-Boys-Part-I.pdf'),
(20, 87, 92, 'Best Boys, Part II Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Best-Boys-Part-II.pdf'),
(21, 93, 22, 'Bullies and Targets, Part I Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Bullies-and-Targets-Part-I.pdf'),
(22, 97, 76, 'Bullies and Targets, Part II Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Bullies-and-Targets-Part-II.pdf'),
(23, 101, 110, 'Burn Specific Programming, Part I Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Burn-Specific-Programming-Part-I.pdf'),
(24, 105, 120, 'Burn Specific Programming, Part II Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Burn-Specific-Programming-Part-II.pdf'),
(25, 109, 116, 'Canoeing Success, Part I Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Canoeing-Success-Part-I.pdf'),
(26, 117, 98, 'Child Welfare and Protection Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Child-Welfare-_-Protection-Packet.pdf'),
(27, 121, 11, 'Children with Verbal Learning Disabilities Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Children-with-Verbal-Learning-Disabilities.pdf'),
(28, 125, 129, 'Christian Perspectives, Part I Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Christian-Perspectives-Part-I.pdf'),
(29, 129, 130, 'Christian Perspectives, Part II Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Christian-Perspectives-Part-II.pdf'),
(30, 133, 25, 'Classic Problem Solving Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Classic-Problem-Solving.pdf'),
(31, 137, 65, 'Confidentiality for Youth Professionals Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Confidentiality-for-Youth-Professionals.pdf'),
(32, 142, 107, 'Cracking Kids Secret Code Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Cracking-Kids-Code.pdf'),
(33, 147, 62, 'Cultivating Patience Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Cultivating-Patience.pdf'),
(34, 152, 82, 'Cultural Competence in Youth Programs, Part I Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Cultural-Competence-in-Youth-Programs-Part-I.pdf'),
(35, 157, 113, 'Cultural Competence in Youth Programs, Part II Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Cultural-Competence-in-Youth-Programs-Part-II.pdf'),
(36, 163, 78, 'Cyberbullying and Sexting Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Cyberbullying-and-Sexting.pdf'),
(37, 165, 66, 'Day Camp Dynamics, Part I Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Day-Camp-Dynamics-Part-I.pdf'),
(38, 169, 67, 'Day Camp Dynamics, Part II Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Day-Camp-Dynamics-Part-II.pdf'),
(39, 173, 84, 'Day Camp Dynamics, Part III Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Day-Camp-Dynamics-Part-III.pdf'),
(40, 179, 63, 'Difficult Management Conversations, Part I Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Difficult-Management-Conversations-Part-I.pdf'),
(41, 183, 134, 'Difficult Management Conversations, Part II Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Difficult-Management-Conversations-Part-II.pdf'),
(42, 189, 73, 'Duty of Care, Part I Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Duty-of-Care-Part-I.pdf'),
(43, 194, 74, 'Duty of Care, Part II Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Duty-of-Care-Part-II.pdf'),
(44, 200, 79, 'Effective Debriefing Tools and Techniques Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Effective-Debriefing-Tools-and-Techniques.pdf'),
(45, 206, 26, 'Equity and Diversity Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Equity-and-Diversity.pdf'),
(46, 210, 86, 'Face to Face Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Face-to-Face.pdf'),
(47, 217, 88, 'Fire Building and Fire Safety Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Fire-Building-and-Fire-Safety.pdf'),
(48, 222, 111, 'Fire Evacuation Planning Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Fire-Evacuation-Planning.pdf'),
(49, 226, 89, 'Firing a Staff Member Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Firing-a-Staff-Member.pdf'),
(50, 230, 125, 'Get the Best and Forget the Rest Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Get-the-Best-and-Forget-the-Rest.pdf'),
(51, 234, 81, 'Girl Power Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Girl-Power.pdf'),
(52, 239, 68, 'Good Sportsmanship vs. Foul Play Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Good-Sportsmanship-vs.-Foul-Play.pdf'),
(53, 244, 14, 'Hello Games Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Hello-Games.pdf'),
(54, 248, 12, 'Helping Awkward Children Fit In Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Helping-Awkward-Children-Fit-In.pdf'),
(55, 255, 31, 'Homesickness at Day & Resident Camps, Part I Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Homesickness-at-Day-and-Resident-Part-I.pdf'),
(56, 261, 72, 'Homesickness at Day & Resident Camps, Part II Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Homesickness-at-Day-and-Resident-Part-II.pdf'),
(57, 270, 96, 'Jewish Perspectives on Child-Staff Relationships, Part I Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Jewish-Perspectives-on-Child-Staff-Relationships-Part-I.pdf'),
(58, 274, 112, 'Jewish Perspectives on Child-Staff Relationships, Part II Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Jewish-Perspectives-on-Child-Staff-Relationships-Part-II.pdf'),
(59, 278, 97, 'Jewish Perspectives on Staff Happiness and Stamina Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Jewish-Perspectives-on-Staff-Happiness-and-Stamina.pdf'),
(60, 286, 136, 'Kickin Kitchens Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Kickin’-Kitchens.pdf'),
(61, 292, 93, 'Lifeguarding Skills Verification Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Lifeguarding-Skills-Verification.pdf'),
(62, 297, 139, 'Listening with More Than Ears Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Listening-with-More-Than-Ears.pdf'),
(63, 300, 143, 'Masterful Meetings Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Masterful-Meetings.pdf'),
(64, 303, 128, 'Medical Preparation for Burn Camps Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Medical-Preparation-for-Burn-Camps.pdf'),
(65, 307, 60, 'Move It Like You Mean It Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Move-It-Like-You-Mean-It.pdf'),
(66, 314, 138, 'No Losers Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_No-Losers.pdf'),
(67, 318, 95, 'Out of the Pool and Into the Wild Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Out-of-the-Pool-and-into-the-Wild.pdf'),
(68, 324, 87, 'Outdoor Cooking with Youth Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Outdoor-Cooking-with-Youth.pdf'),
(69, 328, 142, 'Personality Synergy Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Personality-Synergy.pdf'),
(70, 331, 80, 'Playing with a Full Deck Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Playing-with-a-Full-Deck.pdf'),
(71, 338, 69, 'Preventing Gossip and Relational Aggression Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Preventing-Gossip-_-Relational-Aggression.pdf'),
(72, 343, 85, 'Programming for All Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Programming-For-All.pdf'),
(73, 348, 27, 'Providing Effective Feedback, Part I Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Providing-Effective-Feedback-Part-I.pdf'),
(74, 352, 133, 'Providing Effective Feedback, Part II Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Providing-Effective-Feedback-Part-II.pdf'),
(75, 357, 13, 'Rainy Day Games Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Rainy-Day-Games.pdf'),
(76, 362, 127, 'Regional Disaster Management for Youth Programs Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Regional-Disaster-Mgt-Packet.pdf'),
(77, 367, 137, 'Respect is Learned, Not Given Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Respect-Learned-Not-Given.pdf'),
(78, 372, 10, 'Rules were Made to Be Positive Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Rules-Were-Made-to-be-Positive.pdf'),
(79, 377, 28, 'Safe Touch & Safe Talk Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Safe-Touch-_-Safe-Talk.pdf'),
(80, 388, 64, 'Sexual Harassment Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Sexual-Harassment.pdf'),
(81, 392, 144, 'Shockingly Professional Talk, Part I Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Shockingly-Professional-Part-I.pdf'),
(82, 394, 145, 'Shockingly Professional Talk, Part II Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Shockingly-Professional-Part-II.pdf'),
(83, 398, 29, 'Skillful Discipline, Part I Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Skillful-Discipline-Part-I.pdf'),
(84, 405, 75, 'Skillful Discipline, Part II Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Skillful-Discipline-Part-II.pdf'),
(85, 410, 70, 'Speaking of Camp Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Speaking-of-Camp.pdf'),
(86, 416, 23, 'Staff Use of the Internet Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Staff-Use-of-the-Internet.pdf'),
(87, 420, 30, 'Stop Yelling Get the HINT Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Stop-Yelling-Get-the-HINT.pdf'),
(88, 427, 17, 'Supervising Junior Leaders Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Supervising-Junior-Leaders.pdf'),
(89, 433, 94, 'Swim Checks in Action Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Swim-Checks-in-Action.pdf'),
(90, 440, 108, 'Waterfront Safety Design Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Waterfront-Safety-Design.pdf'),
(91, 447, 9, 'We Squashed It! - Physical Agression Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_We-Squashed-It-Physical-Aggression-Part-I.pdf'),
(92, 451, 132, 'We Squashed It! - Relational Agression Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_We-Squashed-It-Relational-Aggression-Part-II.pdf'),
(93, 455, 83, 'Weather Watch Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Weather-Watch.pdf'),
(94, 460, 115, 'Wilderness Wellness Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Wilderness-Wellness.pdf'),
(95, 464, 61, 'Winning Ways of Skilled Supervisors Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Winning-Ways-of-Skilled-Supervisors.pdf'),
(96, 469, 32, 'Wise Use of Time Off Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Wise-Use-of-Time-Off.pdf'),
(97, 475, 109, 'Youth Inspired Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Youth-Inspired.pdf'),
(98, 113, 117, 'Canoeing Success, Part II Handout', 1, 1, '2016-12-12', 'https://eot.staging.wpengine.com/wp-content/uploads/2014/09/Notes_Canoeing-Success-Part-II-1.pdf'),
(99, 0, 118, 'Cast a Wider Net', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2015/06/16/cast-a-wider-net?rq=after-school'),
(100, 0, 119, 'Cast a Wider Net', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2015/06/16/cast-a-wider-net?rq=after-school'),
(101, 0, 21, 'A Focused Group', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2010/05/17/a-focused-group?rq=staff%20training'),
(102, 0, 135, 'A Focused Group', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2010/05/17/a-focused-group?rq=staff%20training'),
(103, 0, 21, 'Overcoming Mid-Season Slump', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2011/07/01/overcoming-mid-season-slump?rq=staff%20training'),
(104, 0, 135, 'Overcoming Mid-Season Slump', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2011/07/01/overcoming-mid-season-slump?rq=staff%20training'),
(105, 0, 21, 'The Right Foot', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2002/05/18/the-right-foot?rq=staff%20training'),
(106, 0, 135, 'The Right Foot', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2002/05/18/the-right-foot?rq=staff%20training'),
(107, 0, 116, 'RiverTrek', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2016/6/rivertrek?rq=canoe'),
(108, 0, 117, 'RiverTrek', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2016/6/rivertrek?rq=canoe'),
(109, 0, 116, 'A Place to Touch Base', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2012/01/02/a-place-to-touch-base?rq=canoe'),
(110, 0, 117, 'A Place to Touch Base', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2012/01/02/a-place-to-touch-base?rq=canoe'),
(111, 0, 62, 'When Tempers Flare', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2014/05/06/when-tempers-flare?rq=calm'),
(112, 0, 26, 'Strength in Diversity', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2005/05/18/strength-in-diversity?rq=diversity'),
(113, 0, 26, 'The Difference Diversity Makes', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2013/11/01/the-difference-diversity-makes?rq=diversity'),
(114, 0, 26, 'Camp and The Diversity Of Experience', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2012/02/17/camp-and-the-diversity-of-experience?rq=diversity'),
(115, 0, 86, 'Training Staff To Work With Special-Needs Campers', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2014/10/27/training-staff-to-work-with-special-needs-campers?rq=special%20needs'),
(116, 0, 86, 'Additional special-needs articles', 10, 1, '2016-12-23', 'http://www.campbusiness.com/search?q=special+needs'),
(117, 0, 14, 'Games articles', 10, 1, '2016-12-23', 'http://www.campbusiness.com/search?q=games'),
(118, 0, 60, 'Communication articles', 10, 1, '2016-12-23', 'http://www.campbusiness.com/search?q=communication'),
(119, 0, 87, 'Outdoor Cooking', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2016/9/outdoor-cooking?rq=outdoor%20cooking'),
(120, 0, 80, 'The Value of No/Low-Prop Initiative Games', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2014/05/06/the-value-of-nolow-prop-initiative-games?rq=low%20prop%20games'),
(121, 0, 13, 'Group Games', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2005/04/14/group-games?rq=rainy%20day'),
(122, 0, 70, 'Parents articles', 10, 1, '2016-12-23', 'http://www.campbusiness.com/search?q=parents'),
(123, 0, 24, 'Problem Behavior at Camp', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2002/05/18/problem-behavior-at-camp?rq=behavior'),
(124, 0, 126, 'Accepting Differences', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2016/10/accepting-differences?rq=autism'),
(125, 0, 126, 'Children On The Autistic Spectrum', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2013/05/02/children-on-the-autistic-spectrum?rq=autism'),
(126, 0, 22, 'Bully articles', 10, 1, '2016-12-23', 'http://www.campbusiness.com/search?q=bully'),
(127, 0, 76, 'Bully articles', 10, 1, '2016-12-23', 'http://www.campbusiness.com/search?q=bully'),
(128, 0, 68, 'Sportsmanship articles', 10, 1, '2016-12-23', 'http://www.campbusiness.com/search?q=sportsmanship'),
(129, 0, 31, 'Homesick articles', 10, 1, '2016-12-23', 'http://www.campbusiness.com/search?q=homesick'),
(130, 0, 72, 'Homesick articles', 10, 1, '2016-12-23', 'http://www.campbusiness.com/search?q=homesick'),
(131, 0, 139, 'Empathy articles', 10, 1, '2016-12-23', 'http://www.campbusiness.com/search?q=empathy'),
(132, 0, 18, 'Lifeguard articles', 10, 1, '2016-12-23', 'http://www.campbusiness.com/search?q=lifeguard'),
(133, 0, 77, 'Staff Conscious', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2002/07/16/staff-conscious?rq=alcohol'),
(134, 0, 114, 'Allergies articles', 10, 1, '2016-12-23', 'http://www.campbusiness.com/search?q=allergies'),
(135, 0, 98, 'Safeguarding Campers & Staff Members', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2011/05/02/safeguarding-campers-staff-members?rq=abuse'),
(136, 0, 88, 'In Case Of Fire', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2011/03/08/in-case-of-fire?rq=fire'),
(137, 0, 136, 'Kitchen articles', 10, 1, '2016-12-23', 'http://www.campbusiness.com/search?q=kitchen'),
(138, 0, 95, 'Waterfront Safety And Preparation', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2012/03/01/waterfront-safety-and-preparation?rq=lake'),
(139, 0, 95, 'Camp Aquatic Safety', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2013/03/04/camp-aquatic-safety?rq=lake'),
(140, 0, 127, 'Create An Emergency Response Team', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2015/12/17/create-an-emergency-response-team?rq=emergency'),
(141, 0, 127, 'Prepare Staff For The Worst', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2011/05/02/prepare-staff-for-the-worst?rq=emergency'),
(142, 0, 108, 'Waterfront Safety and Preparation', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2012/03/01/waterfront-safety-and-preparation?rq=waterfront'),
(143, 0, 83, 'When Lightning Strikes', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2016/04/25/when-lightning-strikes?rq=weather'),
(144, 0, 115, 'Medical articles', 10, 1, '2016-12-23', 'http://www.campbusiness.com/search?q=medical'),
(145, 0, 32, 'Overcoming Mid-Season Slump', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2011/07/01/overcoming-mid-season-slump?rq=burnout'),
(146, 0, 27, 'The Importance Of Candid Feedback', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2013/05/06/the-importance-of-candid-feedback?rq=conversation'),
(147, 0, 133, 'The Importance Of Candid Feedback', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2013/05/06/the-importance-of-candid-feedback?rq=conversation'),
(148, 0, 142, 'Personality articles', 10, 1, '2016-12-23', 'http://www.campbusiness.com/search?q=personality'),
(149, 0, 143, 'Meeting articles', 10, 1, '2016-12-23', 'http://www.campbusiness.com/search?q=meeting'),
(150, 0, 146, 'Flaming Hearts And Spirits', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2011/07/05/flaming-hearts-and-spirits?rq=archery'),
(151, 0, 147, 'Flaming Hearts And Spirits', 10, 1, '2016-12-23', 'http://www.campbusiness.com/articles/2011/07/05/flaming-hearts-and-spirits?rq=archery');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `wp_eot_resources`
--
ALTER TABLE `wp_eot_resources`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `wp_eot_resources`
--
ALTER TABLE `wp_eot_resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=152;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
