# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.6.35)
# Database: eot_v5
# Generation Time: 2017-07-24 15:48:10 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table wp_track
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_track`;

CREATE TABLE `wp_track` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('download_resource','video_started','watch_video','watch_slide','login','failed_login','failed_coupon','massmail','download_video','generate_report','delete_subscription','quiz_taken','certificate_conferred') DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ip` varchar(32) NOT NULL DEFAULT '',
  `library_id` bigint(20) unsigned DEFAULT NULL,
  `video_id` bigint(20) unsigned DEFAULT NULL,
  `resource_id` bigint(20) unsigned DEFAULT NULL,
  `quiz_id` bigint(20) unsigned DEFAULT NULL,
  `assignment_id` bigint(20) unsigned DEFAULT NULL,
  `subscription_id` bigint(20) unsigned DEFAULT NULL,
  `certification_id` bigint(20) unsigned DEFAULT NULL,
  `video_time` double DEFAULT NULL,
  `notes` text,
  `result` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
