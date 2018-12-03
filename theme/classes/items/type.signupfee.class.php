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
					"hint_message" => "Membership name",
					"error_message" => "Membership needs a name."
				));

				$this->addToModel("item_price", array(
					"type" => "string",
					"label" => "New price",
					"pattern" => "[0-9,]+",
					"class" => "price",
					"required" => true,
					"hint_message" => "State the price INCLUDING VAT, using comma (,) as decimal point.",
					"error_message" => "Price cannot be empty."
				));
				
				$this->addToModel("item_price_vatrate", array(
					"type" => "integer",
					"label" => "Vatrate",
					"class" => "vatrate",
					"required" => true,
					"hint_message" => "VAT rate for this product.",
					"error_message" => "VAT rate cannot be empty."
				));
				function shipped($order_item_id, $order) {

					// print "\n<br>###$order_item_id### shipped\n<br>";

				}

				// user subscribed to an item
				function subscribed($subscription) {
			//		print_r($subscription);

					// check for subscription error
					if($subscription && $subscription["item_id"] && $subscription["user_id"] && $subscription["order"]) {

						$item_id = $subscription["item_id"];
						$user_id = $subscription["user_id"];
						$order = $subscription["order"];
						$item_key = arrayKeyValue($order["items"], "item_id", $item_id);
						$order_item = $order["items"][$item_key];

						$message_id = $subscription["item"]["subscribed_message_id"];

						// variables for email
						$price = formatPrice(array("price" => $order_item["total_price"], "vat" => $order_item["total_vat"],  $order_item["total_price"], "country" => $order["country"], "currency" => $order["currency"]));


						$IC = new Items();
						$model = $IC->typeObject("message");

						$model->sendMessage([
							"item_id" => $message_id,
							"user_id" => $user_id,
							"values" => ["PRICE" => $price]
						]);

						global $page;
						$page->addLog("membership->subscribed: item_id:$item_id, user_id:$user_id, order_id:".$order["id"]);


			//
			//
			// 			$classname = $subscription["item"]["classname"];
			//
			//
			// 			$UC = new User();
			//
			// 			// switch user id to enable user data collection
			// 			$current_user_id = session()->value("user_id");
			// 			session()->value("user_id", $user_id);
			//
			// 			// get user, order and  info
			// 			$user = $UC->getUser();
			//
			// 			// switch back to correct user
			// 			session()->value("user_id", $current_user_id);
			//
			//
			// //			print "subscription:\n";
			// //			print_r($subscription);
			//
			// 			// variables for email
			// 			$nickname = $user["nickname"];
			// 			$email = $user["email"];
			// 			$membership = $user["membership"];
			//
			// 			// print "nickname:" . $nickname."<br>\n";
			// 			// print "email:" . $email."<br>\n";
			// 			// print "classname:" . $classname."<br>\n";
			// 			// print "member no:" . $membership["id"]."<br>\n";
			// 			// print "membership:" . $membership["item"]["name"]."<br>\n";
			// 			// print "price:" . $price."\n";
			//
			//
			// 			//$nickname = false;
			// 			if($nickname && $email && $membership && $price && $classname) {
			//
			// 				mailer()->send(array(
			// 					"values" => array(
			// 						"ORDER_NO" => $order["order_no"],
			// 						"MEMBER_ID" => $membership["id"],
			// 						"MEMBERSHIP" => $membership["item"]["name"],
			// 						"PRICE" => $price,
			// 						"EMAIL" => $email,
			// 						"NICKNAME" => $nickname
			// 					),
			// 					"recipients" => $email,
			// 					"template" => "subscription_".$classname
			// 				));
			//
			// 				// send notification email to admin
			// 				mailer()->send(array(
			// 					"recipients" => SHOP_ORDER_NOTIFIES,
			// 					"subject" => SITE_URL . " - New ".$subscription["item"]["name"].": " . $email,
			// 					"message" => "Do something"
			// 				));
			//
			// 			}
			// 			else {
			//
			// 				// send notification email to admin
			// 				mailer()->send(array(
			// 					"subject" => "ERROR: subscription creation: " . $email,
			// 					"message" => "Do something",
			// 					"template" => "system"
			// 				));
			//
			// 			}

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
