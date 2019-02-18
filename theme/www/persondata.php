<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();


$page->bodyClass("persondata");
$page->pageTitle("Persondata");

// /persondata
// show person-data directly 
$page->page(array(
	"templates" => "pages/person_data.php",
	"type" => "login"
	)
);
exit();


?>
