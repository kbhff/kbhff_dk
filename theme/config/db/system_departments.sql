CREATE TABLE `SITE_DB`.`system_departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `address1` varchar(50) NOT NULL DEFAULT "",
  `address2` text NOT NULL DEFAULT "",
  `city` varchar(50) NOT NULL DEFAULT "",
  `postal` varchar(50) NOT NULL DEFAULT "",
  `email` varchar(50) NOT NULL DEFAULT "",
  `opening_hours` text NOT NULL DEFAULT "",



  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;