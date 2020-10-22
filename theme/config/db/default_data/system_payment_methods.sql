INSERT INTO `kbhff_dk`.`system_payment_methods` (`id`, `name`, `classname`, `description`, `gateway`, `state`, `position`)
VALUES
	(1,'Bank transfer','banktransfer','Regular bank transfer. Preferred option.',NULL,NULL,1),
	(2,'Credit Card','stripe','Stripe credit card payment - 1.4% transaction fee. *','stripe','public',2),
	(3,'MobilePay','mobilepay','MobilePay payment.',NULL,NULL,3),
	(4,'Cash','cash','Cash payment',NULL,NULL,4);
