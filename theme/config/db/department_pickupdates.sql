CREATE TABLE `SITE_DB`.`department_pickupdates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department_id` int(11) NOT NULL,
  `pickupdate_id` int(11) NOT NULL,

  PRIMARY KEY (`id`),
  KEY `department_id` (`department_id`),
  KEY `pickupdate_id` (`pickupdate_id`),
  CONSTRAINT `department_pickupdates_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `SITE_DB`.`system_departments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `department_pickupdates_ibfk_2` FOREIGN KEY (`pickupdate_id`) REFERENCES `SITE_DB`.`shop_pickupdates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
