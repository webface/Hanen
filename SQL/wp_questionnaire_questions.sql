CREATE TABLE `wp_questionnaire_questions` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `library_id` bigint(20) unsigned DEFAULT NULL,
  `question` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int(3) NOT NULL DEFAULT '50',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `wp_questionnaire_questions` (`ID`, `library_id`, `question`, `answer`, `order`)
VALUES
	(1,1,'What type of Camp are you?','{\"1\":\"Day\",\"2\":\"Resident\",\"3\":\"A combination of day and resident\",\"4\":\"An afterschool program\"}',1),
	(2,1,'The young people who attend our program are primarily:','{\r\n   \"1\": \"girls\",\r\n   \"2\": \"boys\",\r\n   \"3\": \"boys and girls\"\r\n}',2),
	(3,1,'Regarding Campers with special needs, we primarily serve:','{\n   \"1\": \"very few Campers with identified special needs\",\n   \"2\": \"Campers with Autistic Spectrum Disorders\",\n   \"3\": \"Campers with special medical and physical needs\",\n   \"4\": \"children who are burn survivors\"\n}',3),
	(4,1,'Would you like to include 2 additional modules on either Jewish or Christian perspectives on youth leadership?','{\r\n   \"1\": \"Yes, please include “Christian Perspectives, Part I” and “Christian Perspectives, Part II” for my staff.\",\r\n   \"2\": \"Yes, please include “Jewish Perspectives, Part I” and “Jewish Perspectives, Part II” for my staff.\",\r\n   \"3\": \"No, thanks. I understand I can always have my staff watch these later, if there’s time.\"\r\n}',4);
