<?php


$access_item["/"] = true;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");

// get REST parameters
$action = $page->actions();

// page info
$page->bodyClass("purchasing");
$page->pageTitle("Indkøb");

$IC = new Items();

include_once("classes/shop/pickupdate.class.php");
$PC = new Pickupdate();

include_once("classes/system/department.class.php");
$DC = new Department();

$supermodel = $IC->typeObject("product");


if($action) {

	if($action[0] == "nyt-produkt") {

		// standard template
		$page->page(array(
			"templates" => "purchasing/add_product.php",
			"type" => "admin"
		));
		exit();

	}
	else if($action[0] == "rediger-produkt" && count($action) == 2) {

		// standard template
		$page->page(array(
			"templates" => "purchasing/edit_product.php",
			"type" => "admin"
		));
		exit();

	}
	else if($action[0] == "ny-afhentningsdag") {

		// standard template
		$page->page(array(
			"templates" => "purchasing/add_pickupdate.php",
			"type" => "admin"
		));
		exit();

	}
	else if($action[0] == "rediger-afhentningsdag") {

		// standard template
		$page->page(array(
			"templates" => "purchasing/edit_pickupdate.php",
			"type" => "admin"
		));
		exit();

	}

	else if(count($action) == 1 && $action[0] == "addNewProduct" && $page->validateCsrfToken()) {

		$item = $supermodel->addNewProduct($action);

		// successful creation
		if($item) {
			header("Location: /indkoeb");
			
		}
		else {
			header("Location: /indkoeb/nyt-produkt");
		}

		exit();

	}

	else if(count($action) == 2 && $action[0] == "updateProduct" && $page->validateCsrfToken()) {
		
		$item_id = $action[1];
		$item = $IC->getItem(array("id" => $item_id, "extend" => ["mediae" => true, "prices" => true]));
		
		$model = $IC->typeObject($item["itemtype"]);

		$item = $supermodel->updateProduct($action);

		// successful update
		if($item) {
			header("Location: /indkoeb");
			
		}
		else {
			header("Location: /indkoeb/rediger-produkt");
		}

		exit();

	}

	else if(count($action) == 1 && $action[0] == "savePickupdate" && $page->validateCsrfToken()) {

		if($PC->savePickupdate($action)) {

			message()->resetMessages();
			message()->addMessage("Afhentningsdagen blev oprettet.");
			header("Location: /indkoeb");
		}
		else {
			
			message()->resetMessages();
			message()->addMessage("Noget gik galt.", array("type" => "error"));
			header("Location: /indkoeb/ny-afhentningsdag");
		}
		
		exit();

	}

	else if(count($action) == 2 && $action[0] == "updatePickupdateDepartments" && $page->validateCsrfToken()) {

		if($DC->updatePickupdateDepartments($action)) {

			message()->addMessage("Afdelingernes åbningstid på den givne dato blev justeret.");
			header("Location: /indkoeb");
		}
		else {
			
			message()->addMessage("Noget gik galt.", array("type" => "error"));
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
 