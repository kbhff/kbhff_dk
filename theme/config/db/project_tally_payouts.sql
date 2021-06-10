CREATE TABLE `SITE_DB`.`project_tally_payouts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tally_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `amount` float NOT NULL,
  `comment` text NOT NULL,

  PRIMARY KEY (`id`),
  KEY `tally_id` (`tally_id`),

  CONSTRAINT `project_tally_payouts_ibfk_1` FOREIGN KEY (`tally_id`) REFERENCES `SITE_DB`.`project_tallies` (`id`) ON UPDATE CASCADE ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8;