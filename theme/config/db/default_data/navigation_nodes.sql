INSERT INTO `kbhff_dk`.`navigation_nodes` (`id`, `navigation_id`, `node_name`, `node_link`, `node_item_id`, `node_item_controller`, `node_classname`, `node_target`, `node_fallback`, `relation`, `position`)
VALUES
	(DEFAULT,3,'Min side','/profil',NULL,NULL,'profile',NULL,"/login",0,0),
	(DEFAULT,3,'Grøntshoppen','/butik',NULL,NULL,'shop',NULL,NULL,0,0),
	(DEFAULT,4,'Min side','/profil',NULL,NULL,'profile',NULL,"/login",0,0),
	(DEFAULT,4,'Frivillig på arbejde','/medlemshjaelp',NULL,NULL,'volunteer',NULL,NULL,0,0),
	(DEFAULT,5,'Min side','/profil',NULL,NULL,'profile',NULL,"/login",0,0),
	(DEFAULT,5,'Medlemshjælp','/medlemshjaelp',NULL,NULL,'volunteer',NULL,NULL,0,0),

	(DEFAULT,2,'Departments','/janitor/departments/list',NULL,NULL,'departments',NULL,NULL,0,0),
	(DEFAULT,2,'Pages','/janitor/admin/page/list',NULL,NULL,'pages',NULL,NULL,0,0),
	(DEFAULT,2,'Signup fees','/janitor/signupfee/list',NULL,NULL,'signupfees',NULL,NULL,0,0),
	(DEFAULT,2,'Weekly bags','/janitor/weeklybag/list',NULL,NULL,'weeklybags',NULL,NULL,0,0);

