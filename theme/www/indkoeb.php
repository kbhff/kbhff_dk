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

	else if(count($action) == 1 && $action[0] == "addNewProduct" && security()->validateCsrfToken()) {

		$item = $supermodel->addNewProduct($action);

		// successful creation
		if($item) {
			header("Location: /indkoeb/rediger-produkt/".$item["id"]);
		}
		else {
			// standard template
			$page->page(array(
				"templates" => "purchasing/add_product.php",
				"type" => "admin"
			));
			
			// header("Location: /indkoeb/nyt-produkt");
		}

		exit();

	}

	else if(count($action) == 2 && $action[0] == "updateProductBasics" && security()->validateCsrfToken()) {

		$item_id = $action[1];
		$supermodel->updateProductBasics($action);
		header("Location: /indkoeb/rediger-produkt/$item_id");
		exit();

	}

	else if(count($action) == 2 && $action[0] == "updateProductAvailability" && security()->validateCsrfToken()) {
		
		$item_id = $action[1];
		$supermodel->updateProductAvailability($action);

		header("Location: /indkoeb/rediger-produkt/$item_id");
		exit();

	}

	else if(count($action) == 2 && $action[0] == "updateProductPrices" && security()->validateCsrfToken()) {

		$item_id = $action[1];
		$supermodel->updateProductPrices($action);
		header("Location: /indkoeb/rediger-produkt/$item_id");
		exit();

	}

	else if(count($action) == 2 && $action[0] == "updateProductTags" && security()->validateCsrfToken()) {

		$item_id = $action[1];
		$supermodel->updateProductTags($action);
		header("Location: /indkoeb/rediger-produkt/$item_id");
		exit();

	}

	else if(count($action) == 2 && $action[0] == "disableProduct" && security()->validateCsrfToken()) {
		
		$item_id = $action[1];
		$item = $IC->getItem(array("id" => $item_id));
		$model = $IC->typeObject($item["itemtype"]);

		$result = $model->status(["status", $item_id, "0"]);

		// successful update
		message()->resetMessages();
		if($result) {
			message()->addMessage("Produktet blev arkiveret");
		}
		else {
			message()->addMessage("Produktet kunne ikke arkiveres");
		}

		header("Location: /indkoeb/rediger-produkt/$item_id");
		exit();

	}

	else if(count($action) == 2 && $action[0] == "enableProduct" && security()->validateCsrfToken()) {
		
		$item_id = $action[1];
		$item = $IC->getItem(array("id" => $item_id));
		$model = $IC->typeObject($item["itemtype"]);

		$result = $model->status(["status", $item_id, "1"]);

		// successful update
		message()->resetMessages();
		if($result) {
			message()->addMessage("Produktet er genaktiveret");
		}
		else {
			message()->addMessage("Produktet kunne ikke genaktiveres");
		}

		header("Location: /indkoeb/rediger-produkt/$item_id");
		exit();

	}

	else if(count($action) == 1 && $action[0] == "savePickupdate" && security()->validateCsrfToken()) {

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

	else if($action[0] == "selectPickupdate" && count($action) == 1) {
		
		$pickupdate_id = getPost("pickupdate_id", "value");

		$pickupdate = $PC->getPickupdate(["id" => $pickupdate_id]);

		if($pickupdate && $pickupdate["pickupdate"] == date("Y-m-d") ) {

			// redirect to leave POST state
			header("Location: /indkoeb");
			exit();			
		}

		// redirect to leave POST state
		header("Location: /indkoeb/".$pickupdate_id);
		exit();			

	}


	else if(count($action) == 2 && $action[0] == "updatePickupdateDepartments" && security()->validateCsrfToken()) {

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
 