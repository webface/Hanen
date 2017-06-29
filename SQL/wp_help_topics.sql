# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.6.35)
# Database: eot_v5
# Generation Time: 2017-06-29 15:53:20 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table wp_help_topics
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_help_topics`;

CREATE TABLE `wp_help_topics` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `video_filename` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `wp_help_topics` WRITE;
/*!40000 ALTER TABLE `wp_help_topics` DISABLE KEYS */;

INSERT INTO `wp_help_topics` (`ID`, `title`, `summary`, `video_filename`)
VALUES
	(1,'Add/Remove Staff','Use this feature to add or remove staff from a staff group.','add_remove_staff'),
	(2,'Intro to My Dashboard','The Dashboard is your central hub for accessing content available for your account.','dashboard'),
	(3,'Emailing Staff','Use this feature to send out mass emails to staff members.','email_staff'),
	(4,'Managing Assignments','Use this feature to manage assignments for your staff members.','manage_assignments'),
	(5,'Managing Staff Accounts','Use this feature to manage your staff accounts and their subscriptions.','manage_staff_accounts'),
	(6,'Managing Staff Groups','Use this feature to manage groups of your staff members and to assign content to those groups.','manage_staff_groups'),
	(7,'Viewing Statistics','View statistics about your staff members\' activities and track their progress.','statistics'),
	(8,'Viewing Content','This page allows you to view the content you have available for your staff members through your purchased subscriptions.','view_content'),
	(9,'Max Out EOT','Quickly learn how to get the most out of your subscription this season by watching this fact-packed screencast.','get_the_most'),
	(10,'Watch Some Videos',' This page allows you to view the content you have available for your staff members through your purchased subscription.','some_videos'),
	(11,'Pick a Default Course','Use this feature to manage the default courses for your staff members.','default_course'),
	(12,'Modify a Default Course','Use this feature to modify the default courses for your staff members.','modify_default'),
	(13,'Create a New Course','Use this feature to create you own custom course for your staff memebers.','create_course'),
	(14,'Publish a Course','Before you can enroll your staff in a course, it must be published.','publish_course'),
	(15,'Modify a Published Course','Use this feature to modify a published course.','modify_published'),
	(16,'Add Staff Information','Use this feature to add staff and enroll them into courses. ','add_staff'),
	(17,'Change Course Enrollment','Use this feature to modify user enrollments in specific courses.','change_enroll'),
	(18,'Check Statistics','Use this feature to get statistics on how your staff is progressing through their assigned courses.','check_stats'),
	(19,'Upload Custom Content','Use this feature to upload your own custom content so that your staff has access to it.','upload_custom'),
	(20,'Create a Custom Quiz','Use this feature to create custom quizes for your staff.','create_custom_quiz');

/*!40000 ALTER TABLE `wp_help_topics` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
