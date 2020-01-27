CREATE TABLE `SITE_DB`.`system_tally_payouts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tally_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `amount` int(11) NOT NULL,
  `comment` text NOT NULL,

  PRIMARY KEY (`id`),
  KEY `tally_id` (`tally_id`),

  CONSTRAINT `system_tally_payouts_ibfk_1` FOREIGN KEY (`tally_id`) REFERENCES `SITE_DB`.`system_tallies` (`id`) ON UPDATE CASCADE ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8;