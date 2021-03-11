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
			"label" => "Short SEO description",
			"max" => 155,
			"hint_message" => "Write a short description of the membership for SEO.",
			"error_message" => "Your membership needs a description – max 155 characters."
		));

		// Introduction
		$this->addToModel("introduction", array(
			"type" => "html",
			"label" => "Introduction for overview",
			"allowed_tags" => "p,h2,h3,h4,ul",
			"hint_message" => "Write a short introduction of the membership.",
			"error_message" => "A short introduction without any words? How weird."
		));

		// HTML
		$this->addToModel("html", [
			"type" => "html",
			"label" => "Full description",
			"allowed_tags" => "p,h2,h3,h4,ul,ol,code,download,jpg,png,vimeo", //,mp4,vimeo,youtube",
			"hint_message" => "Write a full description of the membership.",
			"error_message" => "A full description without any words? How weird."
		]);

		// Single media
		$this->addToModel("single_media", array(
			"type" => "files",
			"label" => "Add media here",
			// "allowed_sizes" => "960x540",
			"min_width" => 640,
			"max" => 1,
			"allowed_formats" => "png,jpg",
			"hint_message" => "Add single image by dragging it here. PNG or JPG allowed, min. 640px wide",
			"error_message" => "Media does not fit requirements."
		));

		// Fixed url id (to allow for prettier and fixed url's – because sindex must be unique, and signupfees and memberships have identical names)
		$this->addToModel("fixed_url_identifier", array(
			"type" => "string",
			"label" => "Fixed URL identifier",
			"hint_message" => "The URL identifier is used for linking to topics. If left empty, this will be based on Membership name.", 
			"error_message" => "Fixed URL identifier has invalid value."
		));

	}

	function enabling($item) {

		if(!$item["subscription_method"]) {

			message()->addMessage("Can't enable. Membership items must have a subscription method.", ["type" => "error"]);
			return false;
		}
	}

	function addedToCart($added_item, $cart) {

		$added_item_id = $added_item["id"];
		// print "\n<br>###$added_item_id### added to cart (membership)\n<br>";
		$SC = new Shop;
		$IC = new Items;
		$query = new Query;

		foreach($cart["items"] as $cart_item) {
			
			$existing_item = $IC->getItem(["id" => $cart_item["item_id"]]);

			// another membership type already exists in cart
			if($existing_item["itemtype"] == "membership" && $existing_item["id"] != $added_item["id"]) {

				// keep the newest membership item
				$SC->deleteFromCart(["deleteFromCart", $cart["cart_reference"], $existing_item["id"]]);

			}
		}
		
		// check quantity
		$sql = "SELECT quantity FROM ".SITE_DB.".shop_cart_items WHERE item_id = ".$added_item["id"]." AND cart_id = ".$cart["id"];
		if($query->sql($sql) && $query->result(0, "quantity") > 1) {

			// ensure that membership item has quantity of 1 
			$sql = "UPDATE ".SITE_DB.".shop_cart_items SET quantity = 1 WHERE item_id = ".$added_item["id"]." AND cart_id = ".$cart["id"];
			// print $sql;
			$query->sql($sql);

			message()->addMessage("Can't update quantity. A Membership can only have a quantity of 1.", ["type" => "error"]);
		}  
		



		global $page;
		$page->addLog("membership->addedToCart: added_item:".$added_item_id);

	}


	function ordered($order_item, $order) {

		include_once("classes/shop/supersubscription.class.php");
		include_once("classes/users/supermember.class.php");
		$SuperSubscriptionClass = new SuperSubscription();
		$MC = new SuperMember();
		$IC = new Items();

		$item = $IC->getItem(["id" => $order_item["item_id"], "extend" => ["subscription_method" => true]]);
		$item_id = $order_item["item_id"];
		
		$order_id = $order["id"];
		$user_id = $order["user_id"];

		if(isset($order_item["custom_price"]) && $order_item["custom_price"] !== false) {
			$custom_price = $order_item["custom_price"];
		}

		$existing_membership = $MC->getMembers(["user_id" => $user_id]);
		
		// user is already member (active or inactive)
		if($existing_membership) {

			// new membership item has a subscription method
			if(SITE_SUBSCRIPTIONS && $item["subscription_method"]) {
				
				// existing membership is active
				if($existing_membership["subscription_id"]) {
					
					// update subscription
					$subscription_id = $existing_membership["subscription_id"];
					$_POST["item_id"] = $item_id;
					$_POST["user_id"] = $user_id;
					$_POST["order_id"] = $order_id;
					if(isset($custom_price) && ($custom_price || $custom_price === "0")) {
						$_POST["custom_price"] = $custom_price;
					}
					else {
						$_POST["custom_price"] = null;
					}					
					
					$subscription = $SuperSubscriptionClass->updateSubscription(["updateSubscription", $subscription_id]);
					unset($_POST);

					
				}
				// existing membership is inactive
				else {

					// add subscription
					$_POST["item_id"] = $item_id;
					$_POST["user_id"] = $user_id;
					$_POST["order_id"] = $order_id;
					if(isset($custom_price) && ($custom_price || $custom_price === "0")) {
						$_POST["custom_price"] = $custom_price;
					}
					else {
						$_POST["custom_price"] = null;
					}					
					
					$subscription = $SuperSubscriptionClass->addSubscription(["addSubscription"]);
					unset($_POST);
				}

				// update membership with subscription_id
				$subscription_id = $subscription["id"];
				$MC->updateMembership(["user_id" => $user_id, "subscription_id" => $subscription_id]);


				// reset user_group to User if new membership is Støttemedlem
				if($item["fixed_url_identifier"] == "stoettemedlem") {

					include_once("classes/users/superuser.class.php");
					$UC = new SuperUser();
	
					$user_groups = $UC->getUserGroups();
					$user_key = arrayKeyValue($user_groups, "user_group", "User");
					$_POST["user_group_id"] = $user_groups[$user_key] ? $user_groups[$user_key]["id"] : false;
					$UC->update(["update", $user_id]);
					unset($_POST);
					
					message()->resetMessages();
				}
			}
			
			// new membership item has no subscription method
			else {
				
				return false;
			}
			
		}
		
		// user is not yet a member
		else {

			// new membership has a subscription method
			if(SITE_SUBSCRIPTIONS && isset($item["subscription_method"]) && $item["subscription_method"]) {
				
				// add subscription
				$_POST["item_id"] = $item_id;
				$_POST["user_id"] = $user_id;
				$_POST["order_id"] = $order_id;
				if(isset($custom_price) && ($custom_price || $custom_price === "0")) {
					$_POST["custom_price"] = $custom_price;
				}
				else {
					$_POST["custom_price"] = null;
				}					$subscription = $SuperSubscriptionClass->addSubscription(["addSubscription"]);
				$subscription_id = $subscription["id"];
				unset($_POST);
	
				// add membership
				$MC->addMembership($item_id, $subscription_id, ["user_id" => $user_id]);
			}
			else {

				return false;
			}
		}
		
		global $page;
		$page->addLog("membership->ordered: order_id:".$order["id"]);
		// print "\n<br>###$item_id### ordered (membership)\n<br>";
	}


	/**
	 * Callback when order is shipped
	 *
	 * @param int $order_item_id
	 * @param array $order The order item.
	 * @return void
	 */
	function shipped($order_item, $order) {


		global $page;
		$page->addLog("membership->shipped: order_id:".$order["id"]);

	}

	/**
	 * 	Callback when user subscribes to a membership item
	 *
	 * @param array $subscription
	 * @return void
	 */
	function subscribed($subscription) {
		// print_r($subscription);
		
		
		// check for subscription error
		if($subscription && $subscription["item_id"] && $subscription["user_id"]) {

			$item_id = $subscription["item_id"];
			$user_id = $subscription["user_id"];
			$order_id = NULL;
			$price = NULL;
			
			if(isset($subscription["order"])) {
				$order = $subscription["order"];
				$item_key = arrayKeyValue($order["items"], "item_id", $item_id);
				$order_id = $order["id"];
				$order_item = $order["items"][$item_key];
				
				// variables for email
				$price = formatPrice(["price" => $order_item["total_price"], "vat" => $order_item["total_vat"],  $order_item["total_price"], "country" => $order["country"], "currency" => $order["currency"]]);
			}

			$message_id = $subscription["item"]["subscribed_message_id"];

			// Set price variable for email
			$price = formatPrice(array("price" => $order_item["total_price"], "vat" => $order_item["total_vat"],  $order_item["total_price"], "country" => $order["country"], "currency" => $order["currency"]));

			$IC = new Items();
			$model = $IC->typeObject("message");

			$model->sendMessage([
				"item_id" => $message_id, 
				"user_id" => $user_id, 
				"values" => ["PRICE" => $price]
			]);


			// set expiry date to May 1st each year

			// current month is May or later
			if(date("m") >= 05) {
				$expiration_year = date("Y") + 1;
			}
			// current month is before May
			else {
				$expiration_year = date("Y");

			}
			$expires_at = $expiration_year."-05-01 00:00:00";
			
			// overwrite the automatically generated expiry date with custom value
			$query = new Query();
			$sql = "UPDATE ".SITE_DB.".user_item_subscriptions SET expires_at = '$expires_at' WHERE id = ".$subscription["id"];
			$query->sql($sql);

			global $page;
			$page->addLog("membership->subscribed: item_id:$item_id, user_id:$user_id, order_id:".$order_id);

		}

	}

	/**
	 * Callback when user unsubscribes an item
	 *
	 * @param array $subscription
	 * @return void
	 */
	function unsubscribed($subscription) {

		// check for subscription error
		if($subscription) {

			global $page;
			$page->addLog("membership->unsubscribed: item_id:".$subscription["item_id"].", user_id:".$subscription["user_id"]);

		}

	}
	
	
	function saved($item_id) {
		
		$query = new Query();
		$IC = new Items();
		$item = $IC->getItem(["id" => $item_id, "extend" => true]);
		
		// insert price type for membership
		$item_id = $item["id"];
		$item_name = $item["name"];
		$normalized_item_name = superNormalize(substr($item_name, 0, 60));
		$sql = "INSERT INTO ".UT_PRICE_TYPES." (item_id, name, description) VALUES ($item_id, '$normalized_item_name', 'Price for \\'$item_name\\' members')";
		$query->sql($sql);
		
		// update fixed_url_identifier based on sindex (if not defined)
		if(!$item["fixed_url_identifier"]) {
			$_POST["fixed_url_identifier"] = $item["sindex"];
			$this->update(["update", $item_id]);			
		}

	}

	// update fixed_url_identifier based on sindex (if not defined)
	function updated($item_id) {

		$IC = new Items();
		$item = $IC->getItem(["id" => $item_id, "extend" => true]);

		if(!$item["fixed_url_identifier"]) {
			$_POST["fixed_url_identifier"] = $item["sindex"];
			// TODO: risky - can cause endless loop
			$this->update(["update", $item_id]);
		}

	}

	function deleting($item_id) {
		$query = new Query();
		$IC = new Items();
		
		$item = $IC->getItem(["id" => $item_id, "extend" => true]);
		$item_id = $item["id"];
		
		$sql = "DELETE FROM ".UT_PRICE_TYPES." WHERE item_id = '$item_id'";
		if($query->sql($sql)) {
			 return true;
		}
		
		message()->addMessage("Can't delete. Could not delete associated price type.", ["type" => "error"]);
		return false;
	}
	

}

?>
