CREATE TABLE `wp_certificate_syllabus` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `course_id` bigint(20) NOT NULL DEFAULT '0',
  `course_name` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `modules` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;