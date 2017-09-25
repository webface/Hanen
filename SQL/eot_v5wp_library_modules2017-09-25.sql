# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.6.35)
# Database: eot_v5
# Generation Time: 2017-09-25 18:48:59 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table wp_library_modules
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_library_modules`;

CREATE TABLE `wp_library_modules` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `library_id` bigint(20) unsigned NOT NULL,
  `module_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `wp_library_modules` WRITE;
/*!40000 ALTER TABLE `wp_library_modules` DISABLE KEYS */;

INSERT INTO `wp_library_modules` (`ID`, `library_id`, `module_id`)
VALUES
	(1,1,2),
	(2,1,5),
	(3,1,9),
	(4,1,15),
	(5,1,19),
	(6,1,24),
	(7,1,29),
	(8,1,35),
	(9,1,39),
	(10,1,43),
	(11,1,53),
	(12,1,59),
	(13,1,63),
	(14,1,66),
	(15,1,68),
	(16,1,71),
	(17,1,75),
	(18,1,79),
	(19,1,83),
	(20,1,87),
	(21,1,91),
	(22,1,93),
	(23,1,97),
	(24,1,101),
	(25,1,105),
	(26,1,109),
	(27,1,113),
	(28,1,117),
	(29,1,121),
	(30,1,125),
	(31,1,129),
	(32,1,133),
	(33,1,137),
	(34,1,142),
	(35,1,147),
	(36,1,152),
	(37,1,157),
	(38,1,163),
	(39,1,165),
	(40,1,169),
	(41,1,173),
	(42,1,179),
	(43,1,183),
	(44,1,187),
	(45,1,189),
	(46,1,194),
	(47,1,200),
	(48,1,204),
	(49,1,210),
	(50,1,214),
	(51,1,217),
	(52,1,222),
	(53,1,226),
	(54,1,230),
	(55,1,234),
	(56,1,239),
	(57,1,244),
	(58,1,248),
	(59,1,255),
	(60,1,261),
	(61,1,267),
	(62,1,270),
	(63,1,274),
	(64,1,278),
	(65,1,282),
	(66,1,284),
	(67,1,286),
	(68,1,290),
	(69,1,292),
	(70,1,297),
	(71,1,300),
	(72,1,303),
	(73,1,307),
	(74,1,314),
	(75,1,318),
	(76,1,324),
	(77,1,328),
	(78,1,331),
	(79,1,338),
	(80,1,343),
	(81,1,348),
	(82,1,352),
	(83,1,357),
	(84,1,362),
	(85,1,367),
	(86,1,372),
	(87,1,377),
	(88,1,385),
	(89,1,388),
	(90,1,392),
	(91,1,394),
	(92,1,398),
	(93,1,405),
	(94,1,410),
	(95,1,416),
	(96,1,420),
	(97,1,427),
	(98,1,433),
	(99,1,438),
	(100,1,440),
	(101,1,447),
	(102,1,451),
	(103,1,455),
	(104,1,460),
	(105,1,464),
	(106,1,469),
	(107,1,475),
	(108,3,117),
	(109,6,475),
	(110,6,165),
	(111,6,169),
	(112,6,173),
	(113,6,71),
	(114,6,420),
	(115,6,314),
	(116,6,372),
	(117,6,460),
	(118,7,475),
	(119,7,255),
	(120,7,261),
	(121,7,93),
	(122,7,71),
	(123,7,420),
	(124,7,314),
	(125,7,372),
	(126,7,460),
	(128,8,475),
	(129,8,9),
	(130,8,455),
	(131,8,93),
	(132,8,71),
	(133,8,420),
	(134,8,314),
	(135,8,372),
	(136,8,460);

/*!40000 ALTER TABLE `wp_library_modules` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
