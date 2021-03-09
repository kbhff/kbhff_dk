<?php

// enable access control
$access_item["/list"] = true;
$access_item["/edit"] = "/list";
$access_item["/updateUserUserGroup"] = "/list";

if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");
include_once("classes/users/superuser.class.php");

// get REST parameters
$action = $page->actions();

// define which model this controller is associated with
$model = new SuperUser();

// page info
$page->bodyClass("user_group");
$page->pageTitle("User groups");

// Check if there are REST parameters in the request
if(is_array($action) && count($action)) {

	// LIST/EDIT ITEM
	if(preg_match("/^(list|edit)$/", $action[0])) {

		$page->page(array(
			"type" => "janitor",
			"templates" => "janitor/user_group/".$action[0].".php"
		));
		exit();
	}

	// Class interface
	else if($page->validateCsrfToken() && preg_match("/[a-zA-Z]+/", $action[0])) {

		// check if custom function exists on model class
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
