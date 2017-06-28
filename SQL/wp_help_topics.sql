CREATE TABLE `wp_help_topics` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` text NOT NULL,
  `full_content` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `wp_help_topics` (`id`, `title`, `summary`, `full_content`)
VALUES
	(1,'Add/Remove Staff','Use this feature to add or remove staff from a staff group.','add_remove_staff'),
	(2,'Intro to My Dashboard','The Dashboard is your central hub for accessing content available for your account.','dashboard'),
	(3,'Emailing Staff','Use this feature to send out mass emails to staff members.','email_staff'),
	(4,'Managing Assignments','Use this feature to manage assignments for your staff members.','manage_assignments'),
	(5,'Managing Staff Accounts','Use this feature to manage your staff accounts and their subscriptions.','manage_staff_accounts'),
	(6,'Managing Staff Groups','Use this feature to manage groups of your staff members and to assign content to those groups.','manage_staff_groups'),
	(7,'Viewing Statistics','View statistics about your staff members\' activities and track their progress.','statistics'),
	(8,'Viewing Content','This page allows you to view the content you have available for your staff members through your purchased subscriptions.','view_content'),
	(9,'Max Out EOT','Quickly learn how to get the most out of your subscription this season by watching this fact-packed screencast.','get_the_most');
	(10,'Watch Some Videos',' This page allows you to view the content you have available for your staff members through your purchased subscription.','some_videos'),
	(11,'Pick a Default Course','Use this feature to manage the default courses for your staff members.','default_course'),
	(12,'Modify a Default Course','Use this feature to modify the default courses for your staff members.','modify_default'),
	(13,'Create a New Course','Use this feature to create you own custom course for your staff memebers.','create_course'),
	(14,'Publish a Course','Before you can enroll your staff in a course, it must be published.','publish_course'),
	(15,'Modify a Published Course','Use this feature to modify a published course.','modify_published'),
	(16,'Add Staff Information','Use this feature to add staff and enroll them into courses. ','add_staff'),
	(17,'Change Course Enrollment','Use this feature to modify user enrollments in specific courses.','change_enroll'),
	(18,'Check Statistics','Use this feature to get statistics on how your staff is progressing through their assigned courses.','check_stats'),
	(19,'Upload Custom Content','Use this feature to upload your own custom content so that your staff has access to it.','upload_custom'),
	(20,'Create a Custom Quiz','Use this feature to create custom quizes for your staff.','create_custom_quiz')