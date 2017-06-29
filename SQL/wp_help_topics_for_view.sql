# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.6.35)
# Database: eot_v5
# Generation Time: 2017-06-29 15:53:33 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table wp_help_topics_for_view
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_help_topics_for_view`;

CREATE TABLE `wp_help_topics_for_view` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `part_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `role` enum('administrator','manager','student','salesrep','sales_manager','umbrella_manager','uber_manager') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'student',
  `topic_id` int(10) unsigned DEFAULT NULL,
  `order` int(4) unsigned NOT NULL DEFAULT '50',
  PRIMARY KEY (`ID`),
  KEY `part-role` (`part_name`(191),`role`),
  KEY `topic-part-role` (`topic_id`,`part_name`(191),`role`),
  CONSTRAINT `Topic ID` FOREIGN KEY (`topic_id`) REFERENCES `wp_help_topics` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `wp_help_topics_for_view` WRITE;
/*!40000 ALTER TABLE `wp_help_topics_for_view` DISABLE KEYS */;

INSERT INTO `wp_help_topics_for_view` (`ID`, `part_name`, `role`, `topic_id`, `order`)
VALUES
	(1,'dashboard','manager',10,50),
	(2,'dashboard','manager',11,50),
	(3,'dashboard','manager',12,50),
	(4,'dashboard','manager',13,50),
	(5,'dashboard','manager',14,50),
	(6,'dashboard','manager',15,50),
	(7,'dashboard','manager',16,50),
	(8,'dashboard','manager',17,50),
	(9,'dashboard','manager',18,50),
	(10,'dashboard','manager',19,50),
	(11,'dashboard','manager',20,50);

/*!40000 ALTER TABLE `wp_help_topics_for_view` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
