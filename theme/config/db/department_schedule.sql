CREATE TABLE `department_schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department_id` int(11) NOT NULL,
  `closed` TINYINT(1) NOT NULL,
  `schedule_date` date NULL,
  `opening_hours` text NOT NULL DEFAULT "",

  PRIMARY KEY  (`id`),
  KEY `department_id` (`department_id`),
  CONSTRAINT `department_schedule_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `system_departments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

