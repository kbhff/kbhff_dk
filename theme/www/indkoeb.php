<?php
$access_item["/"] = true;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");

include_once("classes/items/type.product.class.php");
include_once("classes/system/department.class.php");

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
			"type" => "member",
			"templates" => "/purchasing/new.php"
		));
		exit();
	} elseif(preg_match("/^(edit)$/", $action[0])) {

		$page->page(array(
			"type" => "member",
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
					"type" => "member",
					"templates" => "/purchasing/new.php"
				));

			} else {
				$page->page(array(
					"type" => "member",
					"templates" => "/purchasing/edit.php"
				));

			}
			
		}
		exit();
	} else if($page->validateCsrfToken() && preg_match("/[a-zA-Z]+/", $action[0])) {
		// used for addSingleMedia
		// check if custom function exists on User class
		if($IPC && method_exists($IPC, $action[0])) {

			$output = new Output();
			$output->screen($IPC->{$action[0]}($action));
			exit();
		}
	}

	

}
// standard template
$page->page(array(
	"templates" => "purchasing/index.php",
	"type" => "member"
));
exit();


?>
 