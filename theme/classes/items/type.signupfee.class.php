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

class TypeSignupfee extends Itemtype {

	/**
	* Init, set varnames, validation rules
	*/
	function __construct() {

		parent::__construct(get_class());


		// itemtype database
		$this->db = SITE_DB.".item_signupfee";


		// Name
		$this->addToModel("name", array(
			"type" => "string",
			"label" => "Name",
			"required" => true,
			"hint_message" => "Signup fee name",
			"error_message" => "Signup fee needs a name."
		));

		// Class
		$this->addToModel("classname", array(
			"type" => "string",
			"label" => "CSS Class",
			"hint_message" => "CSS class for custom styling. If you don't know what this is, just leave it empty"
		));

		// Associated membership type
		$this->addToModel("associated_membership_id", array(
			"type" => "select",
			"label" => "Associated membership type",
			"required" => true,
			"hint_message" => "Select a membership that will apply to users when they pay this signup fee",
			"error_message" => "A signup fee must be associated with a membership type"
			
		));
		
		// Description
		$this->addToModel("description", array(
			"type" => "text",
			"label" => "SEO description",
			"hint_message" => "Write a short description of the signup fee for SEO.",
			"error_message" => "A short description without any words? How weird."
		));
		
		// HTML
		$this->addToModel("html", array(
			"type" => "html",
			"label" => "Full description",
			"hint_message" => "Write a full description of the signup fee.",
			"error_message" => "A full description without any words? How weird."
		));
		
	}
	
	function shipped($order_item, $order) {
		
		// print "\n<br>###$order_item### shipped\n<br>";

		include_once("classes/users/superuser.class.php");
		$UC = new SuperUser();

		include_once("classes/items/items.class.php");
		$IC = new Items();
		$signupfee_item = $IC->getItem(array("id" => $order_item["item_id"], "extend" => true));

		

		

		// set values for creating subscription

		$_POST["order_id"] = $order["id"];
		$_POST["item_id"] = $signupfee_item["associated_membership_id"];
		$_POST["user_id"] = $order["user_id"];

		// print_r($order); 
		// print_r($signupfee_item); 
		// print_r($order_item); 
		// print_r($signupfee_item["associated_membership_id"]); exit(); 
		
		$subscription = $UC->addSubscription(array("addSubscription"));		
		if ($subscription) {
			$expires_at = "2019-05-01 00:00:00";
			
			$query = new Query();
			$sql = "UPDATE ".SITE_DB.".user_item_subscriptions SET expires_at = '$expires_at' WHERE id = ".$subscription["id"];
			if($query->sql($sql)) {
				return true;
			}
		}
		
		return false;


		
	}

}

?>
