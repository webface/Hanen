# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.6.35)
# Database: eot_china_dev
# Generation Time: 2017-06-22 14:10:24 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table wp_eot_modules
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_eot_modules`;

CREATE TABLE `wp_eot_modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `description_text` longtext NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `number_of_linked_courses` int(11) DEFAULT NULL,
  `tags` varchar(255) NOT NULL,
  `library_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `component_type` varchar(45) DEFAULT NULL,
  `video_id` int(11) NOT NULL DEFAULT '0',
  `org_id` int(11) NOT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `creator_first_name` varchar(255) DEFAULT NULL,
  `creator_last_name` varchar(255) DEFAULT NULL,
  `creator_email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `wp_eot_modules` WRITE;
/*!40000 ALTER TABLE `wp_eot_modules` DISABLE KEYS */;

INSERT INTO `wp_eot_modules` (`id`, `title`, `description_text`, `created_at`, `updated_at`, `number_of_linked_courses`, `tags`, `library_id`, `category_id`, `component_type`, `video_id`, `org_id`, `creator_id`, `creator_first_name`, `creator_last_name`, `creator_email`)
VALUES
	(2,'15 Passenger Van Safety','',NULL,NULL,3,'Physical_and_Emotional_Safety LE',1,1,'page',140,0,NULL,NULL,NULL,NULL),
	(5,'Achieving Supervisory Balance','',NULL,NULL,3,'Supervision LE',1,2,'page',162,0,NULL,NULL,NULL,NULL),
	(9,'Active Lifeguarding','',NULL,NULL,13,'Physical_and_Emotional_Safety LE LEL LE_SP_PRP',1,1,'page',18,0,NULL,NULL,NULL,NULL),
	(15,'Advanced Skills for Working with Difficult Parents, Part I','',NULL,NULL,16,'Leadership LE LEL',1,3,'page',19,0,NULL,NULL,NULL,NULL),
	(19,'Advanced Skills for Working with Difficult Parents, Part II','',NULL,NULL,15,'Leadership LE',1,3,'page',20,0,NULL,NULL,NULL,NULL),
	(24,'Advanced Staff Supervision, Part I','',NULL,NULL,13,'Supervision LE',1,2,'page',15,0,NULL,NULL,NULL,NULL),
	(29,'Advanced Staff Supervision, Part II','',NULL,NULL,12,'Supervision LE',1,2,'page',16,0,NULL,NULL,NULL,NULL),
	(35,'Afterschool Program Success, Part I','',NULL,NULL,11,'Youth_Development_and_Play LE',1,4,'page',118,0,NULL,NULL,NULL,NULL),
	(39,'Afterschool Program Success, Part II','',NULL,NULL,11,'Youth_Development_and_Play LE',1,4,'page',119,0,NULL,NULL,NULL,NULL),
	(43,'Alcohol Beverage Laws','',NULL,NULL,14,'Physical_and_Emotional_Safety LE LEL',1,1,'page',77,0,NULL,NULL,NULL,NULL),
	(53,'Anaphylactic Emergencies','',NULL,NULL,24,'Physical_and_Emotional_Safety LE LEL',1,1,'page',114,0,NULL,NULL,NULL,NULL),
	(59,'Attention Deficit Hyperactivity Disorder','',NULL,NULL,14,'Mental_Health_and_Behavior LE LEL',1,5,'page',24,0,NULL,NULL,NULL,NULL),
	(63,'Autism Spectrum Disorder','',NULL,NULL,9,'Mental_Health_and_Behavior LE',1,5,'page',126,0,NULL,NULL,NULL,NULL),
	(66,'Awesome Archery, Part I','',NULL,NULL,2,'Youth_Development_and_Play LE',1,4,'page',167,0,NULL,NULL,NULL,NULL),
	(68,'Awesome Archery, Part II','',NULL,NULL,2,'Youth_Development_and_Play LE',1,4,'page',168,0,NULL,NULL,NULL,NULL),
	(71,'Becoming a Youth Development Professional, Part I','',NULL,NULL,14,'Youth_Development_and_Play LE LEL LE_SP_DC LE_SP_OC',1,4,'page',21,0,NULL,NULL,NULL,NULL),
	(75,'Becoming a Youth Development Professional, Part II','',NULL,NULL,3,'Youth_Development_and_Play LE',1,4,'page',135,0,NULL,NULL,NULL,NULL),
	(79,'Behavior-Based Interviewing','',NULL,NULL,9,'Supervision LE',1,2,'page',90,0,NULL,NULL,NULL,NULL),
	(83,'Best Boys, Part I','',NULL,NULL,14,'Leadership LE LEL',1,3,'page',91,0,NULL,NULL,NULL,NULL),
	(87,'Best Boys, Part II','',NULL,NULL,8,'Leadership LE',1,3,'page',92,0,NULL,NULL,NULL,NULL),
	(91,'Boov Ship Game','',NULL,NULL,3,'Creative_Literacy LE',1,7,'page',102,0,NULL,NULL,NULL,NULL),
	(93,'Bullies and Targets, Part I','',NULL,NULL,14,'Mental_Health_and_Behavior LE LEL LE_SP_PRP',1,5,'page',22,0,NULL,NULL,NULL,NULL),
	(97,'Bullies and Targets, Part II','',NULL,NULL,14,'Mental_Health_and_Behavior LE',1,5,'page',76,0,NULL,NULL,NULL,NULL),
	(101,'Burn Specific Programming, Part I','',NULL,NULL,8,'Youth_Development_and_Play LE',1,4,'page',110,0,NULL,NULL,NULL,NULL),
	(105,'Burn Specific Programming, Part II','',NULL,NULL,8,'Youth_Development_and_Play LE',1,4,'page',120,0,NULL,NULL,NULL,NULL),
	(109,'Canoeing Success, Part I','',NULL,NULL,8,'Youth_Development_and_Play LE',1,4,'page',116,0,NULL,NULL,NULL,NULL),
	(113,'Canoeing Success, Part II','',NULL,NULL,9,'Youth_Development_and_Play LE',1,4,'page',117,0,NULL,NULL,NULL,NULL),
	(117,'Child Welfare and Protection','',NULL,NULL,16,'Leadership LE SE',1,3,'page',98,0,NULL,NULL,NULL,NULL),
	(121,'Children with Verbal Learning Disabilities','',NULL,NULL,9,'Mental_Health_and_Behavior LE',1,5,'page',11,0,NULL,NULL,NULL,NULL),
	(125,'Christian Perspectives, Part I','',NULL,NULL,3,'Leadership LE',1,3,'page',129,0,NULL,NULL,NULL,NULL),
	(129,'Christian Perspectives, Part II','',NULL,NULL,3,'Leadership LE',1,3,'page',130,0,NULL,NULL,NULL,NULL),
	(133,'Classic Problem Solving','',NULL,NULL,9,'Leadership LE LEL',1,3,'page',25,0,NULL,NULL,NULL,NULL),
	(137,'Confidentiality for Youth Professionals','',NULL,NULL,6,'Physical_and_Emotional_Safety LE',1,1,'page',65,0,NULL,NULL,NULL,NULL),
	(142,'Cracking Kids’ Secret Code','',NULL,NULL,10,'Leadership LE LEL',1,3,'page',107,0,NULL,NULL,NULL,NULL),
	(147,'Cultivating Patience','',NULL,NULL,8,'Youth_Development_and_Play LE',1,4,'page',62,0,NULL,NULL,NULL,NULL),
	(152,'Cultural Competence in Youth Programs, Part I','',NULL,NULL,7,'',1,0,'page',82,0,NULL,NULL,NULL,NULL),
	(157,'Cultural Competence in Youth Programs, Part II','',NULL,NULL,4,'Leadership LE',1,3,'page',113,0,NULL,NULL,NULL,NULL),
	(163,'Cyberbullying and Sexting','',NULL,NULL,5,'Physical_and_Emotional_Safety LE',1,1,'page',78,0,NULL,NULL,NULL,NULL),
	(165,'Day Camp Dynamics, Part I','',NULL,NULL,6,'Leadership LE LE_SP_DC',1,3,'page',66,0,NULL,NULL,NULL,NULL),
	(169,'Day Camp Dynamics, Part II','',NULL,NULL,6,'Leadership LE LE_SP_DC',1,3,'page',67,0,NULL,NULL,NULL,NULL),
	(173,'Day Camp Dynamics, Part III','',NULL,NULL,7,'Leadership LE LE_SP_DC',1,3,'page',84,0,NULL,NULL,NULL,NULL),
	(176,'Dealing with Difficult Parents, Part I - scorm','Sometimes its hard to deal with parents.\n',NULL,NULL,6,'Leadership',1,3,'scorm',0,0,NULL,NULL,NULL,NULL),
	(179,'Difficult Management Conversations, Part I','',NULL,NULL,9,'Supervision LE LEL',1,2,'page',63,0,NULL,NULL,NULL,NULL),
	(183,'Difficult Management Conversations, Part II','',NULL,NULL,4,'Supervision LE',1,2,'page',134,0,NULL,NULL,NULL,NULL),
	(187,'Dot Painting','',NULL,NULL,2,'Creative_Literacy LE',1,7,'page',101,0,NULL,NULL,NULL,NULL),
	(189,'Duty of Care, Part I','',NULL,NULL,9,'Physical_and_Emotional_Safety LE',1,1,'page',73,0,NULL,NULL,NULL,NULL),
	(194,'Duty of Care, Part II','',NULL,NULL,9,'Physical_and_Emotional_Safety LE LEL',1,1,'page',74,0,NULL,NULL,NULL,NULL),
	(200,'Effective Debriefing Tools and Techniques','',NULL,NULL,5,'',1,0,'page',79,0,NULL,NULL,NULL,NULL),
	(204,'Equity & Diversity','',NULL,NULL,7,'',1,0,'page',26,0,NULL,NULL,NULL,NULL),
	(206,'Equity and Diversity','',NULL,NULL,9,'Youth_Development_and_Play LE',1,4,'page',26,0,NULL,NULL,NULL,NULL),
	(210,'Face-to-Face (Differently Abled Youth)','',NULL,NULL,4,'Youth_Development_and_Play LE',1,4,'page',86,0,NULL,NULL,NULL,NULL),
	(214,'Fairytale Hats','',NULL,NULL,2,'Creative_Literacy LE',1,7,'page',104,0,NULL,NULL,NULL,NULL),
	(217,'Fire Building and Fire Safety','',NULL,NULL,6,'',1,0,'page',88,0,NULL,NULL,NULL,NULL),
	(222,'Fire Evacuation Planning','',NULL,NULL,6,'Physical_and_Emotional_Safety LE',1,1,'page',111,0,NULL,NULL,NULL,NULL),
	(226,'Firing a Staff Member','',NULL,NULL,4,'Supervision LE',1,2,'page',89,0,NULL,NULL,NULL,NULL),
	(230,'Get the Best and Forget the Rest','',NULL,NULL,3,'Leadership LE',1,3,'page',125,0,NULL,NULL,NULL,NULL),
	(234,'Girl Power','',NULL,NULL,5,'Leadership LE LEL',1,3,'page',81,0,NULL,NULL,NULL,NULL),
	(239,'Good Sportsmanship vs. Foul Play','',NULL,NULL,9,'Mental_Health_and_Behavior LE',1,5,'page',68,0,NULL,NULL,NULL,NULL),
	(244,'Hello Games','',NULL,NULL,6,'Youth_Development_and_Play LE LEL',1,4,'page',14,0,NULL,NULL,NULL,NULL),
	(248,'Helping Awkward Children Fit In','',NULL,NULL,4,'Mental_Health_and_Behavior LE',1,5,'page',12,0,NULL,NULL,NULL,NULL),
	(255,'Homesickness at Day & Resident Camps, Part I','',NULL,NULL,12,'Mental_Health_and_Behavior LE LEL LE_SP_OC',1,5,'page',31,0,NULL,NULL,NULL,NULL),
	(261,'Homesickness at Day & Resident Camps, Part II','',NULL,NULL,11,'Mental_Health_and_Behavior LE LE_SP_OC',1,5,'page',72,0,NULL,NULL,NULL,NULL),
	(265,'How to Create Staff Groups','',NULL,NULL,5,'',1,0,'page',0,0,NULL,NULL,NULL,NULL),
	(266,'How to Enroll Staff into Courses','',NULL,NULL,5,'',1,0,'page',0,0,NULL,NULL,NULL,NULL),
	(267,'How to Invite/Create Staff Users','',NULL,NULL,5,'howto',1,0,'page',0,0,NULL,NULL,NULL,NULL),
	(268,'How to polish poles at Winbaums Camp','',NULL,NULL,1,'',1,0,'page',0,0,NULL,NULL,NULL,NULL),
	(270,'Jewish Perspectives on Child-Staff Relationships, Part I','',NULL,NULL,5,'Mental_Health_and_Behavior LE',1,5,'page',96,0,NULL,NULL,NULL,NULL),
	(274,'Jewish Perspectives on Child-Staff Relationships, Part II','',NULL,NULL,5,'Mental_Health_and_Behavior LE',1,5,'page',112,0,NULL,NULL,NULL,NULL),
	(278,'Jewish Perspectives on Staff Happiness and Stamina','',NULL,NULL,4,'Youth_Development_and_Play LE',1,4,'page',97,0,NULL,NULL,NULL,NULL),
	(282,'Juggling Balls','',NULL,NULL,2,'Creative_Literacy LE',1,7,'page',99,0,NULL,NULL,NULL,NULL),
	(284,'Kachina Dolls','',NULL,NULL,2,'Creative_Literacy LE',1,7,'page',100,0,NULL,NULL,NULL,NULL),
	(286,'Kickin’ Kitchens','',NULL,NULL,3,'Physical_and_Emotional_Safety LE',1,1,'page',136,0,NULL,NULL,NULL,NULL),
	(290,'Leader of the Pack','',NULL,NULL,2,'Creative_Literacy LE',1,7,'page',103,0,NULL,NULL,NULL,NULL),
	(292,'Lifeguarding Skills Verification','',NULL,NULL,7,'Physical_and_Emotional_Safety LE',1,1,'page',93,0,NULL,NULL,NULL,NULL),
	(297,'Listening with More Than Ears','',NULL,NULL,3,'Mental_Health_and_Behavior LE',1,5,'page',139,0,NULL,NULL,NULL,NULL),
	(300,'Masterful Meetings','',NULL,NULL,2,'Supervision LE',1,2,'page',164,0,NULL,NULL,NULL,NULL),
	(303,'Medical Preparation for Burn Camps','',NULL,NULL,3,'Physical_and_Emotional_Safety LE',1,1,'page',128,0,NULL,NULL,NULL,NULL),
	(307,'Move It Like You Mean It','',NULL,NULL,8,'Youth_Development_and_Play LE LEL',1,4,'page',60,0,NULL,NULL,NULL,NULL),
	(311,'New Module Title','',NULL,NULL,1,'',1,0,'page',0,0,NULL,NULL,NULL,NULL),
	(314,'No Losers','',NULL,NULL,3,'Leadership LE LE_SP_DC LE_SP_PRP LE_SP_OC',1,3,'page',138,0,NULL,NULL,NULL,NULL),
	(318,'Out of the Pool and Into the Wild','',NULL,NULL,7,'Physical_and_Emotional_Safety LE',1,1,'page',95,0,NULL,NULL,NULL,NULL),
	(324,'Outdoor Cooking with Youth','',NULL,NULL,5,'',1,0,'page',87,0,NULL,NULL,NULL,NULL),
	(328,'Personality Synergy','',NULL,NULL,2,'Leadership LE',1,3,'page',163,0,NULL,NULL,NULL,NULL),
	(331,'Playing with a Full Deck','',NULL,NULL,8,'Youth_Development_and_Play LE',1,4,'page',80,0,NULL,NULL,NULL,NULL),
	(338,'Preventing Gossip and Relational Aggression','',NULL,NULL,7,'',1,0,'page',69,0,NULL,NULL,NULL,NULL),
	(343,'Programming For All (Differently Abled Youth)','',NULL,NULL,6,'',1,0,'page',85,0,NULL,NULL,NULL,NULL),
	(348,'Providing Effective Feedback, Part I','',NULL,NULL,7,'Supervision LE',1,2,'page',27,0,NULL,NULL,NULL,NULL),
	(352,'Providing Effective Feedback, Part II','',NULL,NULL,3,'Supervision LE',1,2,'page',133,0,NULL,NULL,NULL,NULL),
	(357,'Rainy Day Games','',NULL,NULL,4,'Youth_Development_and_Play LE',1,4,'page',13,0,NULL,NULL,NULL,NULL),
	(362,'Regional Disaster Management for Youth Programs','',NULL,NULL,7,'Physical_and_Emotional_Safety LE',1,1,'page',127,0,NULL,NULL,NULL,NULL),
	(367,'Respect is Learned, Not Given','',NULL,NULL,4,'Leadership LE',1,3,'page',137,0,NULL,NULL,NULL,NULL),
	(372,'Rules Were Made to Be Positive','',NULL,NULL,8,'Leadership LE LE_SP_DC LE_SP_PRP LE_SP_OC',1,3,'page',10,0,NULL,NULL,NULL,NULL),
	(377,'Safe Touch & Safe Talk','',NULL,NULL,22,'Physical_and_Emotional_Safety LE LEL',1,1,'page',28,0,NULL,NULL,NULL,NULL),
	(385,'Sand Art','',NULL,NULL,2,'Creative_Literacy LE',1,7,'page',106,0,NULL,NULL,NULL,NULL),
	(388,'Sexual Harassment','',NULL,NULL,6,'',1,0,'page',64,0,NULL,NULL,NULL,NULL),
	(392,'Shockingly Professional Talk, Part I','',NULL,NULL,2,'Physical_and_Emotional_Safety LE',1,1,'page',165,0,NULL,NULL,NULL,NULL),
	(394,'Shockingly Professional Talk, Part II','',NULL,NULL,2,'Physical_and_Emotional_Safety LE',1,1,'page',166,0,NULL,NULL,NULL,NULL),
	(398,'Skillful Discipline, Part I','',NULL,NULL,13,'Mental_Health_and_Behavior LE',1,5,'page',29,0,NULL,NULL,NULL,NULL),
	(405,'Skillful Discipline, Part II','',NULL,NULL,11,'Mental_Health_and_Behavior LE',1,5,'page',75,0,NULL,NULL,NULL,NULL),
	(410,'Speaking of Camp','',NULL,NULL,11,'Youth_Development_and_Play LE LEL',1,4,'page',70,0,NULL,NULL,NULL,NULL),
	(416,'Staff Use of the Internet','',NULL,NULL,7,'',1,0,'page',23,0,NULL,NULL,NULL,NULL),
	(420,'Stop Yelling Get the HINT','',NULL,NULL,14,'Leadership LE LEL LE_SP_DC LE_SP_PRP LE_SP_OC',1,3,'page',30,0,NULL,NULL,NULL,NULL),
	(427,'Supervising Junior Leaders','',NULL,NULL,8,'Supervision LE LEL',1,2,'page',17,0,NULL,NULL,NULL,NULL),
	(433,'Swim Checks in Action','',NULL,NULL,7,'Physical_and_Emotional_Safety LE',1,1,'page',94,0,NULL,NULL,NULL,NULL),
	(438,'Totem Pole','',NULL,NULL,2,'Creative_Literacy LE',1,7,'page',105,0,NULL,NULL,NULL,NULL),
	(440,'Waterfront Safety Design','',NULL,NULL,7,'Physical_and_Emotional_Safety LE',1,1,'page',108,0,NULL,NULL,NULL,NULL),
	(447,'We Squashed It! - Physical Aggression','',NULL,NULL,6,'Leadership LE LEL',1,3,'page',9,0,NULL,NULL,NULL,NULL),
	(451,'We Squashed it! - Relational Aggression','',NULL,NULL,12,'Leadership LE LEL',1,3,'page',132,0,NULL,NULL,NULL,NULL),
	(455,'Weather Watch','',NULL,NULL,9,'Physical_and_Emotional_Safety LE LE_SP_PRP',1,1,'page',83,0,NULL,NULL,NULL,NULL),
	(460,'Wilderness Wellness','',NULL,NULL,6,'Physical_and_Emotional_Safety LE LE_SP_DC LE_SP_PRP LE_SP_OC',1,1,'page',115,0,NULL,NULL,NULL,NULL),
	(464,'Winning Ways of Skilled Supervisors','',NULL,NULL,8,'Supervision LE LEL',1,2,'page',61,0,NULL,NULL,NULL,NULL),
	(469,'Wise Use of Time Off','',NULL,NULL,9,'Physical_and_Emotional_Safety LE LEL',1,1,'page',32,0,NULL,NULL,NULL,NULL),
	(475,'Youth Inspired','',NULL,NULL,13,'Youth_Development_and_Play LE LEL LE_SP_DC LE_SP_PRP LE_SP_OC',1,4,'page',109,0,NULL,NULL,NULL,NULL),
	(483,'Test Module 1','test','2017-05-31 00:00:00',NULL,NULL,'',0,0,'custom',0,692,88,'100','100',NULL),
	(484,'Back to camp basics for boys','test','2017-05-31 00:00:00',NULL,NULL,'',0,0,'custom',0,692,88,'100','100',NULL);

/*!40000 ALTER TABLE `wp_eot_modules` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
