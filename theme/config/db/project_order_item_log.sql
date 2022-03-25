CREATE TABLE `SITE_DB`.`project_order_item_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_item_id` int(11) NOT NULL,
  `department_pickupdate_order_item_id` int(11) DEFAULT NULL,
  `department_pickupdate_id` int(11) DEFAULT NULL,
  `pickupdate` varchar(50) DEFAULT NULL,
  `department` varchar(50) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  KEY `department_pickupdate_order_item_id` (`department_pickupdate_order_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
