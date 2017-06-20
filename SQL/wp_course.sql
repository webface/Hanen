CREATE TABLE `wp_course` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_name` varchar(255) DEFAULT NULL,
  `course_description` text NOT NULL,
  `org_id` int(11) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `due_date_after_enrollment` datetime DEFAULT NULL,
  `subscription_id` int(11) NOT NULL,
  `num_enrolled` int(11) NOT NULL DEFAULT '0',
  `num_completed` int(11) NOT NULL DEFAULT '0',
  `num_not_started` int(11) NOT NULL DEFAULT '0',
  `num_in_progress` int(11) NOT NULL DEFAULT '0',
  `num_passed` int(11) NOT NULL DEFAULT '0',
  `num_failed` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
