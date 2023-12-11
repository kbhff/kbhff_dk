<?php

$access_item["/"] = true;
$access_item["/sendToOwnDepartmentMembers"] = "/";
$access_item["/sendToDepartmentMembers"] = true;
$access_item["/sendToAllMembers"] = true;

if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");

// get REST parameters
$action = $page->actions();
$IC = new Items();
$model = $IC->typeObject("message");

include_once("classes/users/superuser.class.php");
$UC = new SuperUser();
include_once("classes/system/department.class.php");
$DC = new Department();

// page info
$page->bodyClass("mass_mail");
$page->pageTitle("Massemail");

if($action) {

	if(count($action) == 1 && $action[0] == "sendKbhffMessage" && security()->validateCsrfToken()) {

		if($model->sendKbhffMessage($action)) {

			message()->resetMessages();
			message()->addMessage("Beskeden blev afsendt.");
			header("Location: /massemail/kvittering");
			exit();

		}
		else {

			message()->resetMessages();
			message()->addMessage("Noget gik galt.", array("type" => "error"));

		}

	}
	else if(count($action) == 1 && $action[0] == "sendKbhffMessageTest" && security()->validateCsrfToken()) {

		if($model->sendKbhffMessageTest($action)) {

			message()->resetMessages();
			header("Location: /massemail/kvittering");
			exit();

		}
		else {

			message()->resetMessages();

		}

	}
	else if(count($action) == 1 && $action[0] == "kvittering") {

		$page->page(array(
			"templates" => "mass-mail/receipt/index.php",
			"type" => "admin"
		));
		exit();

	}
}


// standard template
$page->page(array(
	"templates" => "mass-mail/index.php",
	"type" => "admin"
));
exit();


?>
 