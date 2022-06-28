CREATE TABLE `SITE_DB`.`project_tallies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,

  `department_id` int(11) NOT NULL,

  `start_cash` float DEFAULT NULL,
  `end_cash` float DEFAULT NULL,
  `deposited` float DEFAULT NULL,

  `comment` text NOT NULL DEFAULT '',

  `status` int(11) NOT NULL DEFAULT 1,

  `opened_by` int(11) NOT NULL,
  `closed_by` int(11) DEFAULT NULL,

  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_at` timestamp NULL DEFAULT NULL,


  PRIMARY KEY (`id`),
  KEY `department_id` (`department_id`),
  KEY `opened_by` (`opened_by`),
  KEY `closed_by` (`closed_by`),

  CONSTRAINT `project_tallies_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `SITE_DB`.`project_departments` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `project_tallies_ibfk_2` FOREIGN KEY (`opened_by`) REFERENCES `SITE_DB`.`users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `project_tallies_ibfk_3` FOREIGN KEY (`closed_by`) REFERENCES `SITE_DB`.`users` (`id`) ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8;