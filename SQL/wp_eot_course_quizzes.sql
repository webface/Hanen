

CREATE TABLE `wp_course_quizzes` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `course_id` bigint(20) DEFAULT NULL,
  `quiz_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `wp_course_quizzes` (`ID`, `course_id`, `quiz_id`)
VALUES
	(12,16,20),
	(13,16,18),
	(14,16,21),
	(20,13,7),
	(21,13,4),
	(23,13,5),
	(26,13,20),
	(27,13,18);

