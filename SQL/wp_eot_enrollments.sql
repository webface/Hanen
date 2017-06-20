CREATE TABLE `wp_enrollments` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `course_id` bigint(20) unsigned DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `org_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `status` enum('not_started','in_progress','completed','passed','failed','pending_review') NOT NULL DEFAULT 'not_started',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
