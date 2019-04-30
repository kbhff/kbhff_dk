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
	function __construct() {

		// Construct Itemtype class and pass itemtype as parameter
		parent::__construct(get_class());


		// itemtype database
		$this->db_item = SITE_DB.".items";
		$this->db = SITE_DB.".item_product";
		$this->db_itemtype = SITE_DB.".item_product";
		$this->db_item_dept = SITE_DB.".item_department";
		$this->db_item_prices = SITE_DB.".items_prices";


		// Name
		$this->addToModel("name", array(
			"type" => "string",
			"label" => "Name",
			"required" => true,
			"hint_message" => "Product name",
			"error_message" => "Product needs a name."
		));

		// PRIS (ALM MEDLEM)
		$this->addToModel("price_default", array(
			"type" => "string",
			"label" => "Pris (ALM MEDLEM)",
			"required" => true,
			"hint_message" => "",
			"error_message" => "PRIS (ALM MEDLEM) is required."
		));

		// PRIS (STØTTEMEDLEM)
		$this->addToModel("price_offer", array(
			"type" => "string",
			"label" => "Pris (STØTTEMEDLEM)",
			"required" => false,
			"hint_message" => "",
			"error_message" => ""
		));

		// PRODUKTBILLEDE
		$this->addToModel("image", array(
			"type" => "file",
			"label" => "PRODUKTBILLEDE",
			"required" => false,
			"hint_message" => "",
			"error_message" => ""
		));

		// PRODUKTBESKRIVELSE
		$this->addToModel("description", array(
			"type" => "string",
			"label" => "PRODUKTBESKRIVELSE",
			"required" => false,
			"hint_message" => "",
			"error_message" => ""
		));

		// PRODUKTTYPE
		$this->addToModel("producttype", array(
			"type" => "string",
			"label" => "PRODUKTTYPE",
			"required" => true,
			"hint_message" => "",
			"error_message" => "PRODUKTTYPE is required"
		));

		// AVLER/LEVERANDØR
		$this->addToModel("supplier", array(
			"type" => "string",
			"label" => "AVLER/LEVERANDØR",
			"required" => true,
			"hint_message" => "",
			"error_message" => "AVLER/LEVERANDØR is Requiered"
		));

		// HVORNÅR VAREN KAN KØBES
		$this->addToModel("productAvailability", array(
			"type" => "string",
			"label" => "HVORNÅR VAREN KAN KØBES",
			"required" => true,
			"hint_message" => "productAvailability",
			"error_message" => "Availability is Requiered"
		));

		// HVORNÅR VAREN KAN KØBES
		$this->addToModel("departments", array(
			"type" => "string",
			"label" => "HVORNÅR VAREN KAN KØBES",
			"required" => false,
			"hint_message" => "",
			"error_message" => ""
		));

		// HVORNÅR VAREN KAN KØBES
		$this->addToModel("status", array(
			"type" => "boolean",
			"label" => "Status",
			"required" => false,
			"hint_message" => "",
			"error_message" => ""
		));


	}
	/**
	 * Shop clerk creates / modifies new product via INDKØB
	 *
	 * @param array $action REST parameters of current request
	 * @return array|false Success: array with user Item created / mdified. Error: false or array with error message. 
	 */
	function saveItemFromIndkoeb($action) {
		// Log that the method has been started
		global $page;
		$user_id = session()->value("user_id");
		$page->addLog("items->newItemFromIndkoeb: initiated by user_id $user_id");

		// Get content of $_POST array which have been "quality-assured" by Janitor 
		$this->getPostedEntities();
		$entities = $this->getModel();

		$name = $this->getProperty("name", "value");

		// Check if values validate – minimum is name and price_default
		if (!$this->validateList(array("name", "price_default", "supplier", "productAvailability"))) {
			return false;
		}
		// get entities for current value

		$names = array();
		$values = array();
		$price_values = array();
		$image_values = array();
		$departments = array();
		$status = false;

		foreach($entities as $name => $entity) {
			if($entity["value"] !== false) {
				$names[] = $name;
				if(preg_match("/^(name|description|producttype|supplier|productAvailability)$/", $name)) {
					# item_product
					$values[] = $name."='".$entity["value"]."'";
				} elseif(preg_match("/^(price_offer|price_default)$/", $name)) {
					# items_prices
					$price_type = explode("_", $name)[1];
					$price_values[$price_type] = $entity["value"];
				} elseif(preg_match("/^(image|mediae)$/", $name)) {
					# items_mediae

//					$_POST[$name]['tmp_name'] = $entity["value"][0];
					$image_values[$name] = $entity;
				} elseif(preg_match("/^(departments)$/", $name)) {
					foreach ($entity["value"] as $dept_id => $value) {
						if ($value) {
							$departments[] = $dept_id;
						}
					}
				} elseif(preg_match("/^(status)$/", $name)) {
					# status 0/1 > if empty, nothing changes
					if ($entity["value"] == "1") {
						$status = "1";
					} elseif ($entity["value"] == "0") {
						$status = "0";
					}

				} else {
					print("Property $name is unknown in entities.");
					exit();	
				}
			}
		}
		global $mysqli_global;
//		$mysqli_global->autocommit(false);
//		$mysqli_global->begin_transaction(MYSQLI_TRANS_START_READ_ONLY);

		try {
			$query = new Query();
			if(count($action) == 1) {
				# no more checks, create the item to get the new id
				if ($status === false) {
					$status = 0;
				}
				$sql = "INSERT INTO ".$this->db_item." SET sindex = 'produkt', status = $status, itemtype = 'product', user_id = '$user_id'" ;
				if(!$query->sql($sql)) {
					throw new Exception("Error Processing Request:<br> $sql<br>".$query->dbError()."<br>", 1);
				}
				$item_id = $query->lastInsertId();
			} else {
				$item_id = $action[1];
				# update status if status has been defined
				if ($status !== false) {
					$tmp_action = array("status", $item_id, $status);
					if(!$this->status($tmp_action)) {
						message()->addMessage("Status could not be modified.", array("type"=>"error"));
					} else {
						message()->addMessage("Status has been modified.",array("type" => "message"));
					}
				}
			}

				// foreach ($image_values as $name => $entity) {
				// 	print("item $item_id >>> doing image: $name >>>> ".$entity['value'][0]."<br>");
				// 	//print_r($this->upload($item_id, array("input_name" => $entity['value'][0], "variant" => "prdukt")));
				// 	print("<hr>addMedia:<br>");
				// 	print_r($this->addMedia($action));
				// 	print("<hr>");
				// 	print_r($entity);
				// 	print "The end";
				// }

			// add the item_id to $values array, which will define the new item 
			$values[] = "item_id= '$item_id'";
			
			// Item type product item_product
			$sql = "DELETE FROM ".$this->db_itemtype." WHERE item_id = '$item_id'";
			if(!$query->sql($sql)) {
				throw new Exception("Error Processing Request $sql<br>".$query->dbError()."<br>", 1);
			}
			// Create query string by imploding the $values array into a comma-separated string
			$sql = "INSERT INTO ".$this->db_itemtype." SET " . implode(", ", $values);
			if(!$query->sql($sql)) {
				throw new Exception("Error Processing Request $sql<br>".$query->dbError()."<br>", 1);
			}
			
			// Departments
			$sql = "DELETE FROM $this->db_item_dept WHERE item_id = '$item_id'";
			if(!$query->sql($sql)) {
				throw new Exception("Error Processing Request $sql<br>".$query->dbError()."<br>", 1);
			}
			// add departments for the item
			foreach ($departments as $department) {
				$sql = "INSERT INTO $this->db_item_dept SET item_id = '$item_id', department_id = '$department'";
				if(!$query->sql($sql)) {
					throw new Exception("Error Processing Request $sql<br>".$query->dbError()."<br>", 1);
				}
			}
			// prices 
			$sql = "DELETE FROM ".UT_ITEMS_PRICES." WHERE item_id = '$item_id'";
			if(!$query->sql($sql)) {
				throw new Exception("Error Processing Request $sql<br>".$query->dbError()."<br>", 1);
			}
			
			// add prices for the item
			foreach ($price_values as $type => $price) {
				$price = preg_replace("/,/", ".", $price);
				if ($price != "") {
					$sql = "INSERT INTO ".UT_ITEMS_PRICES." VALUES(DEFAULT, $item_id, '$price', 'DKK', 1, '$type', null)";
					if(!$query->sql($sql)) {
						throw new Exception("Error Processing Request $sql<br>".$query->dbError()."<br>", 1);
					}
				}
			}
		 	$mysqli_global->commit();
		} catch (Exception $e) {
			$mysqli_global->rollBack();
			message()->addMessage("Item could not be saved.<br>".$e->getMessage(), array("type" => "error"));
			return false;
		} 
		$IC = new Items();
		return $IC->getItem(array("id" => $item_id, "extend" => array("all" => true)));
	}


}

?>
