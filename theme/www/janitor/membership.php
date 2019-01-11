<?php
// enable access control
$access_item["/"] = true;

$access_item["/subscription"] = true;
$access_item["/updateSubscriptionMethod"] = "/subscription";

$access_item["/new"] = true;
$access_item["/save"] = "/new";

if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");

// get REST parameters
$action = $page->actions();

// define which model this controller is referring to
$IC = new Items();
$itemtype = "membership";
$model = $IC->typeObject($itemtype);

// page info
$page->bodyClass("membership");
$page->pageTitle("Memberships");


if(is_array($action) && count($action)) {

	// LIST/EDIT/NEW
	if(preg_match("/^(list|edit|new)$/", $action[0])) {

		$page->page(array(
			"type" => "janitor",
			"templates" => "janitor/membership/".$action[0].".php"
		));
		exit();
	}

	// Class interface
	else if($page->validateCsrfToken() && preg_match("/[a-zA-Z]+/", $action[0])) {

		// check if custom function exists on Membership class
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
