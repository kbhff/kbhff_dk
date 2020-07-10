CREATE TABLE `SITE_DB`.`project_pickupdates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pickupdate` varchar(50) NOT NULL,

  `comment` text,

  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_at` timestamp NULL DEFAULT NULL,

  PRIMARY KEY (`id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;