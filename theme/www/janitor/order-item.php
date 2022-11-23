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
include_once("classes/shop/supershop.class.php");
$SC = new SuperShop();

include_once("classes/system/department.class.php");
$DC = new Department();

include_once("classes/shop/pickupdate.class.php");
$PC = new Pickupdate();

// page info
$page->bodyClass("order_item");
$page->pageTitle("Order items");


if(is_array($action) && count($action)) {

	// LIST/EDIT/NEW
	if(preg_match("/^(list|edit)$/", $action[0])) {

		$page->page(array(
			"type" => "janitor",
			"templates" => "janitor/order_item/".$action[0].".php"
		));
		exit();
	}

	// Class interface
	else if(security()->validateCsrfToken() && preg_match("/[a-zA-Z]+/", $action[0])) {

		// check if custom function exists on Shop class
		if($SC && method_exists($SC, $action[0])) {

			// output custom function to screen as JSON
			$output = new Output();
			$output->screen($SC->{$action[0]}($action));
			exit();
		}
	}

}

// bad command
$page->page(array(
	"templates" => "pages/404.php"
));

?>
