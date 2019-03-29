<?php
$access_item["/"] = true;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");

include_once("classes/items/type.product.class.php");

$model = new Model();
$IPC = new TypeProduct();

// get REST parameters
$action = $page->actions();

// page info
$page->bodyClass("purchasing");
$page->pageTitle("Indkøb");

if(is_array($action) && count($action)) {

	// LIST/EDIT/NEW ITEM
	if(preg_match("/^(new)$/", $action[0])) {

		$page->page(array(
			"type" => "terms",
			"templates" => "/purchasing/new.php"
		));
		exit();
	} elseif(preg_match("/^(edit)$/", $action[0])) {

		$page->page(array(
			"type" => "terms",
			"templates" => "/purchasing/edit.php"
		));
		exit();
	} elseif($action[0] == "status") {
		if(!$IPC->status($action)) {
			message()->addMessage("Status could not be modified.", array("type"=>"error"));
		} else {
			message()->addMessage("Status has been modified.",array("type" => "message"));
		}
		header("Location: /indkoeb");
		exit();
	} elseif($action[0] == "save" && $page->validateCsrfToken()) {
		// create new item 
		if (count($action) == 1) {
			$item = $IPC->saveItemFromIndkoeb(array("newItemFromIndkoeb"));
		} else {
			$item = $IPC->saveItemFromIndkoeb(array("newItemFromIndkoeb", $action[1]));
		}

		if ($item) {
			header("Location: /indkoeb");
			
		} else {
			if (count($action) == 1) {
				# header("Location: /indkoeb/edit?");
				$page->page(array(
					"type" => "terms",
					"templates" => "/purchasing/edit.php"
				));

			} else {
				$page->page(array(
					"type" => "terms",
					"templates" => "/purchasing/edit.php"
				));

			}
		}
		exit();
		
	}
	

}
// standard template
$page->page(array(
	"templates" => "purchasing/index.php",
	"type" => "admin"
));
exit();


?>
 