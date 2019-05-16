<?php
$access_item["/"] = true;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");

include_once("classes/items/type.product.class.php");
include_once("classes/system/department.class.php");
include_once("classes/system/afhentningsdage.class.php");

$model = new Model();
$IPC = new TypeProduct();
$AC = new Afhentningsdage();

// get REST parameters
$action = $page->actions();

// page info
$page->bodyClass("afhentningsdage");
$page->pageTitle("Afhentningsdage og lokale åbningsdage");

if(is_array($action) && count($action)) {

	// LIST/EDIT/NEW ITEM
	if(preg_match("/^(new_dep_pickup_date)$/", $action[0])) {

		$page->page(array(
			"type" => "member",
			"templates" => "/afhentningsdage/new.php"
		));
		exit();
	} elseif(preg_match("/^(remove)$/", $action[0])) {
		$AC->removeDepartmentSchedule($action);
		$page->page(array(
			"templates" => "afhentningsdage/list.php",
			"type" => "member"
		));
		exit();
	} elseif(preg_match("/^(edit)$/", $action[0])) {

		$page->page(array(
			"type" => "member",
			"templates" => "/afhentningsdage/new.php"
		));
		exit();
	} elseif($action[0] == "save" && $page->validateCsrfToken()) {
		// create / modify new schedule
		$item = $AC->saveDepartmentSchedule($action);

		if ($item) {
			header("Location: /afhentningsdage");
			
		} else {
			$page->page(array(
				"type" => "member",
				"templates" => "/afhentningsdage/new.php"
			));

			
		}
		exit();
	}

	

}
// standard template
$page->page(array(
	"templates" => "afhentningsdage/list.php",
	"type" => "member"
));
exit();


?>
