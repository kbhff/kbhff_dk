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
		$this->db = SITE_DB.".item_product";
		$this->db_product_types = SITE_DB.".system_product_types";


		// Name
		$this->addToModel("name", array(
			"type" => "string",
			"label" => "Name",
			"required" => true,
			"hint_message" => "Give the weekly bag a name. Preferably use Week number in Danish as name, i.e. 'Uge 21' for consistency.",
			"error_message" => "The weekly bag needs a title."
		));

		// Start availability date
		$this->addToModel("start_availability_date", array(
			"type" => "string",
			"label" => "Start availability date",
			"required" => true,
			"hint_message" => "When does the product become available?.",
			"error_message" => "The product needs a start availability date."
		));

		// End availability date
		$this->addToModel("end_availability_date", array(
			"type" => "string",
			"label" => "End availability date",
			"hint_message" => "When does the product become available?.",
			"error_message" => "Invalid end availability date."
		));

		
		// product_type
		$this->addToModel("product_type", array(
			"type" => "integer",
			"label" => "Product type",
			"required" => true,
			"hint_message" => "State the type of product.",
			"error_message" => "The product needs a product type."
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
			"max" => 1,
			"allowed_formats" => "png,jpg",
			"hint_message" => "Add single image by dragging it here. PNG or JPG allowed.",
			"error_message" => "Media does not fit requirements."
		));

	}

	function getProductTypes() {

		include_once("classes/system/upgrade.class.php");
		$UG = new Upgrade();
		$query = new Query();

		$query->checkDbExistence($this->db_product_types);
		$UG->checkDefaultValues($this->db_product_types);

		$sql = "SELECT * FROM ".$this->db_product_types;

		if($query->sql($sql)) {

			return $query->results();
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
