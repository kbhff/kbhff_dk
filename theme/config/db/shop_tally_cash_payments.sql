CREATE TABLE `SITE_DB`.`project_tally_cash_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tally_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,

  PRIMARY KEY (`id`),
  KEY `tally_id` (`tally_id`),
  KEY `payment_id` (`payment_id`),

  CONSTRAINT `project_tally_cash_payments_ibfk_1` FOREIGN KEY (`tally_id`) REFERENCES `SITE_DB`.`project_tallies` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `project_tally_cash_payments_ibfk_2` FOREIGN KEY (`payment_id`) REFERENCES `SITE_DB`.`shop_payments` (`id`) ON UPDATE CASCADE ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8;