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
* Mindehøjvej 4
* 4673 Rødvig Stevns
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


	public $db_department_pickupdate_order_items;


	/**
	* Init, set varnames, validation rules
	*/
	function __construct($type_class = false) {

		// Construct Itemtype class and pass sub-itemtype as parameter, if passed, or else itemtype
		parent::__construct($type_class ?: get_class());

		$this->db_department_pickupdate_order_items = SITE_DB.".project_department_pickupdate_order_items";

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
			"hint_message" => "When does the product become available from the producer? The first available pickupdate will be the first Wednesday that are at least 1 week in the future, relative to this date.",
			"error_message" => "The product needs a start availability date."
		));

		// End availability date
		$this->addToModel("end_availability_date", array(
			"type" => "date",
			"label" => "End availability date",
			"hint_message" => "When does the product stop being available from the producer?",
			"error_message" => "Invalid end availability date."
		));
		
		// Price
		$this->addToModel("price", [
			"type" => "array",
			"label" => "Prices",
			"hint_message" => "Enter price as a number.",
			"error_message" => "Invalid price."
		]);

		// Tag
		$this->addToModel("tag", [
			"type" => "array",
			"label" => "Tags",
			"hint_message" => "Select tag.",
			"error_message" => "Invalid tag."
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
		// global $page;

		$this->getPostedEntities();

		// Validating generic product entities - the remaining entities will be validated on save()
		// if(count($action) == 1 && $this->validateList(["product_type", "price_1", "price_2"])) {
		if(count($action) == 1 && $this->validateList(["product_type", "name"])) {

			$product_type = $this->getProperty("product_type", "value");
			unset($_POST["product_type"]);
			// $price_1 = $this->getProperty("price_1", "value");
			// $price_2 = $this->getProperty("price_2", "value");
			
			// remove generic entities from $_POST to prepare for saving specific product type
			// unset($_POST["price_1"]);
			// unset($_POST["price_2"]);
			
			$model = $IC->typeObject($product_type);

			$item = $model->save($action);

			if($item) {
	
				// $vatrates = $page->vatrates();
				// $vatrate_key = arrayKeyValue($vatrates, "name", "25%");
				// $vatrate_id = $vatrate_key ? $vatrates[$vatrate_key]["id"] : false;
				// $price_types = $page->priceTypes();
				//
				// // add price for Frivillig
				// $frivillig_price_type = $price_types[arrayKeyValue($price_types, "name", "frivillig")];
				// $_POST["item_price"] = $price_1;
				// $_POST["item_price_currency"] = "DKK";
				// $_POST["item_price_vatrate"] = $vatrate_id;
				// $_POST["item_price_type"] = $frivillig_price_type ? $frivillig_price_type["id"] : false;
				// $frivillig_price = $model->addPrice(["addPrice", $item["id"]]);
				// unset($_POST);
				//
				// // add price for Støttemedlem
				// $stoettemedlem_price_type = $price_types[arrayKeyValue($price_types, "name", "stoettemedlem")];
				// $_POST["item_price"] = $price_2;
				// $_POST["item_price_currency"] = "DKK";
				// $_POST["item_price_vatrate"] = $vatrate_id;
				// $_POST["item_price_type"] = $stoettemedlem_price_type ? $stoettemedlem_price_type["id"] : false;
				// $stoettemedlem_price = $model->addPrice(["addPrice", $item["id"]]);
				// unset($_POST);
				//
				//
				// if($frivillig_price && $stoettemedlem_price) {
					
					// enable item
				$model->status(["status", $item["id"], 1]);
				
				message()->resetMessages();
				message()->addMessage("Produktet blev oprettet.");
				return $item;
				// }
				// else {
				//
				// 	message()->resetMessages();
				// 	message()->addMessage("Produktet blev oprettet, men der opstod et problem med at oprette priser", array("type" => "error"));
				// }
			}
			// something went wrong
			// else {
			//
			// }
		}

		// message()->resetMessages();
		message()->addMessage("Noget gik galt. Prøv igen.", array("type" => "error"));
		return false;

	}

	function updateProductBasics($action) {

		$this->getPostedEntities();

		// Validating generic product entities - the remaining entities will be validated on save()
		if(count($action) == 2 && $this->validateList(["name", "description"])) {

			$IC = new Items();
			$item_id = $action[1];
			$item = $IC->getItem(array("id" => $item_id));
			$model = $IC->typeObject($item["itemtype"]);

			$item = $model->update($action);

			if($item) {
	
				message()->resetMessages();
				message()->addMessage("Produktnavn og beskrivelse er opdateret.");
				return $item;

			}

		}

		return false;

	}

	function updateProductAvailability($action) {


		$this->getPostedEntities();

		// Validating generic product entities - the remaining entities will be validated on save()
		if(count($action) == 2 && $this->validateList(["start_availability_date", "end_availability_date"])) {

			$IC = new Items();
			$item_id = $action[1];
			$item = $IC->getItem(array("id" => $item_id));
			$model = $IC->typeObject($item["itemtype"]);

			$item = $model->update($action);

			if($item) {
	
				message()->resetMessages();
				message()->addMessage("Produkt tilgængelighed er opdateret.");
				return $item;

			}

		}

		return false;

	}

	function updateProductPrices($action) {

		$IC = new Items();
		global $page;
		global $model;

		$this->getPostedEntities();

		// Validating generic product entities - the remaining entities will be validated on save()
		if(count($action) == 2 && $this->validateList(["price"])) {

			$item_id = $action[1];
			$prices = $this->getProperty("price", "value");

			$item = $IC->getItem(["id" => $item_id, "extend" => ["prices" => true]]);
			// debug(["item", $item]);

			if($item) {

				$vatrates = $page->vatrates();
				$vatrate_key = arrayKeyValue($vatrates, "name", "25%");
				$vatrate_id = $vatrate_key ? $vatrates[$vatrate_key]["id"] : false;


				foreach($prices as $price_type_id => $new_price) {

					$current_price_id = $item["prices"] ? arrayKeyValue($item["prices"], "type_id", $price_type_id) : false;
					$current_price = $current_price_id !== false ? $item["prices"][$current_price_id]["price"] : false;
					// debug(["current_price", $current_price, "new price", $new_price]);

					// Price has changed
					if($current_price != $new_price) {

						// Delete old price if it exists
						if($current_price_id !== false) {
							// debug(["delete price", $current_price_id]);
							$this->deletePrice(["deletePrice", $item_id, $item["prices"][$current_price_id]["id"]]);
						}

						// add new price
						unset($_POST);
						$_POST["item_price"] = $new_price;
						$_POST["item_price_currency"] = "DKK";
						$_POST["item_price_vatrate"] = $vatrate_id;
						$_POST["item_price_type"] = $price_type_id;
						$this->addPrice(["addPrice", $item_id]);
						unset($_POST);

					}

				}

				message()->resetMessages();
				message()->addMessage("Priserne er opdateret.");
				return $item;

			}
			else {

				message()->resetMessages();
				message()->addMessage("Noget gik galt. Prøv igen.", array("type" => "error"));
				return false;

			}
		}

		message()->resetMessages();
		message()->addMessage("Der skal angives priser for alle medlemstyper for at gennemføre opdateringen.", array("type" => "error"));
		return false;

	}

	function getMembershipPriceTypes($product = false) {
		global $page;

		$IC = new Items();

		// Get price types for memberships
		$all_price_types = $page->priceTypes();

		$membership_price_types = [];

		foreach($all_price_types as $price_type) {
			if($price_type["item_id"]) {
				$related_item = $IC->getItem(["id" => $price_type["item_id"]]);
				if($related_item["itemtype"] === "membership") {

					if($product) {
						$product_price_key = $product["prices"] !== false ? arrayKeyValue($product["prices"], "type", $price_type["name"]) : false;
						$price_type["price"] = $product["prices"] !== false ? $product["prices"][$product_price_key] : false;
						$membership_price_types[] = $price_type;
					}

				}
			}
		}

		return $membership_price_types;

	}

	function updateProductTags($action) {

		$IC = new Items();
		global $page;
		global $model;

		$this->getPostedEntities();

		// Validating generic product entities - the remaining entities will be validated on save()
		if(count($action) == 2 && $this->validateList(["tag"])) {

			$item_id = $action[1];
			$tags = $this->getProperty("tag", "value");

			$item = $IC->getItem(["id" => $item_id, "extend" => ["tags" => true]]);
			// debug(["item", $item]);

			if($item) {

				foreach($tags as $tag_id => $selected) {

					$current_tag_id = $item["tags"] ? arrayKeyValue($item["tags"], "id", $tag_id) : false;

					// Price has changed
					if($selected && $current_tag_id === false) {

						// Add tag
						unset($_POST);
						$_POST["tags"] = $tag_id;
						$this->addTag(["addTag", $item_id]);
						unset($_POST);

					}
					if(!$selected && $current_tag_id !== false) {

						// Delete tag
						$this->deleteTag(["deleteTag", $item_id, $tag_id]);

					}

				}

				message()->resetMessages();
				message()->addMessage("Tags er opdateret.");
				return $item;

			}
		}

		message()->resetMessages();
		message()->addMessage("Noget gik galt. Prøv igen.", array("type" => "error"));
		return false;

	}


	function updated($item_id) {

		$IC = new Items();
		$SC = new Shop();
		include_once("classes/shop/pickupdate.class.php");
		$PC = new Pickupdate();
		$query = new Query();

		$item = $IC->getItem(["id" => $item_id, "extend" => true]);

		// get unshipped order_items for this product
		$sql = "SELECT order_items.* FROM ".SITE_DB.".shop_order_items AS order_items WHERE order_items.item_id = $item_id AND order_items.shipped_by IS NULL";
		if($query->sql($sql)) {

			$order_items = $query->results();

			$order_item_links = [];
			foreach ($order_items as $order_item) {

				$order_item_department_pickupdate = $SC->getOrderItemDepartmentPickupdate($order_item["id"]);

				$order_item_pickupdate = $order_item_department_pickupdate ? $PC->getPickupdate(["id" => $order_item_department_pickupdate["pickupdate_id"]]) : false;

				if($item["end_availability_date"]) {

					if($order_item_pickupdate && ($order_item_pickupdate["pickupdate"] < $item["start_availability_date"] || $order_item_pickupdate["pickupdate"] > $item["end_availability_date"])) {

						$order_item_links[] = SITE_URL."/janitor/order-item/edit/".$order_item["id"];
					}
				}
				else {

					if($order_item_pickupdate && $order_item_pickupdate["pickupdate"] < $item["start_availability_date"]) {

						$order_item_links[] = SITE_URL."/janitor/order-item/edit/".$order_item["id"];
					}
				}
				
			}

			if($order_item_links) {

				// send notification email to admin
				email()->send(array(
					"recipients" => ADMIN_EMAIL,
					"subject" => SITE_URL . " - ACTION NEEDED: A product's availability window was changed, affecting undelivered orders.",
					"message" => "The availability window for the product '".$item['name']."' has been changed in the system. As a consequence there are now ".count($order_item_links)." undelivered order items that fall outside the product's availability period. \n\nHere are links to each of the affected order items:\n\n".implode("\n", $order_item_links). " \n\nFollow the links to resolve the issue manually.",
					"tracking" => false
					// "template" => "system"
				));
				
			}
			
		}

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

	function disabled($item) {

		$item_id = $item["id"];
		$query = new Query();

		// get unshipped order_items for this product
		$sql = "SELECT order_items.* FROM ".SITE_DB.".shop_order_items AS order_items WHERE order_items.item_id = $item_id AND order_items.shipped_by IS NULL";

		if($query->sql($sql)) {

			$order_items = $query->results();

			$order_item_links = [];
			foreach ($order_items as $order_item) {
				
				$order_item_links[] = SITE_URL."/janitor/order-item/edit/".$order_item["id"];
			}

			// send notification email to admin
			email()->send(array(
				"recipients" => ADMIN_EMAIL,
				"subject" => SITE_URL . " - ACTION NEEDED: A product was disabled but has not yet been delivered.",
				"message" => "The product '".$item['name']."' has been disabled in the system. But the product is the content of ".count($order_items)." order items, which have not yet been delivered. \n\nHere are links to each of the affected order items:\n\n".implode("\n", $order_item_links). " \n\nFollow the links to resolve the issue manually.",
				"tracking" => false
				// "template" => "system"
			));
			
		}
	}

	function checkProductAvailability($item_id, $date) {

		$IC = new Items();

		$item =$IC->getItem(["id" => $item_id, "extend" => true]);
		
		// item exists and is a product
		if($item && preg_match("/^product\w*$/", $item["itemtype"])) {
			
			if(
				$item["start_availability_date"] <= $date 
				&& (!$item["end_availability_date"] || $item["end_availability_date"] >= $date)
			) {

				return ["status" => "AVAILABLE"];
			}

			return ["status" => "UNAVAILABLE"];

		}
		
		return false;

	}

}
