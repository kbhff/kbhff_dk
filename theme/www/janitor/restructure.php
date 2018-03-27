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

		// Get Members data from ff_persons
		// privacy = newsletter
		// adr2 = streetname
		// uid = member id
		$sql = "SELECT firstname, middlename, lastname, adr2, streetno, floor, door, zip, city, tel, email, privacy, last_login, created, changed, uid FROM kbhff1.ff_persons";
		$query->sql($sql);

		// INSERT DATA IN JANITOR STRUCTURE
		// NAME DATA -> users
		// ADDRESS DATA -> user_addresses
		// EMAIL + TEL -> user_usernames
		// PRIVACY -> user_maillists
		// UID -> user_members

		// DO NOT INSERT PASSWORDS

		// DELETE COLUMNS WITH OLD/MOVED DATA


		// DELETE UNUSED TABLES
		// ff_zipcodes
		// ff_jobs
		// ff_chores
		// ff_xfer
		// fornavne
		// piger
		// unisex
		// drenge


	}

}

$page->page(array(
	"type" => "janitor",
	"templates" => "janitor/restructure/index.php"
));

?>
