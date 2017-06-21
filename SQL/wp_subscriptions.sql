
CREATE TABLE `wp_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org_id` int(11) NOT NULL DEFAULT '0',
  `manager_id` int(11) NOT NULL DEFAULT '0',
  `library_id` int(11) NOT NULL DEFAULT '0',
  `start_date` date NOT NULL DEFAULT '0000-00-00',
  `end_date` date NOT NULL DEFAULT '0000-00-00',
  `method` enum('cardflex','stripe','check','paypal','free','pending') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cardflex',
  `trans_id` text COLLATE utf8mb4_unicode_ci,
  `trans_date` date NOT NULL DEFAULT '0000-00-00',
  `price` decimal(9,2) NOT NULL DEFAULT '0.00',
  `dash_price` decimal(9,2) NOT NULL DEFAULT '0.00',
  `staff_price` decimal(9,2) NOT NULL DEFAULT '0.00',
  `data_disk_price` decimal(9,2) NOT NULL DEFAULT '0.00',
  `dash_discount` decimal(9,2) NOT NULL DEFAULT '0.00',
  `staff_discount` decimal(9,2) NOT NULL DEFAULT '0.00',
  `staff_credits` int(11) NOT NULL DEFAULT '0',
  `status` enum('active','disabled','pending') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `rep_id` int(11) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `setup` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=125 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;