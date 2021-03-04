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

class TypeProductSeasonalbag extends Itemtype {

	/**
	* Init, set varnames, validation rules
	*/
	function __construct() {

		// Construct Itemtype class and pass itemtype as parameter
		parent::__construct(get_class());


		// itemtype database
		$this->db = SITE_DB.".item_product_seasonalbag";


		// Name
		$this->addToModel("name", array(
			"type" => "string",
			"label" => "Name",
			"required" => true,
			"hint_message" => "Give the seasonal bag a name. ",
			"error_message" => "The seasonal bag needs a title."
		));

		// Start availability date
		$this->addToModel("start_availability_date", array(
			"type" => "date",
			"label" => "Start availability date",
			"required" => true,
			"hint_message" => "When does the product become available?.",
			"error_message" => "The product needs a start availability date."
		));

		// End availability date
		$this->addToModel("end_availability_date", array(
			"type" => "date",
			"label" => "End availability date",
			"hint_message" => "When does the product stop being available?.",
			"error_message" => "Invalid end availability date."
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
