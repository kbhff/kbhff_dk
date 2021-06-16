INSERT INTO `SITE_DB`.`system_payment_methods` (`id`, `name`, `classname`, `description`, `gateway`, `state`, `position`)
VALUES
	(1,'Bankoverførsel','banktransfer','Regular bank transfer. Preferred option.',NULL,'admin',1),
	(2,'Betalingskort','stripe','','stripe','public',2),
	(3,'MobilePay','mobilepay','MobilePay payment.',NULL,'memberhelp',3),
	(4,'Cash','cash','Cash payment',NULL,'admin',4);
