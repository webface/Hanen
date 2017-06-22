# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.6.35)
# Database: eot_china_dev
# Generation Time: 2017-06-21 20:38:38 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table wp_questionnaire_questions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_questionnaire_questions`;

CREATE TABLE `wp_questionnaire_questions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `library_id` int(11) DEFAULT NULL,
  `question` varchar(255) DEFAULT NULL,
  `answer` text,
  `order` int(3) NOT NULL DEFAULT '50',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `wp_questionnaire_questions` WRITE;
/*!40000 ALTER TABLE `wp_questionnaire_questions` DISABLE KEYS */;

INSERT INTO `wp_questionnaire_questions` (`id`, `library_id`, `question`, `answer`, `order`)
VALUES
	(1,1,'What type of Camp are you?','{\"1\":\"Day\",\"2\":\"Resident\",\"3\":\"A combination of day and resident\",\"4\":\"An afterschool program\"}',1),
	(2,1,'The young people who attend our program are primarily:','{\r\n   \"1\": \"girls\",\r\n   \"2\": \"boys\",\r\n   \"3\": \"boys and girls\"\r\n}',2),
	(3,1,'Regarding Campers with special needs, we primarily serve:','{\n   \"1\": \"very few Campers with identified special needs\",\n   \"2\": \"Campers with Autistic Spectrum Disorders\",\n   \"3\": \"Campers with special medical and physical needs\",\n   \"4\": \"children who are burn survivors\"\n}',3),
	(4,1,'Would you like to include 2 additional modules on either Jewish or Christian perspectives on youth leadership?','{\r\n   \"1\": \"Yes, please include “Christian Perspectives, Part I” and “Christian Perspectives, Part II” for my staff.\",\r\n   \"2\": \"Yes, please include “Jewish Perspectives, Part I” and “Jewish Perspectives, Part II” for my staff.\",\r\n   \"3\": \"No, thanks. I understand I can always have my staff watch these later, if there’s time.\"\r\n}',4),
	(7,6,'What is the name of the company?','{\"1\":\"EOT\",\"2\":\"Target\"}',50),
	(8,6,'How many kids can the camp accomodate?','{\"1\":\"10-20\",\"2\":\"20-50\"}',50),
	(9,8,'A or B?','{\"1\":\"A\",\"2\":\"B\"}',50),
	(11,7,'Overnight A, B or C','{\"1\":\"A\",\"2\":\"B\",\"3\":\"C\"}',50);

/*!40000 ALTER TABLE `wp_questionnaire_questions` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
