<?php
/**
* @package janitor.shop
* Meant to allow local shop additions/overrides
*/


class Shop extends ShopCore {

	/**
	* Defines order-statuses in danish. 
	*/
	function __construct() {

		// receiving user id
		$this->addToModel("receiving_user_id", array(
			"type" => "integer",
			"label" => "Receiving user_id",
			"required" => true,
			"hint_message" => "The user_id that accepted the payment from memberhelp",
			"error_message" => "Error"
		));

		parent::__construct(get_class());

		$this->db_pickupdates = SITE_DB.".project_pickupdates";
		$this->db_pickupdate_cart_items = SITE_DB.".project_pickupdate_cart_items";
		$this->db_pickupdate_order_items = SITE_DB.".project_pickupdate_order_items";

		$this->order_statuses_dk = array(0 => "Ny", 1 => "Afventer", 2 => "Færdig", 3 => "Annulleret");


		// payment and shipping statuses
		$this->payment_statuses_dk = array(0 => "Ikke betalt", 1 => "Delvist betalt", 2 => "Betalt");
		$this->shipping_statuses_dk = array(0 => "Ikke modtaget", 1 => "Delvist afsendt", 2 => "Afsendt");

	}
	
	// Add item to cart
	# /shop/addToCart
	// Items and quantity in $_post
	function addToCart($action) {

		if(count($action) >= 1) {

			// Get posted values to make them available for models
			$this->getPostedEntities();

			$user_id = session()->value("user_id");

			$cart = false;

			// getCart checks for cart_reference in session and cookie or looks for cart for current user ( != 1)
			$cart = $this->getCart();
			if($cart) {
				$cart_reference = $cart["cart_reference"];
			}
			// still no cart
			// then add a new cart
			else {
				$cart = $this->addCart(array("addCart"));
	//				print_r($cart);
				
				$cart_reference = $cart["cart_reference"];
			}

			// does values validate
			if($cart && $this->validateList(array("quantity", "item_id"))) {

				$query = new Query();
				$IC = new Items();

				$quantity = $this->getProperty("quantity", "value");
				$item_id = $this->getProperty("item_id", "value");
				$pickupdate_id = getPost("pickupdate_id", "value");
				
				$item = $IC->getItem(array("id" => $item_id));

				// make sure only one membership exists in cart at any given time

				// are there any items in cart already?
				if($cart["items"]) {

					// what kind of itemtype is being added
					// if it is a membership, then remove existing memberships from cart
					if($item["itemtype"] == "signupfee") {

						foreach($cart["items"] as $key => $cart_item) {
							$existing_item = $IC->getItem(array("id" => $cart_item["item_id"]));
							if($existing_item["itemtype"] == "signupfee") {
								$cart = $this->deleteFromCart(array("deleteFromCart", $cart_reference, $cart_item["id"]));
							}
						}
					}
				}

				// same item_id with same pickupdate is already in cart
				$existing_cart_item = $this->getExistingCartItem($cart["id"], $item_id, $pickupdate_id);
				if($cart["items"] && $existing_cart_item) {
					
					$existing_quantity = $existing_cart_item["quantity"];
					$new_quantity = intval($quantity) + intval($existing_quantity);

					// update cart item quantity
					$sql = "UPDATE ".$this->db_cart_items." SET quantity=$new_quantity WHERE id = ".$existing_cart_item["id"]." AND cart_id = ".$cart["id"];
					// print $sql;
					
				}
				else {
					
					// insert new cart item
					$sql = "INSERT INTO ".$this->db_cart_items." SET cart_id=".$cart["id"].", item_id=$item_id, quantity=$quantity";
					// print $sql;
				}				

				if($query->sql($sql)) {
					
					if($existing_cart_item) {
						$cart_item_id = $existing_cart_item["id"];
					}
					else {
						$cart_item_id = $query->lastInsertId();
						if($pickupdate_id) {
							$this->addPickupdateCartItem($pickupdate_id, $cart_item_id);
						}
					}
					
					
					// update modified at time
					$sql = "UPDATE ".$this->db_carts." SET modified_at=CURRENT_TIMESTAMP WHERE id = ".$cart["id"];
					$query->sql($sql);

					// add callback to addedToCart
					$model = $IC->typeObject($item["itemtype"]);
					if(method_exists($model, "addedToCart")) {
						$model->addedToCart($item, $cart);
					}

					

					return $this->getCart();

				}
			}
		}
		return false;
	}
	
	function deleteSignupfeesAndMembershipsFromCart() {
		
		if($cart = $this->deleteItemtypeFromCart("signupfee")) {
			
			if($cart = $this->deleteItemtypeFromCart("membership")) {
				return $cart;
			}		
		}
		return false;	
	}

	function getCartPickupdates() {
		
		$cart = $this->getCart();
		
		if($cart && $cart["items"]) {

			$query = new Query();
			$query->checkDbExistence($this->db_pickupdate_cart_items);

			$cart_id = $cart["id"];

			$sql = "SELECT DISTINCT pickupdates.* FROM ".$this->db_pickupdates." AS pickupdates, ".$this->db_pickupdate_cart_items." AS pickupdate_cart_items, ".$this->db_cart_items." AS cart_items WHERE cart_items.cart_id = $cart_id AND cart_items.id = pickupdate_cart_items.cart_item_id AND pickupdates.id = pickupdate_cart_items.pickupdate_id";
			if($query->sql($sql)) {
	
				$cart_pickupdates = $query->results();
	
				return $cart_pickupdates;
			}
		}


		return false;
	}
	
	function getCartPickupdateItems($pickupdate_id) {

		$query = new Query();
		$cart = $this->getCart();

		if($cart && $cart["items"]) {

			$sql = "SELECT cart_items.* FROM ".$this->db_pickupdate_cart_items." AS pickupdate_cart_items, ".$this->db_cart_items." AS cart_items WHERE pickupdate_cart_items.pickupdate_id = $pickupdate_id AND cart_items.id = pickupdate_cart_items.cart_item_id";
			if($query->sql($sql)) {
				
				$cart_pickupdate_items = $query->results();
				
				return $cart_pickupdate_items;
				
			}
		}


		
		return false;
	}

	function getCartItemsWithoutPickupdate() {

		$query = new Query();
		$cart = $this->getCart();
		$cart_id = $cart["id"];

		if($cart && $cart["items"]) {

			$sql = "SELECT cart_items.* 
			FROM ".$this->db_cart_items." AS cart_items
			WHERE cart_items.id NOT IN (
				SELECT pickupdate_cart_items.cart_item_id 
				FROM ".$this->db_pickupdate_cart_items." AS pickupdate_cart_items 
				) 
			AND cart_items.cart_id = $cart_id";

			if($query->sql($sql)) {

				$cart_items_without_pickupdate = $query->results();

				return $cart_items_without_pickupdate;
			}
		}

		return false;
	}

	function getCartItemPickupdate($cart_item_id) {
		
		
		$query = new Query();
		$query->checkDbExistence($this->db_pickupdate_cart_items);

		$sql = "SELECT pickupdates.* FROM ".$this->db_pickupdates." AS pickupdates, ".$this->db_pickupdate_cart_items." AS pickupdate_cart_items WHERE pickupdate_cart_items.cart_item_id = $cart_item_id AND pickupdates.id = pickupdate_cart_items.pickupdate_id";
		if($query->sql($sql)) {

			$cart_item_pickupdate = $query->result(0);

			return $cart_item_pickupdate;
		}

		return false;
	}

	function addPickupdateCartItem($pickupdate_id, $cart_item_id) {
		
		$query = new Query();
		$query->checkDbExistence($this->db_pickupdate_cart_items);

		$sql = "INSERT INTO ".$this->db_pickupdate_cart_items." SET pickupdate_id = $pickupdate_id, cart_item_id = $cart_item_id";
		if($query->sql($sql)) {

			return $this->getCartPickupdateItems($pickupdate_id);
		}

		return false;
		
	}

	function getExistingCartItem($cart_id, $item_id, $pickupdate_id) {

		$query = new Query();

		if($pickupdate_id) {

			$sql = "SELECT cart_items.* 
			FROM ".$this->db_cart_items." AS cart_items, ".$this->db_pickupdate_cart_items." AS pickupdate_cart_items 
			WHERE cart_items.cart_id = $cart_id 
			AND cart_items.item_id = $item_id 
			AND cart_items.id = pickupdate_cart_items.cart_item_id 
			AND pickupdate_cart_items.pickupdate_id = $pickupdate_id";
		}
		else {

			$sql = "SELECT cart_items.* 
			FROM ".$this->db_cart_items." AS cart_items 
			WHERE cart_items.cart_id = $cart_id 
			AND cart_items.item_id = $item_id
			AND cart_items.id NOT IN (
				SELECT pickupdate_cart_items.cart_item_id 
				FROM ".$this->db_pickupdate_cart_items." AS pickupdate_cart_items 
				)
			";
		}


		if($query->sql($sql)) {

			$existing_cart_item = $query->result(0);

			return $existing_cart_item;
		}

		return false;
	}

	function addPickupdateOrderItem($pickupdate_id, $order_item_id) {

		$query = new Query();
		$query->checkDbExistence($this->db_pickupdate_order_items);

		$sql = "INSERT INTO ".$this->db_pickupdate_order_items." SET pickupdate_id = $pickupdate_id, order_item_id = $order_item_id";
		if($query->sql($sql)) {

			return true;
		}

		return false;
		
	}



	/**
	 * ### Convert cart to order
	 * 
	 * Custom version receives pickupdate_ids from cart_items
	 * 
	 * /shop/newOrderFromCart/#cart_reference#
	 * order_comment in $_POST
	 *
	 * @param array $action
	 * @return array|false Order object. False on error. 
	 */
// 	function newOrderFromCart($action) {
// //		debug(["newOrderFromCart", $action]);

// 		// Get posted values to make them available for models
// 		$this->getPostedEntities();

// 		// does values validate
// 		if(count($action) == 2) {

// 			$query = new Query();
// 			$UC = new User();
// 			$IC = new Items();

// 			$order_comment = $this->getProperty("order_comment", "value");

// 			$cart_reference = $action[1];
// 			$received_cart = $this->getCarts(["cart_reference" => $cart_reference]);

// 			// you can never create a cart for someone else, so ignore cart user_id
// 			$user_id = session()->value("user_id");

// 			$cart = $this->getCart();
			
// 			// user cart matches cart received via REST
// 			if($cart["cart_reference"] == $cart_reference) {
// 				$cart_match = true;
// 			}
// 			// received cart is an internal cart
// 			else if($received_cart["user_id"] == $user_id) {
// 				$cart_match = true;
// 				$cart = $received_cart;
// 			}			
// 			// cart mismatch
// 			else {
// 				$cart_match = false;
// 			}

// 			// is cart registered and has content
// //			print $cart_reference ." ==". $cart["cart_reference"];
// 			if($cart && $user_id && $cart["items"] && $cart_match) {

// 				$user = $UC->getUser();

// 				// get new order number
// 				$order_no = $this->getNewOrderNumber();
// 				if($order_no) {


// 					// get data from cart
// 					$currency = $cart["currency"];
// 					$country = $cart["country"];

// 					$delivery_address_id = $cart["delivery_address_id"];
// 					$delivery_address = false;
// 					$billing_address_id = $cart["billing_address_id"];
// 					$billing_address = false;

// 					// create base data update sql
// 					$sql = "UPDATE ".$this->db_orders." SET user_id=$user_id, country='$country', currency='$currency'";
// //					print $sql."<br />\n";

// 					// add delivery address
// 					if($delivery_address_id) {
// 						$delivery_address = $UC->getAddresses(array("address_id" => $delivery_address_id));
// 						if($delivery_address) {
// 							$sql .= ", delivery_name='".prepareForDB($delivery_address["address_name"])."'";
// 							$sql .= ", delivery_att='".prepareForDB($delivery_address["att"])."'";
// 							$sql .= ", delivery_address1='".prepareForDB($delivery_address["address1"])."'";
// 							$sql .= ", delivery_address2='".prepareForDB($delivery_address["address2"])."'";
// 							$sql .= ", delivery_city='".prepareForDB($delivery_address["city"])."'";
// 							$sql .= ", delivery_postal='".prepareForDB($delivery_address["postal"])."'";
// 							$sql .= ", delivery_state='".prepareForDB($delivery_address["state"])."'";
// 							$sql .= ", delivery_country='".prepareForDB($delivery_address["country"])."'";
// 						}
// 					}

// 					// add billing address
// 					if($billing_address_id) {
// 						$billing_address = $UC->getAddresses(array("address_id" => $billing_address_id));
// 						if($billing_address) {
// 							$sql .= ", billing_name='".prepareForDB($billing_address["address_name"])."'";
// 							$sql .= ", billing_att='".prepareForDB($billing_address["att"])."'";
// 							$sql .= ", billing_address1='".prepareForDB($billing_address["address1"])."'";
// 							$sql .= ", billing_address2='".prepareForDB($billing_address["address2"])."'";
// 							$sql .= ", billing_city='".prepareForDB($billing_address["city"])."'";
// 							$sql .= ", billing_postal='".prepareForDB($billing_address["postal"])."'";
// 							$sql .= ", billing_state='".prepareForDB($billing_address["state"])."'";
// 							$sql .= ", billing_country='".prepareForDB($billing_address["country"])."'";
// 						}
// 					}

// 					// no billing info is provided
// 					if(!$billing_address) {

// 						// use available account info
// 						$user = $UC->getUser();
// 						if($user["firstname"] && $user["lastname"]) {
// 							$sql .= ", billing_name='".prepareForDB($user["firstname"])." ".prepareForDB($user["lastname"])."'";
// 						}
// 						else {
// 							$sql .= ", billing_name='".prepareForDB($user["nickname"])."'";
// 						}
// 					}


// 					// finalize sql
// 					$sql .= " WHERE order_no='$order_no'";

// //					print $sql;
// 					// execute "create order" query 
// 					if($query->sql($sql)) {


// 						// get the new order
// 						$order = $this->getOrders(array("order_no" => $order_no));


// 						$admin_summary = [];
// //						print "items";
// //						print_r($cart["items"]);

// 						// add the items from the cart
// 						foreach($cart["items"] as $cart_item) {

// 							$quantity = $cart_item["quantity"];
// 							$item_id = $cart_item["item_id"];
// 							$cart_item_pickupdate = $this->getCartItemPickupdate($cart_item["id"]);

// 							// get item details
// 							$item = $IC->getItem(["id" => $item_id, "extend" => true]);

// 							if($item) {

// 								// get best price for item
// 								$price = $this->getPrice($item_id, array("quantity" => $quantity, "currency" => $order["currency"], "country" => $order["country"]));
// 								// print_r("price: ".$price);

// 								// use custom price if available
// 								if(isset($cart_item["custom_price"]) && $cart_item["custom_price"] !== false) {
// 									$custom_price = $cart_item["custom_price"];
									
// 									$price["price"] = $custom_price;
// 									$custom_price_without_vat = $custom_price / (100 + $price["vatrate"]) * 100;
// 									$price["price_without_vat"] = $custom_price_without_vat;
// 									$price["vat"] = $custom_price - $custom_price_without_vat;
// 								}

// 								$unit_price = $price["price"];
// 								$unit_vat = $price["vat"];
// 								$total_price = $unit_price * $quantity;
// 								$total_vat = $unit_vat * $quantity;

// 								// use custom name for cart item if available
// 								$item_name = isset($cart_item["custom_name"]) ? $cart_item["custom_name"] : $item["name"];

// 								$sql = "INSERT INTO ".$this->db_order_items." SET order_id=".$order["id"].", item_id=$item_id, name='".prepareForDB($item_name)."', quantity=$quantity, unit_price=$unit_price, unit_vat=$unit_vat, total_price=$total_price, total_vat=$total_vat";
// 								// print $sql;


// 								// Add item to order
// 								if($query->sql($sql)) {
// 									$order_item_id = $query->lastInsertId();

// 									$admin_summary[] = $item_name;

// 									// get order_item
// 									$sql = "SELECT * FROM ".$this->db_order_items." WHERE id = $order_item_id";
// 									if($query->sql($sql)) {
// 										$order_item = $query->result(0);

// 										$order_item["custom_price"] = isset($custom_price) ? $custom_price : null;

// 										// add callback to 'ordered'
// 										$model = $IC->typeObject($item["itemtype"]);
// 										if(method_exists($model, "ordered")) {

// 											$model->ordered($order_item, $order);
// 										}
// 									}
									
// 								}

// 							}

// 						}
				

// 						// update cart_reference cookie and session
// 						session()->reset("cart_reference");

// 						// Delete cart reference cookie
// 						setcookie("cart_reference", "", time() - 3600, "/");


// 						// make sure order no is set for user
// 						session()->value("order_no", $order_no);

// 						// Add cookie for user
// 						setcookie("order_no", $order_no, time()+60*60*24*60, "/");

// 						if($order_comment) {
							
// 							$sql = "UPDATE ".$this->db_orders." SET comment = '".$order_comment."' WHERE order_no='$order_no'";
// 							$query->sql($sql);
// 						}
						
// 						// only autoship order if every item should be autoshipped
// 						$order["autoship"] = true;
// 						foreach($cart["items"] as $cart_item) {
// 							if(!isset($item["autoship"]) || !$item["autoship"]) {
// 								$order["autoship"] = false;
// 							}
// 						}
// 						if($order["autoship"]) {
// 							// update shipping_status to shipped
// 							$sql = "UPDATE ".$this->db_orders." SET shipping_status = 2 WHERE order_no='$order_no'";
// 							$query->sql($sql);
// 						}


// 						// set payment status for 0-prices orders
// 						$order = $this->getOrders(array("order_no" => $order_no));
// 						$total_order_price = $this->getTotalOrderPrice($order["id"]);
// 						if($total_order_price["price"] === 0) {
// 							$sql = "UPDATE ".$this->db_orders." SET status = 1, payment_status = 2 WHERE order_no='$order_no'";
// 							$query->sql($sql);
// 						}


// 						// delete cart
// 						$sql = "DELETE FROM $this->db_carts WHERE id = ".$cart["id"]." AND cart_reference = '".$cart["cart_reference"]."'";
// 						// debug([$sql]);
// 						$query->sql($sql);


// 						// send notification email to admin
// 						mailer()->send(array(
// 							"recipients" => SHOP_ORDER_NOTIFIES,
// 							"subject" => SITE_URL . " - New order ($order_no) created by: $user_id",
// 							"message" => "Check out the new order: " . SITE_URL . "/janitor/admin/user/orders/" . $user_id . "\n\nOrder content: ".implode(",", $admin_summary),
// 							"tracking" => false
// 							// "template" => "system"
// 						));


// 						// order confirmation mail
// 						mailer()->send(array(
// 							"recipients" => $user["email"],
// 							"values" => array(
// 								"NICKNAME" => $user["nickname"], 
// 								"ORDER_NO" => $order_no, 
// 								"ORDER_ID" => $order["id"], 
// 								"ORDER_PRICE" => formatPrice($total_order_price) 
// 							),
// 							// "subject" => SITE_URL . " – Thank you for your order!",
// 							"tracking" => false,
// 							"template" => "order_confirmation"
// 						));

						

// 						global $page;
// 						$page->addLog("Shop->newOrderFromCart: order_no:".$order_no);


// 						return $this->getOrders(array("order_no" => $order_no));

// 					}
// 				}

// 				// order creation failed, remove unused order number
// 				$this->deleteOrderNumber($order_no);

// 			}

// 		}

// 		return false;
// 	}	

	function getOrderItemsPickupdates($_options = false) {
		
		$after = false;
		
		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {
					case "after"             : $after                  = $_value; break;
				}
			}
		}

		
		$query = new Query();
		$query->checkDbExistence($this->db_pickupdate_order_items);

		$user_id = session()->value("user_id");

		$sql = "SELECT DISTINCT pickupdates.* 
		FROM ".$this->db_pickupdates." AS pickupdates, "
		.$this->db_pickupdate_order_items." AS pickupdate_order_items, "
		.$this->db_order_items." AS order_items, "
		.$this->db_orders." AS orders 
		WHERE orders.user_id = $user_id
		AND order_items.order_id = orders.id
		AND pickupdate_order_items.order_item_id = order_items.id 
		AND pickupdates.id = pickupdate_order_items.pickupdate_id";

		if($after) {
			$sql .= " AND pickupdates.pickupdate >= '$after'";
		}

		if($query->sql($sql)) {

			$order_items_pickupdates = $query->results();

			return $order_items_pickupdates;
		}

		return false;
	}

	function getPickupdateOrderItems($pickupdate_id) {

		$query = new Query();
		
		$user_id = session()->value("user_id");
		
		$sql = "SELECT DISTINCT order_items.* 
		FROM ".$this->db_pickupdates." AS pickupdates, "
		.$this->db_pickupdate_order_items." AS pickupdate_order_items, "
		.$this->db_order_items." AS order_items, "
		.$this->db_orders." AS orders 
		WHERE pickupdate_order_items.pickupdate_id = $pickupdate_id
		AND pickupdate_order_items.order_item_id = order_items.id 
		AND order_items.order_id = orders.id
		AND orders.user_id = $user_id";

		if($query->sql($sql)) {

			$pickupdate_order_items = $query->results();

			return $pickupdate_order_items;
		}

		return false;
	}

	

}

?>