<?php


$access_item["/"] = true;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


// get REST parameters
$action = $page->actions();

include_once("classes/shop/tally.class.php");
$TC = new Tally();
include_once("classes/system/department.class.php");
$DC = new Department();


// page info
$page->bodyClass("accountant");
$page->pageTitle("Bogholder");

if($action) {

	if($action[0] == "afregninger") {

		// /bogholder/afregninger/#department_id#
		if(count($action) == 2) {

			$page->page([
				"templates" => "accountant/tallies.php"
			]);
			exit();
		}

		// /bogholder/afregninger/#department_id#/#tally_id#
		elseif(count($action) == 3) {

			$page->page([
				"templates" => "accountant/tally.php"
			]);
			exit();
		}

	}
}

// standard template
$page->page(array(
	"templates" => "accountant/index.php",
	// "type" => "accountant"
));
exit();


?>
 