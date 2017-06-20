CREATE TABLE `wp_courses` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `course_name` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `course_description` text COLLATE utf8mb4_unicode_520_ci,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `owner_id` bigint(20) unsigned DEFAULT NULL,
  `due_date_after_enrollment` datetime DEFAULT '0000-00-00 00:00:00',
  `subscription_id` bigint(20) unsigned DEFAULT NULL,
  `num_enrolled` int(11) NOT NULL DEFAULT 0,
  `num_completed` int(11) NOT NULL DEFAULT 0,
  `num_not_started` int(11) NOT NULL DEFAULT 0,
  `num_in_progress` int(11) NOT NULL DEFAULT 0,
  `num_passed` int(11) NOT NULL DEFAULT 0,
  `num_failed` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
