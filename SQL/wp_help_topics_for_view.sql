CREATE TABLE `wp_help_topics_for_view` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `view_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','presenter','director','student','sales','sales_manager','umbrella_manager') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `topic_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `wp_help_topics_for_view` (`id`, `view_name`, `role`, `topic_id`)
VALUES
	(24,'dashboard','director',10),
	(25,'dashboard','director',11),
	(26,'dashboard','director',12),
	(27,'dashboard','director',13),
	(28,'dashboard','director',14),
	(29,'dashboard','director',15),
	(30,'dashboard','director',16),
	(31,'dashboard','director',17),
	(32,'dashboard','director',18),
	(33,'dashboard','director',19),
	(34,'dashboard','director',20);