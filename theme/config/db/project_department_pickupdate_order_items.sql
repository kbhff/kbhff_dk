CREATE TABLE `SITE_DB`.`project_department_pickupdate_order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department_id` int(11) NOT NULL DEFAULT 1,
  `pickupdate_id` int(11) NOT NULL,
  `order_item_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,

  PRIMARY KEY (`id`),
  KEY `department_id` (`department_id`),
  KEY `pickupdate_id` (`pickupdate_id`),
  KEY `order_item_id` (`order_item_id`),
  CONSTRAINT `project_department_order_items_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `SITE_DB`.`project_departments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `project_department_pickupdate_order_items_ibfk_2` FOREIGN KEY (`pickupdate_id`) REFERENCES `SITE_DB`.`project_pickupdates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `project_department_pickupdate_order_items_ibfk_3` FOREIGN KEY (`order_item_id`) REFERENCES `SITE_DB`.`shop_order_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `project_department_pickupdate_order_items_ibfk_4` FOREIGN KEY (`pickupdate_id`) REFERENCES `SITE_DB`.`project_department_pickupdates` (`pickupdate_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
