CREATE TABLE `SITE_DB`.`shop_tallies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,

  `department_id` int(11) NOT NULL,

  `start_cash` int(11) NOT NULL,
  `end_cash` int(11) NOT NULL,
  `deposited` int(11) NOT NULL,
  `misc_cash_revenue` int(11) NOT NULL,

  `comment` text NOT NULL,

  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_at` timestamp NULL DEFAULT NULL,

  `status` int(11) NOT NULL DEFAULT 1,


  PRIMARY KEY (`id`),
  KEY `department_id` (`department_id`),

  CONSTRAINT `shop_tallies_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `SITE_DB`.`system_departments` (`id`) ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8;