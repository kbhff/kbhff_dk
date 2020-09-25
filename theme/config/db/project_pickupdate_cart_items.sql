CREATE TABLE `SITE_DB`.`project_pickupdate_cart_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pickupdate_id` int(11) NOT NULL,
  `cart_item_id` int(11) NOT NULL,

  PRIMARY KEY (`id`),
  KEY `pickupdate_id` (`pickupdate_id`),
  KEY `cart_item_id` (`cart_item_id`),
  CONSTRAINT `project_pickupdate_cart_items_ibfk_1` FOREIGN KEY (`pickupdate_id`) REFERENCES `SITE_DB`.`project_pickupdates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `project_pickupdate_cart_items_ibfk_2` FOREIGN KEY (`cart_item_id`) REFERENCES `SITE_DB`.`shop_cart_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
