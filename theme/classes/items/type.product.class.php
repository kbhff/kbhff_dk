<?php

/**
* Foodnet platform
* Copyright (C) 2018  Københavns Fødevarefællesskab and think.dk
*
* Københavns Fødevarefællesskab
* KPH-Projects
* Enghavevej 80 C, 3. sal
* 2450 København SV
* Denmark
* mail: bestyrelse@kbhff.dk
*
* think.dk
* Æbeløgade 4
* 2100 København Ø
* Denmark
* mail: start@think.dk
*
* This source code is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This source code is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this source code.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
* @package janitor.items
* This file contains item type functionality
*/

class TypeProduct extends Itemtype {

	/**
	* Init, set varnames, validation rules
	*/
	function __construct($type_class = false) {

		// Construct Itemtype class and pass sub-itemtype as parameter, if passed, or else itemtype
		parent::__construct($type_class ?: get_class());


		// Name
		$this->addToModel("name", array(
			"type" => "string",
			"label" => "Name",
			"required" => true,
			"hint_message" => "Give the product a name.",
			"error_message" => "The product needs a title."
		));

		// Start availability date
		$this->addToModel("start_availability_date", array(
			"type" => "date",
			"label" => "Start availability date",
			"required" => true,
			"hint_message" => "When does the product become available?",
			"error_message" => "The product needs a start availability date."
		));

		// End availability date
		$this->addToModel("end_availability_date", array(
			"type" => "date",
			"label" => "End availability date",
			"hint_message" => "When does the product stop being available?",
			"error_message" => "Invalid end availability date."
		));
		
		// Price 1
		$this->addToModel("price_1", [
			"type" => "number",
			"label" => "Price 1 (Frivillig member)",
			"hint_message" => "Enter price as a number.",
			"error_message" => "Invalid price."
		]);

		// Price 2
		$this->addToModel("price_2", [
			"type" => "number",
			"label" => "Price 2 (Støttemedlem)",
			"hint_message" => "Enter price as a number.",
			"error_message" => "Invalid price."
		]);
		
		// Product type
		$this->addToModel("product_type", array(
			"type" => "select",
			"label" => "Product type",
			"hint_message" => "Select the product type.",
			"error_message" => "Please select a valid product type.",
			"required" => true
		));
		
		// description
		$this->addToModel("description", array(
			"type" => "text",
			"label" => "Description",
			"required" => true,
			"hint_message" => "Decribe the product.",
			"error_message" => "The product needs a description."
		));

		// Single media
		$this->addToModel("single_media", array(
			"type" => "files",
			"label" => "Add media here",
			"min_width" => 960,
			"min_height" => 960,
			"allowed_proportion" => 1,
			"max" => 1,
			"allowed_formats" => "png,jpg",
			"hint_message" => "Add single image by dragging it here. Minimum 960x960 PNG or JPG allowed.",
			"error_message" => "Media does not fit requirements."
		));

	}

	function addNewProduct($action) {

		$IC = new Items();
		global $page;

		$this->getPostedEntities();

		// Validating generic product entities - the remaining entities will be validated on save()
		if(count($action) == 1 && $this->validateList(["product_type", "price_1", "price_2"])) {

			$product_type = $this->getProperty("product_type", "value");
			$price_1 = $this->getProperty("price_1", "value");
			$price_2 = $this->getProperty("price_2", "value");
			
			// remove generic entities from $_POST to prepare for saving specific product type
			unset($_POST["product_type"]);
			unset($_POST["price_1"]);
			unset($_POST["price_2"]);
			
			$model = $IC->typeObject($product_type);
			// overwrite itemtype (by default, the system uses the itemtype of the supermodel (product) rather than the model (e.g. productweeklybag))

			$item = $model->save($action);

			if($item) {
	
				$vatrates = $page->vatrates();
				$vatrate_key = arrayKeyValue($vatrates, "name", "25%");
				$vatrate_id = $vatrate_key ? $vatrates[$vatrate_key]["id"] : false;
				$price_types = $page->price_types();
				
				// add price for Frivillig
				$frivillig_price_type = $price_types[arrayKeyValue($price_types, "name", "frivillig")];
				$_POST["item_price"] = $price_1;
				$_POST["item_price_currency"] = "DKK";
				$_POST["item_price_vatrate"] = $vatrate_id;
				$_POST["item_price_type"] = $frivillig_price_type ? $frivillig_price_type["id"] : false;
				$frivillig_price = $model->addPrice(["addPrice", $item["id"]]);
				unset($_POST);
				
				// add price for Støttemedlem
				$stoettemedlem_price_type = $price_types[arrayKeyValue($price_types, "name", "stoettemedlem")];
				$_POST["item_price"] = $price_2;
				$_POST["item_price_currency"] = "DKK";
				$_POST["item_price_vatrate"] = $vatrate_id;
				$_POST["item_price_type"] = $stoettemedlem_price_type ? $stoettemedlem_price_type["id"] : false;
				$stoettemedlem_price = $model->addPrice(["addPrice", $item["id"]]);
				unset($_POST);
				
				
				if($frivillig_price && $stoettemedlem_price) {
					
					// enable item
					$model->status(["status", $item["id"], 1]);
					
					message()->resetMessages();
					message()->addMessage("Produktet blev oprettet.");
					return $item;
				}
				else {
					
					message()->resetMessages();
					message()->addMessage("Produktet blev oprettet, men der opstod et problem med at oprette priser", array("type" => "error"));
				}
			}
			// something went wrong
			else {

				message()->resetMessages();
				message()->addMessage("Noget gik galt. Prøv igen.", array("type" => "error"));
			}
		}

		return false;

	}

	function updateProduct($action) {

		$IC = new Items();
		global $page;
		global $model;

		$this->getPostedEntities();

		// Validating generic product entities - the remaining entities will be validated on save()
		if(count($action) == 2 && $this->validateList(["price_1", "price_2"])) {

			$new_price_1 = $this->getProperty("price_1", "value");
			$new_price_2 = $this->getProperty("price_2", "value");
			
			// remove generic entities from $_POST to prepare for saving specific product type
			unset($_POST["price_1"]);
			unset($_POST["price_2"]);
			
			$item = $model->update($action);

			if($item) {
	
				$old_price_1_key = $item["prices"] ? arrayKeyValue($item["prices"], "type", "frivillig") : false;
				$old_price_2_key = $item["prices"] ? arrayKeyValue($item["prices"], "type", "stoettemedlem") : false;

				$old_price_1 = $old_price_1_key !== false ? $item["prices"][$old_price_1_key] : false;
				$old_price_2 = $old_price_2_key !== false ? $item["prices"][$old_price_2_key] : false;

				$vatrates = $page->vatrates();
				$vatrate_key = arrayKeyValue($vatrates, "name", "25%");
				$vatrate_id = $vatrate_key ? $vatrates[$vatrate_key]["id"] : false;
				$price_types = $page->price_types();
		
				if($old_price_1 !== false && $new_price_1 !== false) {

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
				else if($old_price_1 !== false) {
					// delete price_1
					$model->deletePrice(["deletePrice", $item["id"], $old_price_1["id"]]);
					$frivillig_price = false;
				}
				else if($new_price_1 !== false) {
	
					// add price for Frivillig
					$frivillig_price_type = $price_types[arrayKeyValue($price_types, "name", "frivillig")];
					$_POST["item_price"] = $new_price_1;
					$_POST["item_price_currency"] = "DKK";
					$_POST["item_price_vatrate"] = $vatrate_id;
					$_POST["item_price_type"] = $frivillig_price_type ? $frivillig_price_type["id"] : false;
					$frivillig_price = $model->addPrice(["addPrice", $item["id"]]);
					}
	
				else {
					$frivillig_price = false;
				}
	
				
	
				if($old_price_2 !== false && $new_price_2 !== false) {
	
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
				else if($old_price_2 !== false) {
					// delete price_2
					$model->deletePrice(["deletePrice", $item["id"], $old_price_2["id"]]);
					$stoettemedlem_price = false;
				}
				else if($new_price_2 !== false) {
	
					// add price for Støttemedlem
					$stoettemedlem_price_type = $price_types[arrayKeyValue($price_types, "name", "stoettemedlem")];
					$_POST["item_price"] = $new_price_2;
					$_POST["item_price_currency"] = "DKK";
					$_POST["item_price_vatrate"] = $vatrate_id;
					$_POST["item_price_type"] = $stoettemedlem_price_type ? $stoettemedlem_price_type["id"] : false;
					$stoettemedlem_price = $model->addPrice(["addPrice", $item["id"]]);
					}
	
				else {
					$stoettemedlem_price = false;
				}
	
				
				if($frivillig_price && $stoettemedlem_price) {
					
					// enable item
					$model->status(["status", $item["id"], 1]);
					
					message()->resetMessages();
					message()->addMessage("Produktet blev opdateret.");
					return $item;
				}
				else {
					
					message()->resetMessages();
					message()->addMessage("Produktet blev opdateret, men der opstod et problem med at opdatere priser", array("type" => "error"));
				}
			}
			// something went wrong
			else {

				message()->resetMessages();
				message()->addMessage("Noget gik galt. Prøv igen.", array("type" => "error"));
			}
		}

		return false;

	}

	function saved($item_id) {

		include_once("classes/system/department.class.php");
		$DC = new Department();

		$departments = $DC->getDepartments();

		// add the new product to all departments
		foreach($departments as $department) {

			$DC->addProduct($department["id"], $item_id);
		}
	}

}

?>
