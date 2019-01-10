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

class TypeMembership extends Itemtype {

	/**
	* Init, set varnames, validation rules
	*/
	function __construct() {

		// Construct itemtype class and pass itemtype as parameter
		parent::__construct(get_class());


		// itemtype database
		$this->db = SITE_DB.".item_membership";


		// Name
		$this->addToModel("name", array(
			"type" => "string",
			"label" => "Name",
			"required" => true,
			"hint_message" => "Membership name",
			"error_message" => "Membership needs a name."
		));

		// CSS Class
		$this->addToModel("classname", array(
			"type" => "string",
			"label" => "CSS Class",
			"hint_message" => "CSS class for custom styling. If you don't know what this is, just leave it empty"
		));

		// subscribed_message
		$this->addToModel("subscribed_message_id", array(
			"type" => "integer",
			"label" => "Welcome message",
			"required" => true,
			"hint_message" => "Select a message to send to users when they subscribe to this membership"
		));

		// Description
		$this->addToModel("description", array(
			"type" => "text",
			"label" => "SEO description",
			"hint_message" => "Write a short description of the membership for SEO.",
			"error_message" => "A short description without any words? How weird."
		));

		// Introduction
		$this->addToModel("introduction", array(
			"type" => "html",
			"label" => "Introduction for overview",
			"allowed_tags" => "p,h2,h3,h4,ul",
			"hint_message" => "Write a short introduction of the membership.",
			"error_message" => "A short introduction without any words? How weird."
		));

		// Full description
		$this->addToModel("html", array(
			"type" => "html",
			"label" => "Full description",
			"hint_message" => "Write a full description of the membership.",
			"error_message" => "A full description without any words? How weird."
		));

	}

	function shipped($order_item_id, $order) {

		// print "\n<br>###$order_item_id### shipped\n<br>";

	}

	// user subscribed to an item
	function subscribed($subscription) {
		// print_r($subscription);
		
		
		// check for subscription error
		if($subscription && $subscription["item_id"] && $subscription["user_id"] && $subscription["order"]) {

			$item_id = $subscription["item_id"];
			$user_id = $subscription["user_id"];
			$order = $subscription["order"];
			$item_key = arrayKeyValue($order["items"], "item_id", $item_id);
			$order_item = $order["items"][$item_key];
			$message_id = $subscription["item"]["subscribed_message_id"];


			$query = new Query();
			// Add member number as username (if username doesn't already exist)
			$sql = "SELECT username FROM ".SITE_DB.".user_usernames WHERE user_id = $user_id, type = 'member_no'";
			if(!$query->sql($sql)) {

				include_once("classes/users/superuser.class.php");
				$UC = new SuperUser();

				// get member no
				$member = $UC->getMembers(["user_id" => $user_id]);

				// insert as username
				$sql = "INSERT INTO ".SITE_DB.".user_usernames SET user_id = $user_id, username = '".$member["id"]."', type = 'member_no', verified=1, verification_code = '".randomKey(8)."'";
				$query->sql($sql);
			}


			// Set price variable for email
			$price = formatPrice(array("price" => $order_item["total_price"], "vat" => $order_item["total_vat"],  $order_item["total_price"], "country" => $order["country"], "currency" => $order["currency"]));


			// send email
			$IC = new Items();
			$model = $IC->typeObject("message");

			$model->sendMessage([
				"item_id" => $message_id,
				"user_id" => $user_id,
				"values" => ["PRICE" => $price]
			]);

			// Add subscription to log
			global $page;
			$page->addLog("membership->subscribed: item_id:$item_id, user_id:$user_id, order_id:".$order["id"]);

		}

	}

	function unsubscribed($subscription) {

		// check for subscription error
		if($subscription) {

			global $page;
			$page->addLog("membership->unsubscribed: item_id:".$subscription["item_id"].", user_id:".$subscription["user_id"]);

		}

	}

}

?>
