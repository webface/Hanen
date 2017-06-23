# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.6.35)
# Database: eot_v5
# Generation Time: 2017-06-22 20:53:21 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table wp_courses
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_courses`;

CREATE TABLE `wp_courses` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `course_name` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `course_description` text COLLATE utf8mb4_unicode_520_ci,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `owner_id` bigint(20) unsigned DEFAULT NULL,
  `due_date_after_enrollment` datetime DEFAULT '0000-00-00 00:00:00',
  `subscription_id` bigint(20) unsigned DEFAULT NULL,
  `num_enrolled` int(11) NOT NULL DEFAULT '0',
  `num_completed` int(11) NOT NULL DEFAULT '0',
  `num_not_started` int(11) NOT NULL DEFAULT '0',
  `num_in_progress` int(11) NOT NULL DEFAULT '0',
  `num_passed` int(11) NOT NULL DEFAULT '0',
  `num_failed` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

LOCK TABLES `wp_courses` WRITE;
/*!40000 ALTER TABLE `wp_courses` DISABLE KEYS */;

INSERT INTO `wp_courses` (`ID`, `course_name`, `course_description`, `org_id`, `owner_id`, `due_date_after_enrollment`, `subscription_id`, `num_enrolled`, `num_completed`, `num_not_started`, `num_in_progress`, `num_passed`, `num_failed`)
VALUES
	(1,'New Staff','for new staff',0,0,'0000-00-00 00:00:00',0,0,0,0,0,0,0),
	(2,'Returning Staff','for returning staff',0,0,'0000-00-00 00:00:00',0,0,0,0,0,0,0),
	(3,'Program Staff','for program staff',0,0,'0000-00-00 00:00:00',0,0,0,0,0,0,0),
	(4,'Supervisory Staff','for supervisors',0,0,'0000-00-00 00:00:00',0,0,0,0,0,0,0);

/*!40000 ALTER TABLE `wp_courses` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
