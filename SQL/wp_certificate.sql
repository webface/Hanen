CREATE TABLE `wp_certificate` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `certification_id` bigint(20) NOT NULL DEFAULT '0',
  `user_id` bigint(20) unsigned NOT NULL,
  `course_name` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `course_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(80) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `filename` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `datecreated` date NOT NULL DEFAULT '0000-00-00',
  `date_enrolled` date NOT NULL DEFAULT '0000-00-00',
  `expirydate` NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('pending','conferred') COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'conferred',
  `org_id` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;