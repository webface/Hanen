
CREATE TABLE `wp_subscriptions` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `manager_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `library_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `start_date` date NOT NULL DEFAULT '0000-00-00',
  `end_date` date NOT NULL DEFAULT '0000-00-00',
  `method` enum('cardflex','stripe','check','paypal','free','pending') COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'stripe',
  `trans_id` text COLLATE utf8mb4_unicode_520_ci,
  `trans_date` date NOT NULL DEFAULT '0000-00-00',
  `price` decimal(9,2) NOT NULL DEFAULT '0.00',
  `dash_price` decimal(9,2) NOT NULL DEFAULT '0.00',
  `staff_price` decimal(9,2) NOT NULL DEFAULT '0.00',
  `data_disk_price` decimal(9,2) NOT NULL DEFAULT '0.00',
  `dash_discount` decimal(9,2) NOT NULL DEFAULT '0.00',
  `staff_discount` decimal(9,2) NOT NULL DEFAULT '0.00',
  `staff_credits` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','disabled','pending') COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'active',
  `rep_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `notes` text COLLATE utf8mb4_unicode_520_ci,
  `setup` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;