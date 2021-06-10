<?php

// enable access control
$access_item["/"] = true;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");

// get REST parameters
$action = $page->actions();

$IC = new Items();
$model = $IC->typeObject("message");

// page info
$page->bodyClass("mass_mail_log");
$page->pageTitle("Mass mail activity log");

// Check if there are REST parameters in the request
if(is_array($action) && count($action)) {

	// LIST/VIEW
	if(preg_match("/^(list|view)$/", $action[0])) {

		$page->page(array(
			"type" => "janitor",
			"templates" => "janitor/mass_mail_log/".$action[0].".php"
		));
		exit();
	}

}

// bad command
$page->page(array(
	"templates" => "pages/404.php"
));

?>
