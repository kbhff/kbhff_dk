CREATE TABLE `SITE_DB`.`project_kbhffmessage_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `recipient` varchar(50) NOT NULL DEFAULT '',
  `html` text NOT NULL DEFAULT '',

  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,


  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;