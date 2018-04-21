<?php
$access_item["/"] = true;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();
$IC = new Items();



$page->bodyClass("restructure");
$page->pageTitle("Restructure tool");


if(is_array($action) && count($action)) {

	// LIST/EDIT/NEW ITEM
	if(preg_match("/^(done)$/", $action[0])) {

		$page->page(array(
			"type" => "janitor",
			"templates" => "janitor/restructure/done.php"
		));
		exit();
	}

	// Class interface
	else if($page->validateCsrfToken() && preg_match("/[run]+/", $action[0])) {

		include_once("classes/system/upgrade.class.php");
		$UC = new Upgrade();


		$query = new Query();

		// Code Igniter upgrades

		// (2.0.3)
		// CREATE INDEX last_activity_idx ON ci_sessions(last_activity); 
		$UC->addKey(SITE_DB.".ff_sessions", "last_activity", "last_activity_idx");

		// ALTER TABLE ci_sessions MODIFY user_agent VARCHAR(120);
		$UC->modifyColumn(SITE_DB.".ff_sessions", "user_agent", "VARCHAR(120)");

		// (2.1.1)
		// ALTER TABLE ci_sessions CHANGE ip_address ip_address varchar(45) default '0' NOT NULL
		$UC->modifyColumn(SITE_DB.".ff_sessions", "ip_address", "VARCHAR(45) DEFAULT '0' NOT NULL");





		// Get Members data from ff_persons
		// privacy = newsletter
		// adr2 = streetname
		// uid = member id
		$sql = "SELECT firstname, middlename, lastname, adr2, streetno, floor, door, zip, city, tel, email, privacy, last_login, created, changed, uid FROM kbhff_dk.ff_persons";
		$query->sql($sql);

		print_r($query->results());
		// INSERT DATA IN JANITOR STRUCTURE
		// NAME DATA -> users
		// ADDRESS DATA -> user_addresses - SKIP

		// EMAIL + TEL -> user_usernames

		// PRIVACY -> user_maillists
		// UID -> user_members


		// DO NOT INSERT PASSWORDS

		// DELETE COLUMNS WITH OLD/MOVED DATA



		// DELETE UNUSED TABLES

		// ff_zipcodes
		$UC->dropTable(SITE_DB.".ff_zipcodes");

		// ff_jobs
		$UC->dropTable(SITE_DB.".ff_jobs");

		// ff_chores
		$UC->dropTable(SITE_DB.".ff_chores");

		// ff_xfer
		$UC->dropTable(SITE_DB.".ff_xfer");

		// fornavne
		$UC->dropTable(SITE_DB.".fornavne");

		// piger
		$UC->dropTable(SITE_DB.".piger");

		// unisex
		$UC->dropTable(SITE_DB.".unisex");

		// drenge
		$UC->dropTable(SITE_DB.".drenge");


		exit();

	}

}

$page->page(array(
	"type" => "janitor",
	"templates" => "janitor/restructure/index.php"
));

?>
