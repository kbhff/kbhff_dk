<?php
// enable access control
$access_item["/"] = true;
$access_item["/new"] = true;
$access_item["/save"] = "/new";

if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");

// get REST parameters
$action = $page->actions();

// define which model this controller is referring to
include_once("classes/shop/pickupdate.class.php");
$model = new Pickupdate();


// page info
$page->bodyClass("pickup_date");
$page->pageTitle("Pickup dates");


if(is_array($action) && count($action)) {

	// LIST/EDIT/NEW
	if(preg_match("/^(list|edit|new)$/", $action[0])) {

		$page->page(array(
			"type" => "janitor",
			"templates" => "janitor/pickupdate/".$action[0].".php"
		));
		exit();
	}

	// Class interface
	else if(security()->validateCsrfToken() && preg_match("/[a-zA-Z]+/", $action[0])) {

		// check if custom function exists on User class
		if($model && method_exists($model, $action[0])) {

			// output custom function to screen as JSON
			$output = new Output();
			$output->screen($model->{$action[0]}($action));
			exit();
		}
	}

}

// bad command
$page->page(array(
	"templates" => "pages/404.php"
));

?>
