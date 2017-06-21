
CREATE TABLE `wp_upgrade` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `org_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `subscription_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `price` decimal(9,2) NOT NULL DEFAULT '0.00',
  `accounts` int(11) NOT NULL DEFAULT '0',
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `method` enum('stripe','check','paypal','free','pending','cardflex') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'stripe',
  `discount_note` text COLLATE utf8mb4_unicode_ci,
  `other_note` text COLLATE utf8mb4_unicode_ci,
  `rep_id` bigint(20) DEFAULT NULL,
  `trans_id` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
