# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.6.35)
# Database: eot_v5
# Generation Time: 2017-06-22 20:53:29 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table wp_course_module
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_course_module`;

CREATE TABLE `wp_course_module` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `course_id` bigint(20) unsigned DEFAULT NULL,
  `module_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `wp_course_module` WRITE;
/*!40000 ALTER TABLE `wp_course_module` DISABLE KEYS */;

INSERT INTO `wp_course_module` (`ID`, `course_id`, `module_id`)
VALUES
	(1,1,133),
	(2,1,420),
	(3,1,71),
	(4,1,147),
	(5,1,204),
	(6,1,475),
	(7,1,59),
	(8,1,93),
	(9,1,97),
	(10,1,398),
	(11,1,53),
	(12,1,416),
	(13,1,377),
	(14,1,469),
	(15,2,15),
	(16,2,19),
	(17,2,142),
	(18,2,152),
	(19,2,307),
	(20,2,410),
	(21,2,255),
	(22,2,261),
	(23,2,338),
	(24,2,405),
	(25,2,53),
	(26,2,189),
	(27,2,194),
	(28,2,377),
	(29,3,372),
	(30,3,447),
	(31,3,451),
	(32,3,200),
	(33,3,324),
	(34,3,331),
	(35,3,343),
	(36,3,239),
	(37,3,9),
	(38,3,53),
	(39,3,217),
	(40,3,292),
	(41,3,318),
	(42,3,377),
	(43,3,433),
	(44,3,440),
	(45,4,447),
	(46,4,451),
	(47,4,43),
	(48,4,53),
	(49,4,137),
	(50,4,222),
	(51,4,377),
	(52,4,388),
	(53,4,455),
	(54,4,24),
	(55,4,29),
	(56,4,179),
	(57,4,183),
	(58,4,348),
	(59,4,427),
	(60,4,464);

/*!40000 ALTER TABLE `wp_course_module` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
