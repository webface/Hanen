
CREATE TABLE `wp_course_resources` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `course_id` bigint(20) unsigned DEFAULT NULL,
  `resource_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `wp_course_resources` (`ID`, `course_id`, `resource_id`)
VALUES
	(3,13,16),
	(14,13,95),
	(15,13,1),
	(16,13,3),
	(17,13,10);

