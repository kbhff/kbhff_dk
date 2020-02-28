CREATE TABLE `SITE_DB`.`item_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,

  `name` varchar(255) NOT NULL,
  `start_availability_date` varchar(255) DEFAULT '',
  `end_availability_date` varchar(255) DEFAULT '',
  -- `producer` int(11) NULL DEFAULT NULL,
  `product_type` int(11) DEFAULT NULL,

  `description` text /**NOT NULL DEFAULT ''*/,

  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  KEY `product_type` (`product_type`),
  CONSTRAINT `item_product_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `SITE_DB`.`items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `item_product_ibfk_2` FOREIGN KEY (`product_type`) REFERENCES `SITE_DB`.`system_product_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;