-- --------------------------------------------------------

--
-- Table structure for table `wp_category`
--

CREATE TABLE `wp_category` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `library_id` int(11) NOT NULL,
  `order` int(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wp category`
--

INSERT INTO `wp_category` (`id`, `name`, `library_id`, `order`) VALUES
(1, 'Physical and Emotional Safety', 1, 4),
(2, 'Supervision', 1, 5),
(3, 'Leadership', 1, 1),
(4, 'Youth Development and Play', 1, 2),
(5, 'Mental Health and Behavior', 1, 3),
(7, 'Creative Literacy', 1, 6),
(15, 'Physical and Emotional Safety', 6, 4),
(16, 'Supervision', 6, 5),
(17, 'Leadership', 6, 1),
(18, 'Youth Development and Play', 6, 2),
(19, 'Mental Health and Behavior', 6, 3),
(20, 'Physical and Emotional Safety', 7, 4),
(21, 'Supervision', 7, 5),
(22, 'Leadership', 7, 1),
(23, 'Youth Development and Play', 7, 2),
(24, 'Mental Health and Behavior', 7, 3),
(25, 'Physical and Emotional Safety', 8, 4),
(26, 'Supervision', 8, 5),
(27, 'Leadership', 8, 1),
(28, 'Youth Development and Play', 8, 2),
(29, 'Mental Health and Behavior', 8, 3);
