CREATE TABLE `SITE_DB`.`project_pickupdate_order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pickupdate_id` int(11) NOT NULL,
  `order_item_id` int(11) NOT NULL,

  PRIMARY KEY (`id`),
  KEY `pickupdate_id` (`pickupdate_id`),
  KEY `order_item_id` (`order_item_id`),
  CONSTRAINT `project_pickupdate_order_items_ibfk_1` FOREIGN KEY (`pickupdate_id`) REFERENCES `SITE_DB`.`project_pickupdates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `project_pickupdate_order_items_ibfk_2` FOREIGN KEY (`order_item_id`) REFERENCES `SITE_DB`.`shop_order_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
