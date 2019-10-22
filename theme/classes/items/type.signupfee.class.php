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

		// Construct Itemtype class and pass itemtype as parameter
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

	function ordered($order_item, $order) {

		include_once("classes/shop/supersubscription.class.php");
		include_once("classes/users/supermember.class.php");
		$SuperSubscriptionClass = new SuperSubscription();
		$MC = new SuperMember();
		$IC = new Items();
		$query = new Query();

		
		$order_id = $order["id"];
		$user_id = $order["user_id"];

		$membership_type = $IC->getItem(["id" => $order_item["associated_membership_id"], "extend" => ["subscription_method" => true]]);
		
		$existing_membership = $MC->getMembers(["user_id" => $user_id]);
		
		// user is already member (active or inactive)
		if($existing_membership) {

			// new membership item has a subscription method
			if(SITE_SUBSCRIPTIONS && $membership_type["subscription_method"]) {
				
				// existing membership is active
				if($existing_membership["subscription_id"]) {
					
					// update subscription
					$subscription_id = $existing_membership["subscription_id"];
					$_POST["item_id"] = $membership_type["id"];
					$_POST["user_id"] = $user_id;
					$_POST["order_id"] = $order_id;
					$subscription = $SuperSubscriptionClass->updateSubscription(["updateSubscription", $subscription_id]);
					unset($_POST);
				}
				// existing membership is inactive
				else {

					// add subscription
					$_POST["item_id"] = $membership_type["id"];
					$_POST["user_id"] = $user_id;
					$_POST["order_id"] = $order_id;
					$subscription = $SuperSubscriptionClass->addSubscription(["addSubscription"]);
					unset($_POST);
				}

				// update membership with subscription_id
				$subscription_id = $subscription["id"];
				$MC->updateMembership(["user_id" => $user_id, "subscription_id" => $subscription_id]);
			}
			
			// new membership item has no subscription method
			else {
				
				// subscriptionless memberships are not allowed (use non-expiring subscription in stead)
				return false;
			}
			
		}
		
		// user is not yet a member
		else {

			// new membership has a subscription method
			if(SITE_SUBSCRIPTIONS && $membership_type["subscription_method"]) {
				
				// add subscription
				$_POST["item_id"] = $membership_type["id"];
				$_POST["user_id"] = $user_id;
				$_POST["order_id"] = $order_id;
				$subscription = $SuperSubscriptionClass->addSubscription(["addSubscription"]);
				$subscription_id = $subscription["id"];
				unset($_POST);
	
				// add membership
				$MC->addMembership($membership_type["id"], $subscription_id, ["user_id" => $user_id]);
			}
			else {

				return false;
			}


			// Add member number as username (if username doesn't already exist)
			$sql = "SELECT username FROM ".SITE_DB.".user_usernames WHERE user_id = $user_id, type = 'member_no'";
			if(!$query->sql($sql)) {
				
				// get member no
				$member = $MC->getMembers(["user_id" => $user_id]);
				// insert as username
				$sql = "INSERT INTO ".SITE_DB.".user_usernames SET user_id = $user_id, username = '".$member["id"]."', type = 'member_no', verified=1, verification_code = '".randomKey(8)."'";
				$query->sql($sql);
			}
		}

		
		global $page;
		$page->addLog("signupfee->ordered: order_id:".$order["id"]);
		// print "\n<br>###$order_item_item_id### ordered (membership)\n<br>";
	}
	
	function shipped($order_item, $order) {	
		// print "\n<br>###$order_item### shipped\n<br>";
		
	}

}

?>
