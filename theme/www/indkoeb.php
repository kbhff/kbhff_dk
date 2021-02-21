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

// define which model this controller is referring to
$IC = new Items();

// define which model this controller is referring to
include_once("classes/shop/pickupdate.class.php");
$PC = new Pickupdate();

include_once("classes/system/department.class.php");
$DC = new Department();


if($action) {

	if($action[0] == "nyt-produkt") {

		// standard template
		$page->page(array(
			"templates" => "purchasing/add_product.php",
			"type" => "admin"
		));
		exit();

	}
	else if($action[0] == "rediger-produkt") {

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

		$itemtype = getPost("product_type", "value");

		$model = $IC->typeObject($itemtype);
		$item = $model->save($action);

		// successful creation
		if($item) {

			$vatrates = $page->vatrates();
			$vatrate = $vatrates[arrayKeyValue($vatrates, "name", "25%")];
			
			$price_types = $page->price_types();

			// add price for Frivillig
			$frivillig_price_type = $price_types[arrayKeyValue($price_types, "name", "frivillig")];
			$_POST["item_price"] = getPost("price_1", "value");
			$_POST["item_price_currency"] = "DKK";
			$_POST["item_price_vatrate"] = $vatrate ? $vatrate["id"] : false;
			$_POST["item_price_type"] = $frivillig_price_type ? $frivillig_price_type["id"] : false;
			$frivillig_price = $model->addPrice(["addPrice", $item["id"]]);
			
			// add price for Støttemedlem
			$stoettemedlem_price_type = $price_types[arrayKeyValue($price_types, "name", "stoettemedlem")];
			$_POST["item_price"] = getPost("price_2", "value");
			$_POST["item_price_currency"] = "DKK";
			$_POST["item_price_vatrate"] = $vatrate ? $vatrate["id"] : false;
			$_POST["item_price_type"] = $stoettemedlem_price_type ? $stoettemedlem_price_type["id"] : false;
			$stoettemedlem_price = $model->addPrice(["addPrice", $item["id"]]);
			unset($_POST);

			if($frivillig_price && $stoettemedlem_price) {

				// enable item
				$model->status(["status", $item["id"], 1]);

				message()->addMessage("Produktet blev oprettet.");
				header("Location: /indkoeb");
			}
			else {

				message()->addMessage("Produktet blev oprettet, men der opstod et problem med at oprette priser", array("type" => "error"));
			}
		}
		// something went wrong
		else {
			message()->addMessage("Noget gik galt. Prøv igen.", array("type" => "error"));
		}

		exit();

	}

	else if(count($action) == 2 && $action[0] == "updateProduct" && $page->validateCsrfToken()) {
		
		$item_id = $action[1];
		$item = $IC->getItem(array("id" => $item_id, "extend" => ["mediae" => true, "prices" => true]));
		
		$model = $IC->typeObject($item["itemtype"]);
		$item = $model->update($action);
		
		// successful update
		if($item) {

			$old_price_1_key = $item["prices"] ? arrayKeyValue($item["prices"], "type", "frivillig") : false;
			$old_price_2_key = $item["prices"] ? arrayKeyValue($item["prices"], "type", "stoettemedlem") : false;

			$old_price_1 = $old_price_1_key !== false ? $item["prices"][$old_price_1_key] : false;
			$old_price_2 = $old_price_2_key !== false ? $item["prices"][$old_price_2_key] : false;

			$new_price_1 = getPost("price_1", "value");
			$new_price_2 = getPost("price_2", "value");

			$vatrates = $page->vatrates();
			$vatrate_key = arrayKeyValue($vatrates, "name", "25%");
			$vatrate_id = $vatrate_key ? $vatrates[$vatrate_key]["id"] : false;
			$price_types = $page->price_types();


			if($old_price_1 && $new_price_1) {

				if($old_price_1["price"] !== $new_price_1) {

					// delete price_1
					$model->deletePrice(["deletePrice", $item["id"], $old_price_1["id"]]);

					// add new price_1
					$_POST["item_price"] = $new_price_1;
					$_POST["item_price_currency"] = "DKK";
					$_POST["item_price_vatrate"] = $vatrate_id;
					$_POST["item_price_type"] = $old_price_1["type_id"];
					$frivillig_price = $model->addPrice(["addPrice", $item["id"]]);
					unset($_POST);
				}
				else {
					$frivillig_price = $old_price_1["price"];
				}
			}
			else if($old_price_1) {
				// delete price_1
				$model->deletePrice(["deletePrice", $item["id"], $old_price_1["id"]]);
				$frivillig_price = false;
			}
			else if($new_price_1) {

				$vatrates = $page->vatrates();
				$vatrate = $vatrates[arrayKeyValue($vatrates, "name", "25%")];
				
				$price_types = $page->price_types();

				// add price for Frivillig
				$frivillig_price_type = $price_types[arrayKeyValue($price_types, "name", "frivillig")];
				$_POST["item_price"] = getPost("price_1", "value");
				$_POST["item_price_currency"] = "DKK";
				$_POST["item_price_vatrate"] = $vatrate_id;
				$_POST["item_price_type"] = $frivillig_price_type ? $frivillig_price_type["id"] : false;
				$frivillig_price = $model->addPrice(["addPrice", $item["id"]]);
				}

			else {
				$frivillig_price = false;
			}

			

			if($old_price_2 && $new_price_2) {

				if($old_price_2["price"] !== $new_price_2) {

					// delete price_2
					$model->deletePrice(["deletePrice", $item["id"], $old_price_2["id"]]);

					// add new price_2
					$_POST["item_price"] = $new_price_2;
					$_POST["item_price_currency"] = "DKK";
					$_POST["item_price_vatrate"] = $vatrate_id;
					$_POST["item_price_type"] = $old_price_2["type_id"];
					$stoettemedlem_price = $model->addPrice(["addPrice", $item["id"]]);
					unset($_POST);
				}
				else {
					$stoettemedlem_price = $old_price_2["price"];
				}
			}
			else if($old_price_2) {
				// delete price_2
				$model->deletePrice(["deletePrice", $item["id"], $old_price_2["id"]]);
				$stoettemedlem_price = false;
			}
			else if($new_price_2) {

				// add price for Støttemedlem
				$stoettemedlem_price_type = $price_types[arrayKeyValue($price_types, "name", "stoettemedlem")];
				$_POST["item_price"] = getPost("price_2", "value");
				$_POST["item_price_currency"] = "DKK";
				$_POST["item_price_vatrate"] = $vatrate_id;
				$_POST["item_price_type"] = $stoettemedlem_price_type ? $stoettemedlem_price_type["id"] : false;
				$stoettemedlem_price = $model->addPrice(["addPrice", $item["id"]]);
				}

			else {
				$stoettemedlem_price = false;
			}

			if($frivillig_price && $stoettemedlem_price) {

				message()->addMessage("Produktet blev opdateret.");
				header("Location: /indkoeb");
			}
			else {
				
				message()->addMessage("Produktet blev opdateret, men der opstod et problem med at oprette priser", array("type" => "error"));
				header("Location: /indkoeb");
			}
		}
		// something went wrong
		else {
			message()->addMessage("Noget gik galt. Prøv igen.", array("type" => "error"));
		}

		exit();

	}

	else if(count($action) == 1 && $action[0] == "savePickupdate" && $page->validateCsrfToken()) {

		if($PC->savePickupdate($action)) {

			message()->addMessage("Afhentningsdagen blev oprettet.");
			header("Location: /indkoeb");
		}
		else {
			
			message()->addMessage("Noget gik galt.", array("type" => "error"));
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
 