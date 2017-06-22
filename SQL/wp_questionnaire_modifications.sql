# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.6.35)
# Database: eot_china_dev
# Generation Time: 2017-06-21 20:38:52 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table wp_questionnaire_modifications
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_questionnaire_modifications`;

CREATE TABLE `wp_questionnaire_modifications` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `library_id` int(3) DEFAULT NULL,
  `question` int(3) DEFAULT NULL,
  `answer` int(3) DEFAULT NULL,
  `course_name_id` int(3) DEFAULT NULL,
  `action` enum('Add','Remove') DEFAULT NULL,
  `video_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `wp_questionnaire_modifications` WRITE;
/*!40000 ALTER TABLE `wp_questionnaire_modifications` DISABLE KEYS */;

INSERT INTO `wp_questionnaire_modifications` (`id`, `library_id`, `question`, `answer`, `course_name_id`, `action`, `video_id`)
VALUES
	(38,1,1,1,1,'Add',66),
	(40,1,1,1,1,'Remove',62),
	(41,1,1,1,1,'Remove',32),
	(42,1,1,1,2,'Add',68),
	(43,1,1,1,2,'Remove',19),
	(44,1,1,1,2,'Remove',20),
	(46,1,1,3,1,'Add',66),
	(48,1,1,3,2,'Add',68),
	(49,1,1,1,1,'Add',67),
	(50,1,1,3,1,'Add',67),
	(53,1,1,1,2,'Add',84),
	(54,1,1,3,2,'Add',84),
	(60,1,1,4,1,'Add',118),
	(61,1,1,4,1,'Add',119),
	(62,1,1,4,1,'Remove',62),
	(63,1,1,4,1,'Remove',32),
	(64,1,1,4,2,'Add',68),
	(65,1,1,4,2,'Remove',19),
	(66,1,1,4,2,'Remove',20),
	(82,1,2,2,1,'Remove',24),
	(84,1,2,2,2,'Remove',60),
	(89,1,2,3,1,'Remove',24),
	(90,1,2,3,2,'Add',91),
	(91,1,2,3,2,'Remove',60),
	(101,1,3,2,1,'Add',12),
	(102,1,3,2,1,'Add',126),
	(103,1,3,2,2,'Add',12),
	(104,1,3,2,2,'Add',126),
	(105,1,3,2,3,'Add',126),
	(106,1,3,2,3,'Add',12),
	(107,1,3,2,4,'Add',12),
	(108,1,3,2,4,'Add',126),
	(109,1,3,2,1,'Remove',29),
	(110,1,3,2,1,'Remove',109),
	(111,1,3,2,2,'Remove',75),
	(112,1,3,2,2,'Remove',82),
	(114,1,3,3,1,'Add',86),
	(116,1,3,3,2,'Add',86),
	(118,1,3,3,3,'Add',86),
	(119,1,3,3,4,'Add',86),
	(120,1,3,3,1,'Remove',29),
	(121,1,3,3,1,'Remove',109),
	(122,1,3,3,2,'Remove',75),
	(123,1,3,3,2,'Remove',82),
	(124,1,3,4,1,'Add',110),
	(125,1,3,4,1,'Add',120),
	(126,1,3,4,2,'Add',110),
	(127,1,3,4,2,'Add',120),
	(128,1,3,4,3,'Add',110),
	(129,1,3,4,3,'Add',120),
	(130,1,3,4,4,'Add',110),
	(131,1,3,4,4,'Add',120),
	(132,1,3,4,2,'Remove',75),
	(133,1,3,4,2,'Remove',82),
	(134,1,3,4,1,'Remove',29),
	(135,1,3,4,1,'Remove',109),
	(136,1,4,1,1,'Add',129),
	(137,1,4,1,1,'Add',130),
	(138,1,4,1,2,'Add',129),
	(139,1,4,1,2,'Add',130),
	(140,1,4,1,3,'Add',129),
	(141,1,4,1,3,'Add',130),
	(142,1,4,1,4,'Add',129),
	(143,1,4,1,4,'Add',130),
	(144,1,4,2,1,'Add',96),
	(145,1,4,2,1,'Add',112),
	(146,1,4,2,2,'Add',96),
	(147,1,4,2,2,'Add',112),
	(148,1,4,2,3,'Add',96),
	(149,1,4,2,3,'Add',112),
	(150,1,4,2,4,'Add',112),
	(151,1,4,2,4,'Add',96),
	(152,1,2,1,4,'Remove',64),
	(153,1,2,2,4,'Remove',64),
	(155,1,2,2,4,'Add',91),
	(157,1,2,3,4,'Add',81),
	(158,1,2,3,4,'Remove',64),
	(159,1,3,2,4,'Remove',9),
	(160,1,3,2,4,'Remove',83),
	(161,1,3,2,3,'Remove',87),
	(162,1,3,3,4,'Add',85),
	(163,1,3,3,3,'Remove',87),
	(164,1,3,3,4,'Remove',9),
	(165,1,3,3,4,'Remove',83),
	(166,1,3,4,3,'Remove',87),
	(167,1,3,4,3,'Remove',10),
	(168,1,3,4,4,'Remove',9),
	(169,1,3,4,4,'Remove',83),
	(170,1,5,1,1,'Add',83),
	(171,1,4,1,4,'Add',109),
	(173,1,7,1,2,'Add',140),
	(174,1,7,1,3,'Add',140),
	(175,1,1,2,1,'Add',35),
	(176,1,1,2,1,'Add',59),
	(177,1,1,2,1,'Add',18),
	(178,1,1,1,1,'Add',140),
	(179,1,1,1,1,'Add',35),
	(180,1,1,1,1,'Add',59),
	(181,1,1,1,4,'Add',140),
	(182,6,2,1,2,'Remove',81),
	(183,7,10,1,1,'Add',18),
	(184,7,10,1,1,'Add',118),
	(185,7,10,1,1,'Add',119),
	(186,7,10,1,1,'Add',91),
	(187,1,2,1,1,'Add',81),
	(188,7,11,1,1,'Add',140),
	(189,7,11,1,1,'Add',35),
	(190,7,11,1,1,'Add',59),
	(191,7,11,1,1,'Add',18),
	(192,7,11,1,1,'Add',19),
	(193,7,11,1,1,'Add',20);

/*!40000 ALTER TABLE `wp_questionnaire_modifications` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
