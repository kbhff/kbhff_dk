CREATE TABLE `SITE_DB`.`item_membership` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,

  `name` varchar(255) NOT NULL,
  `classname` varchar(100) NOT NULL DEFAULT '',
  `subscribed_message_id` int(11) NULL DEFAULT NULL,
  `description` text NOT NULL DEFAULT '',
  `introduction` text NOT NULL DEFAULT '',
  `html` text NOT NULL DEFAULT '',
  
  `fixed_url_identifier` varchar(100) DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT '0',

  PRIMARY KEY (`id`),
  UNIQUE KEY `fixed_url_identifier` (`fixed_url_identifier`),
  KEY `item_id` (`item_id`),
  CONSTRAINT `item_membership_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `SITE_DB`.`items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `item_membership_ibfk_2` FOREIGN KEY (`subscribed_message_id`) REFERENCES `SITE_DB`.`items` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;