<?php

// enable access control
$access_item["/"] = true;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");
include_once("classes/shop/tally.class.php");

// get REST parameters
$action = $page->actions();

// define which model this controller is associated with
$model = new Tally();

// page info
$page->bodyClass("tally");
$page->pageTitle("Tallies");

// Check if there are REST parameters in the request
if(is_array($action) && count($action)) {

	// LIST/EDIT/NEW ITEM
	if(preg_match("/^(list|edit|new)$/", $action[0])) {

		$page->page(array(
			"type" => "janitor",
			"templates" => "janitor/tally/".$action[0].".php"
		));
		exit();
	}

	// Class interface
	else if(security()->validateCsrfToken() && preg_match("/[a-zA-Z]+/", $action[0])) {

		// check if custom function exists on Tally class
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
