CREATE TABLE `SITE_DB`.`item_weeklybag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,

  `name` varchar(255) NOT NULL,
  `week` int(11) NULL DEFAULT NULL,
  `year` int(11) NULL DEFAULT NULL,

  `html` text NOT NULL DEFAULT '',
  `full_description` text NOT NULL DEFAULT '',

  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  CONSTRAINT `item_weeklybag_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `SITE_DB`.`items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;