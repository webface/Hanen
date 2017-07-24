# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.6.35)
# Database: eot_v5
# Generation Time: 2017-07-11 19:09:54 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table wp_quiz
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_quiz`;

CREATE TABLE `wp_quiz` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` mediumtext,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `passing_score` int(11) DEFAULT NULL,
  `num_attempts` int(11) DEFAULT NULL,
  `time_limit` time DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `wp_quiz` WRITE;
/*!40000 ALTER TABLE `wp_quiz` DISABLE KEYS */;

INSERT INTO `wp_quiz` (`ID`, `name`, `description`, `org_id`, `user_id`, `date_created`, `passing_score`, `num_attempts`, `time_limit`)
VALUES
	(1,'Advanced Skills for Working with Difficult Parents, Part I','Advanced Skills for Working with Difficult Parents, Part I',0,0,'2017-06-13 00:00:00',NULL,0,'00:20:00'),
	(2,'Advanced Skills for Working with Difficult Parents, Part II','Advanced Skills for Working with Difficult Parents, Part II',0,0,'2017-06-13 00:00:00',NULL,0,'00:20:00'),
	(3,'Best Boys, Part I','Best Boys, Part I',0,0,'2017-06-13 00:00:00',NULL,0,'00:20:00'),
	(4,'Best Boys, Part II','Best Boys, Part II',0,0,'2017-06-13 00:00:00',NULL,0,'00:20:00'),
	(5,'Christian Perspectives, Part 1','Christian Perspectives, Part 1',0,0,'2017-06-13 00:00:00',NULL,0,'00:20:00'),
	(6,'Christian Perspectives, Part 2','Christian Perspectives, Part 2',0,0,'2017-06-13 00:00:00',NULL,0,'00:20:00'),
	(7,'Classic Problem Solving','Classic Problem Solving',0,0,'2017-06-13 00:00:00',NULL,0,'00:20:00'),
	(8,'Cracking Kids’ Secret Code','Cracking Kids’ Secret Code',0,0,'2017-06-14 00:00:00',NULL,0,'00:20:00'),
	(9,'Cultural Competence in Youth Programs, Part I','Cultural Competence in Youth Programs, Part I',0,0,'2017-06-14 00:00:00',NULL,0,'00:20:00'),
	(10,'Cultural Competence in Youth Programs, Part II','Cultural Competence in Youth Programs, Part II',0,0,'2017-06-14 00:00:00',NULL,0,'00:20:00'),
	(11,'Day Camp Dynamics, Part I','Day Camp Dynamics, Part I',0,0,'2017-06-14 00:00:00',0,0,'00:20:00'),
	(13,'Day Camp Dynamics, Part II','Day Camp Dynamics, Part II',0,0,'2017-06-14 00:00:00',NULL,0,'00:20:00'),
	(14,'Day Camp Dynamics, Part III','Day Camp Dynamics, Part III',0,0,'2017-06-14 00:00:00',NULL,0,'00:20:00'),
	(15,'Get the Best and Forget the Rest','Get the Best and Forget the Rest',0,0,'2017-06-14 00:00:00',NULL,0,'00:20:00'),
	(16,'Girl Power: Guiding Principles of Strong Female Leadership','Girl Power: Guiding Principles of Strong Female Leadership',0,0,'2017-06-14 00:00:00',NULL,0,'00:20:00'),
	(17,'No Losers','No Losers',0,0,'2017-06-14 00:00:00',NULL,0,'00:20:00'),
	(18,'Respect is Learned, Not Given','Respect is Learned, Not Given',0,0,'2017-06-14 00:00:00',NULL,0,'00:20:00'),
	(19,'Rules Were Made to Be Positive','Rules Were Made to Be Positive',0,0,'2017-06-14 00:00:00',NULL,0,'00:20:00'),
	(20,'Stop Yelling: Get the HINT','Stop Yelling: Get the HINT',0,0,'2017-06-14 00:00:00',NULL,0,'00:20:00'),
	(21,'We Squashed It! - Physical Aggression','We Squashed It! - Physical Aggression',0,0,'2017-06-14 00:00:00',NULL,0,'00:20:00'),
	(22,'We Squashed it! - Relational Aggression','We Squashed it! - Relational Aggression',0,0,'2017-06-14 00:00:00',NULL,0,'00:20:00'),
	(23,'Afterschool Program Success, Part I','Afterschool Program Success, Part I',0,0,'2017-06-14 00:00:00',NULL,0,'00:20:00'),
	(24,'Afterschool Program Success, Part II','Afterschool Program Success, Part II',0,0,'2017-06-14 00:00:00',NULL,0,'00:20:00'),
	(25,'Becoming a Youth Development Professional, Part I','Becoming a Youth Development Professional, Part I',0,0,'2017-06-14 00:00:00',0,0,'00:20:00'),
	(26,'Becoming a Youth Development Professional, Part II','Becoming a Youth Development Professional, Part II',0,0,'2017-06-14 00:00:00',NULL,0,'00:20:00'),
	(27,'Burn Specific Programming, Part I','Burn Specific Programming, Part I',0,0,'2017-06-15 00:00:00',NULL,0,'00:20:00'),
	(28,'Burn Specific Programming, Part 2','Burn Specific Programming, Part 2',0,0,'2017-06-15 00:00:00',NULL,0,'00:20:00'),
	(29,'Canoeing Success, Part I','Canoeing Success, Part I',0,0,'2017-06-15 00:00:00',0,0,'00:20:00'),
	(30,'Canoeing Success, Part II','Canoeing Success, Part II',0,0,'2017-06-15 00:00:00',NULL,0,'00:20:00'),
	(31,'Cultivating Patience','Effective Debriefing Tools and Techniques',0,0,'2017-06-15 00:00:00',NULL,0,'00:20:00'),
	(32,'Effective Debriefing Tools and Techniques','Effective Debriefing Tools and Techniques',0,0,'2017-06-15 00:00:00',NULL,0,'00:20:00'),
	(33,'Equity and Diversity','Equity and Diversity',0,0,'2017-06-15 00:00:00',NULL,0,'00:20:00'),
	(34,'Face-to-Face (Differently Abled Youth)','Face-to-Face (Differently Abled Youth)',0,0,'2017-06-15 00:00:00',NULL,0,'00:20:00'),
	(35,'Hello Games','Hello Games',0,0,'2017-06-15 00:00:00',NULL,0,'00:20:00'),
	(36,'Jewish Perspectives on Staff Happiness and Stamina','Jewish Perspectives on Staff Happiness and Stamina',0,0,'2017-06-15 00:00:00',NULL,0,'00:20:00'),
	(37,'Move It Like You Mean It','Move It Like You Mean It',0,0,'2017-06-15 00:00:00',NULL,0,'00:20:00'),
	(38,'Outdoor Cooking with Youth','Outdoor Cooking with Youth',0,0,'2017-06-16 00:00:00',NULL,0,'00:20:00'),
	(39,'Playing with a Full Deck','Playing with a Full Deck',0,0,'2017-06-16 00:00:00',NULL,0,'00:20:00'),
	(40,'Programming For All (Differently Abled Youth)','Programming For All (Differently Abled Youth)',0,0,'2017-06-16 00:00:00',NULL,0,'00:20:00'),
	(41,'Rainy Day Games','Rainy Day Games',0,0,'2017-06-16 00:00:00',NULL,0,'00:20:00'),
	(42,'Speaking of Camp','Speaking of Camp',0,0,'2017-06-16 00:00:00',NULL,0,'00:20:00'),
	(43,'Youth Inspired','Youth Inspired',0,0,'2017-06-16 00:00:00',NULL,0,'00:20:00'),
	(44,'Attention Deficit Hyperactivity Disorder','Attention Deficit Hyperactivity Disorder',0,0,'2017-06-16 00:00:00',NULL,0,'00:20:00'),
	(45,'Autism Spectrum Disorder','Autism Spectrum Disorder',0,0,'2017-06-16 00:00:00',NULL,0,'00:20:00'),
	(46,'Bullies and Targets, Part I','Bullies and Targets, Part I',0,0,'2017-06-16 00:00:00',NULL,0,'00:20:00'),
	(47,'Bullies and Targets, Part II','Bullies and Targets, Part II',0,0,'2017-06-16 00:00:00',NULL,0,'00:20:00'),
	(48,'Children with Verbal Learning Disabilities','Children with Verbal Learning Disabilities',0,0,'2017-06-16 00:00:00',NULL,0,'00:20:00'),
	(49,'Good Sportsmanship vs. Foul Play','Good Sportsmanship vs. Foul Play',0,0,'2017-06-16 00:00:00',NULL,0,'00:20:00'),
	(50,'Helping Awkward Children Fit In','Helping Awkward Children Fit In',0,0,'2017-06-19 00:00:00',NULL,0,'00:20:00'),
	(51,'Homesickness at Day & Resident Camps, Part I','Homesickness at Day & Resident Camps, Part I',0,0,'2017-06-19 00:00:00',NULL,0,'00:20:00'),
	(52,'Homesickness at Day & Resident Camps, Part II','Homesickness at Day & Resident Camps, Part II',0,0,'2017-06-19 00:00:00',NULL,0,'00:20:00'),
	(53,'Jewish Perspectives on Child-Staff Relationships, Part I','Jewish Perspectives on Child-Staff Relationships, Part I',0,0,'2017-06-19 00:00:00',NULL,0,'00:20:00'),
	(54,'Jewish Perspectives on Child-Staff Relationships, Part II','Jewish Perspectives on Child-Staff Relationships, Part II',0,0,'2017-06-19 00:00:00',NULL,0,'00:20:00'),
	(55,'Listening with More Than Ears','Listening with More Than Ears',0,0,'2017-06-19 00:00:00',NULL,0,'00:20:00'),
	(56,'Preventing Gossip and Relational Aggression','Preventing Gossip and Relational Aggression',0,0,'2017-06-19 00:00:00',NULL,0,'00:20:00'),
	(57,'Skillful Discipline, Part I','Skillful Discipline, Part I',0,0,'2017-06-19 00:00:00',NULL,0,'00:20:00'),
	(58,'Skillful Discipline, Part II','Skillful Discipline, Part II',0,0,'2017-06-19 00:00:00',NULL,0,'00:20:00'),
	(59,'15 Passenger Van Safety','15 Passenger Van Safety',0,0,'2017-06-19 00:00:00',NULL,0,'00:20:00'),
	(60,'Active Lifeguarding','Active Lifeguarding',0,0,'2017-06-19 00:00:00',NULL,0,'00:20:00'),
	(61,'Alcohol Beverage Laws','Alcohol Beverage Laws',0,0,'2017-06-19 00:00:00',NULL,0,'00:20:00'),
	(62,'Anaphylactic Emergencies','Anaphylactic Emergencies',0,0,'2017-06-19 00:00:00',NULL,0,'00:20:00'),
	(63,'Child Welfare and Protection','Child Welfare and Protection',0,0,'2017-06-20 00:00:00',NULL,0,'00:20:00'),
	(64,'Confidentiality for Youth Professionals','Confidentiality for Youth Professionals',0,0,'2017-06-20 00:00:00',NULL,0,'00:20:00'),
	(65,'Cyberbullying & Sexting','Cyberbullying & Sexting',0,0,'2017-06-20 00:00:00',NULL,0,'00:20:00'),
	(66,'Duty of Care, Part I','Duty of Care, Part I',0,0,'2017-06-20 00:00:00',NULL,0,'00:20:00'),
	(67,'Duty of Care, Part II','Duty of Care, Part II',0,0,'2017-06-20 00:00:00',NULL,0,'00:20:00'),
	(68,'Fire Building and Fire Safety','Fire Building and Fire Safety',0,0,'2017-06-20 00:00:00',NULL,0,'00:20:00'),
	(69,'Fire Evacuation Planning','Fire Evacuation Planning',0,0,'2017-07-06 00:00:00',NULL,0,'00:20:00'),
	(70,'Kickin\\\' Kitchens','Kickin\\\' Kitchens',0,0,'2017-07-06 00:00:00',NULL,0,'00:20:00'),
	(73,'Lifeguarding Skills Verification','Lifeguarding Skills Verification',0,0,'2017-07-06 00:00:00',NULL,0,'00:20:00'),
	(74,'Out of the Pool and Into the Wild','Out of the Pool and Into the Wild',0,0,'2017-07-07 00:00:00',NULL,0,'00:20:00'),
	(75,'Regional Disaster Management for Youth Programs','Regional Disaster Management for Youth Programs',0,0,'2017-07-07 00:00:00',NULL,0,'00:20:00'),
	(76,'Safe Touch and Safe Talk','Safe Touch and Safe Talk',0,0,'2017-07-07 00:00:00',NULL,0,'00:20:00'),
	(77,'Sexual Harassment','Sexual Harassment',0,0,'2017-07-07 00:00:00',NULL,0,'00:20:00'),
	(78,'Staff Use of the Internet','Staff Use of the Internet',0,0,'2017-07-07 00:00:00',NULL,0,'00:20:00'),
	(79,'Swim Checks in Action','Swim Checks in Action',0,0,'2017-07-07 00:00:00',0,0,'00:20:00'),
	(80,'Waterfront Safety Design','Waterfront Safety Design',0,0,'2017-07-07 00:00:00',NULL,0,'00:20:00'),
	(82,'Weather Watch','Weather Watch',0,0,'2017-07-07 00:00:00',NULL,0,'00:20:00'),
	(83,'Wilderness Wellness','Wilderness Wellness',0,0,'2017-07-07 00:00:00',NULL,0,'00:20:00'),
	(84,'Wise Use of Time Off','Wise Use of Time Off',0,0,'2017-07-07 00:00:00',NULL,0,'00:20:00'),
	(88,'Advanced Staff Supervision, Part I','Advanced Staff Supervision, Part I',0,0,'2017-07-07 00:00:00',NULL,0,'00:20:00'),
	(94,'Advanced Staff Supervision, Part II','Advanced Staff Supervision, Part II',0,0,'2017-07-10 00:00:00',NULL,0,'00:20:00'),
	(95,'Behavior-Based Interviewing','Behavior-Based Interviewing',0,0,'2017-07-10 00:00:00',NULL,0,'00:20:00'),
	(96,'Difficult Management Conversations, Part I','Difficult Management Conversations, Part I',0,0,'2017-07-10 00:00:00',NULL,0,'00:20:00'),
	(97,'Difficult Management Conversations, Part II','Difficult Management Conversations, Part II',0,0,'2017-07-10 00:00:00',NULL,0,'00:20:00'),
	(98,'Firing a Staff Member','Firing a Staff Member',0,0,'2017-07-10 00:00:00',NULL,0,'00:20:00'),
	(99,'Providing Effective Feedback, Part I','Providing Effective Feedback, Part I',0,0,'2017-07-10 00:00:00',NULL,0,'00:20:00'),
	(100,'Providing Effective Feedback, Part II','Providing Effective Feedback, Part II',0,0,'2017-07-10 00:00:00',NULL,0,'00:20:00'),
	(101,'Supervising Junior Leaders','Supervising Junior Leaders',0,0,'2017-07-10 00:00:00',NULL,0,'00:20:00'),
	(102,'Winning Ways of Skilled Supervisors','Winning Ways of Skilled Supervisors',0,0,'2017-07-10 00:00:00',NULL,0,'00:20:00');

/*!40000 ALTER TABLE `wp_quiz` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
