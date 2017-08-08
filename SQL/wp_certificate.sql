CREATE TABLE `wp_certificates` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `org_id` bigint(20) NOT NULL DEFAULT '0',
  `course_id` bigint(20) unsigned DEFAULT NULL,
  `course_name` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `filename` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `date_created` date NOT NULL DEFAULT '1000-01-01',
  `status` enum('pending','conferred') COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'conferred',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

