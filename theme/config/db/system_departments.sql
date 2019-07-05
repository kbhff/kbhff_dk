CREATE TABLE `SITE_DB`.`system_departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `abbreviation` varchar(3) NOT NULL,
  `address1` varchar(50) NOT NULL DEFAULT "",
  `address2` text NOT NULL DEFAULT "",
  `city` varchar(50) NOT NULL DEFAULT "",
  `postal` varchar(50) NOT NULL DEFAULT "",
  `email` varchar(50) NOT NULL DEFAULT "",
  `opening_hours` text NOT NULL DEFAULT "",
  `mobilepay_id` varchar(50) DEFAULT NULL,
  `accepts_signup` int(1) NOT NULL DEFAULT 1,

  `geolocation` varchar(255) NOT NULL DEFAULT '',
  `latitude` double NOT NULL DEFAULT 0,
  `longitude` double NOT NULL DEFAULT 0,

  `description` text NOT NULL DEFAULT '',
  `html` text NOT NULL DEFAULT '',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;