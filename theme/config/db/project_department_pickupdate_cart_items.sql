CREATE TABLE `SITE_DB`.`project_department_pickupdate_cart_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department_pickupdate_id` int(11) NOT NULL,
  `cart_item_id` int(11) NOT NULL,

  PRIMARY KEY (`id`),
  KEY `departent_pickupdate_id` (`department_pickupdate_id`),
  KEY `cart_item_id` (`cart_item_id`),
  CONSTRAINT `project_department_pickupdate_cart_items_ibfk_1` FOREIGN KEY (`department_pickupdate_id`) REFERENCES `SITE_DB`.`project_department_pickupdates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `project_department_pickupdate_cart_items_ibfk_2` FOREIGN KEY (`cart_item_id`) REFERENCES `SITE_DB`.`shop_cart_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
