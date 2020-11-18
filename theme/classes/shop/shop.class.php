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

		$this->addToModel("quantity", array(
			"type" => "integer",
			"label" => "Quantity",
			"min" => 1,
			"required" => true,
			"hint_message" => "Quantity of items.", 
			"error_message" => "Quantity must be a number."
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

				$custom_name = $this->getProperty("custom_name", "value");
				$custom_price = $this->getProperty("custom_price", "value");
				$quantity = $this->getProperty("quantity", "value");
				$item_id = $this->getProperty("item_id", "value");
				$pickupdate_id = getPost("pickupdate_id", "value");
				$item = $IC->getItem(array("id" => $item_id));
				$price = $this->getPrice($item_id);

				
				// are there any items in cart already?
				if($cart["items"]) {

					// what kind of itemtype is being added
					// if it is a membership, then remove existing memberships from cart
					if($item["itemtype"] == "signupfee") {

						foreach($cart["items"] as $cart_item) {
							$existing_item = $IC->getItem(array("id" => $cart_item["item_id"]));
							if($existing_item["itemtype"] == "signupfee") {
								$cart = $this->deleteFromCart(array("deleteFromCart", $cart_reference, $cart_item["id"]));
							}
						}
					}
				}

				// item has a price (price can be zero)
				if ($price !== false) {
					
					// look in cart to see if the added item is already there
					// if added item already exists with a different custom_name or custom_price, create new line
					if ($custom_price !== false && $custom_name) {

						$existing_cart_item = $this->getCartItem($cart_reference, $item_id, ["custom_price" => $custom_price, "custom_name" => $custom_name]);
					}
					else if($custom_price !== false) {

						$existing_cart_item = $this->getCartItem($cart_reference, $item_id, ["custom_price" => $custom_price]);
					}
					else if($custom_name) {
						
						$existing_cart_item = $this->getCartItem($cart_reference, $item_id, ["custom_name" => $custom_name]);
					}
					else {
						
						$existing_cart_item = $this->getCartItem($cart_reference, $item_id);
					}

					if($existing_cart_item) {
						
						// check if same item_id with same pickupdate is already in cart
						$existing_cart_item = $this->getExistingCartItem($cart["id"], $item_id, $pickupdate_id);
					}
					

					// added item is already in cart
					if($existing_cart_item) {
						
						$existing_quantity = $existing_cart_item["quantity"];
						$new_quantity = intval($quantity) + intval($existing_quantity);
	
						// update item quantity
						$sql = "UPDATE ".$this->db_cart_items." SET quantity=$new_quantity WHERE id = ".$existing_cart_item["id"]." AND cart_id = ".$cart["id"];
	//					print $sql;
					}
					else {
						
						// insert new cart item
						$sql = "INSERT INTO ".$this->db_cart_items." SET cart_id=".$cart["id"].", item_id=$item_id, quantity=$quantity";

						if($custom_price !== false) {

							// use correct decimal seperator
							$custom_price = preg_replace("/,/", ".", $custom_price);

							$sql .= ", custom_price=$custom_price";
						}
						if($custom_name) {
							$sql .= ", custom_name='".$custom_name."'";
						}
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

	function getCartPickupdates($_options = false) {
		
		$cart = $this->getCart();
		
		if($cart && $cart["items"]) {

			$query = new Query();
			$query->checkDbExistence($this->db_pickupdates);
			$query->checkDbExistence($this->db_pickupdate_cart_items);

			$cart_id = $cart["id"];

			$sql = "SELECT DISTINCT pickupdates.* FROM ".$this->db_pickupdates." AS pickupdates, ".$this->db_pickupdate_cart_items." AS pickupdate_cart_items, ".$this->db_cart_items." AS cart_items WHERE cart_items.cart_id = $cart_id AND cart_items.id = pickupdate_cart_items.cart_item_id AND pickupdates.id = pickupdate_cart_items.pickupdate_id ORDER BY pickupdates.pickupdate ASC";
			if($query->sql($sql)) {
	
				$cart_pickupdates = $query->results();
	
				return $cart_pickupdates;
			}
		}


		return false;
	}
	
	function getCartPickupdateItems($pickupdate_id, $_options = false) {

		$query = new Query();
		$cart = $this->getCart();

		if($cart && $cart["items"]) {

			$sql = "SELECT cart_items.* FROM ".$this->db_pickupdate_cart_items." AS pickupdate_cart_items, ".$this->db_cart_items." AS cart_items WHERE pickupdate_cart_items.pickupdate_id = $pickupdate_id AND cart_items.id = pickupdate_cart_items.cart_item_id AND cart_items.cart_id = ".$cart["id"];
			if($query->sql($sql)) {
				
				$cart_pickupdate_items = $query->results();
				
				return $cart_pickupdate_items;
				
			}
		}


		
		return false;
	}

	function getCartItemsWithoutPickupdate($_options = false) {

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

			return true;
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

	function getPickupdates($_options = false) {
		
		$query = new Query();
		$query->checkDbExistence($this->db_pickupdates);

		$after = false;
		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {
					case "after"          : $after               = $_value; break;
				}
			}
		}

		$sql = "SELECT pickupdates.* 
		FROM ".$this->db_pickupdates." AS pickupdates";

		if($after) {
			$sql .= " WHERE pickupdates.pickupdate >= '$after'";
		}

		$sql .= " ORDER BY pickupdates.pickupdate ASC";

		if($query->sql($sql)) {

			$pickupdates = $query->results();

			return $pickupdates;
		}

		return false;
	}
	
	function getOrderPickupdates($order_id) {
		
		$query = new Query();
		$query->checkDbExistence($this->db_pickupdate_order_items);

		$sql = "SELECT DISTINCT pickupdates.* 
		FROM ".$this->db_pickupdates." AS pickupdates, "
		.$this->db_pickupdate_order_items." AS pickupdate_order_items, "
		.$this->db_order_items." AS order_items, "
		.$this->db_orders." AS orders 
		WHERE orders.id = $order_id 
		AND order_items.order_id = orders.id
		AND pickupdate_order_items.order_item_id = order_items.id 
		AND pickupdates.id = pickupdate_order_items.pickupdate_id";

		if($query->sql($sql)) {

			$order_pickupdates = $query->results();

			return $order_pickupdates;
		}

		return false;
	}

	function getOrderItemPickupdate($order_item_id) {
		
		$query = new Query();
		$query->checkDbExistence($this->db_pickupdate_order_items);

		$sql = "SELECT DISTINCT pickupdates.* 
		FROM ".$this->db_pickupdates." AS pickupdates, "
		.$this->db_pickupdate_order_items." AS pickupdate_order_items
		WHERE pickupdate_order_items.order_item_id = $order_item_id
		AND pickupdate_order_items.pickupdate_id = pickupdates.id 
		LIMIT 1";

		if($query->sql($sql)) {

			$order_item_pickupdate = $query->result(0);

			return $order_item_pickupdate;
		}

		return false;
	}

	function setOrderItemPickupdate($order_item_id, $pickupdate_id) {
		
		$query = new Query();
		$query->checkDbExistence($this->db_pickupdate_order_items);

		if($this->getOrderItemPickupdate($order_item_id)) {

			$sql = "UPDATE ".$this->db_pickupdate_order_items." SET pickupdate_id = $pickupdate_id WHERE order_item_id = $order_item_id";
		}
		else {

			$sql = "INSERT INTO ".$this->db_pickupdate_order_items." SET pickupdate_id = $pickupdate_id, order_item_id = $order_item_id";
		}

		if($query->sql($sql)) {

			return $this->getOrderItemPickupdate($order_item_id);
		}

		return false;
	}

	function getOrderItem($order_item_id) {

		$query = new Query();

		$sql = "SELECT * FROM ".$this->db_order_items." WHERE id = $order_item_id";

		if($query->sql($sql)) {

			$order_item = $query->result(0);

			return $order_item;
		}

		return false;
	}

	/**
	 * updateOrderItemDetails
	 * /butik/updateOrderItemDetails/#order_item_id#
	 * 
	 * Change pickupdate
	 * Change pickup place (not implemented)
	 *
	 * @param array $action
	 * @return boolean
	 */
	function updateOrderItemDetails($action) {

		if(count($action) == 2) {

			$this->getPostedEntities();

			$user_id = session()->value("user_id");
			$order_item_id = $action[1];

			$order_item = $this->getOrderItem($order_item_id);

			if($order_item) {

				$pickupdate_id = getPost("pickupdate_id", "value");
				
				if($pickupdate_id) {
					$this->setOrderItemPickupdate($order_item_id, $pickupdate_id);
				}
				
				return true;
			}
		}
		
		return false;
	}

	function getOrderItemsPickupdates($user_id, $_options = false) {
		
		$query = new Query();
		$query->checkDbExistence($this->db_pickupdate_order_items);

		$after = false;
		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {
					case "after"          : $after               = $_value; break;
				}
			}
		}

		$sql = "SELECT DISTINCT pickupdates.* 
		FROM ".$this->db_pickupdates." AS pickupdates, "
		.$this->db_pickupdate_order_items." AS pickupdate_order_items, "
		.$this->db_order_items." AS order_items, "
		.$this->db_orders." AS orders 
		WHERE orders.user_id = $user_id 
		AND order_items.order_id = orders.id
		AND orders.status < 3
		AND pickupdate_order_items.order_item_id = order_items.id 
		AND pickupdates.id = pickupdate_order_items.pickupdate_id";

		if($after) {
			$sql .= " AND pickupdates.pickupdate >= '$after'";
		}

		$sql .= " ORDER BY pickupdates.pickupdate ASC";

		if($query->sql($sql)) {

			$order_items_pickupdates = $query->results();

			return $order_items_pickupdates;
		}

		return false;
	}

	function getOrderItemsWithoutPickupdate($_options = false) {

		$query = new Query();

		$order_id = false;
		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {
					case "order_id"          : $order_id               = $_value; break;
				}
			}
		}


		$sql = "SELECT order_items.* 
		FROM ".$this->db_order_items." AS order_items
		WHERE order_items.id NOT IN (
			SELECT pickupdate_order_items.order_item_id 
			FROM ".$this->db_pickupdate_order_items." AS pickupdate_order_items 
			)";

		if($order_id) {
			$sql .= " AND order_items.order_id = $order_id";
		}

		if($query->sql($sql)) {

			$order_items_without_pickupdate = $query->results();

			return $order_items_without_pickupdate;
		}
		

		return false;
	}

	function getPickupdateOrderItems($pickupdate_id, $_options = false) {

		$query = new Query();
		
		$order_id = false;
		$user_id = false;
		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {
					case "order_id"          : $order_id               = $_value; break;
					case "user_id"           : $user_id                = $_value; break;
				}
			}
		}

		$sql = "SELECT DISTINCT order_items.* 
		FROM ".$this->db_pickupdates." AS pickupdates, "
		.$this->db_pickupdate_order_items." AS pickupdate_order_items, "
		.$this->db_order_items." AS order_items, "
		.$this->db_orders." AS orders 
		WHERE pickupdate_order_items.pickupdate_id = $pickupdate_id
		AND pickupdate_order_items.order_item_id = order_items.id
		AND order_items.order_id = orders.id
		AND orders.status < 3"; 

		if($order_id) {
			$sql .= " AND orders.id = $order_id";
		}
		else if($user_id) {
			$sql .= " AND orders.user_id = $user_id";
		}

		if($query->sql($sql)) {

			$pickupdate_order_items = $query->results();

			return $pickupdate_order_items;
		}

		return false;
	}

	function addCartItemToOrder($cart_item, $order) {
		
		if($cart_item && $order) {

			$query = new Query();
			$IC = new Items();
	
			$quantity = $cart_item["quantity"];
			$item_id = $cart_item["item_id"];
	
			// get item details
			$item = $IC->getItem(["id" => $item_id, "extend" => true]);
	
			if($item) {
	
				// get best price for item
				$price = $this->getPrice($item_id, array("quantity" => $quantity, "currency" => $order["currency"], "country" => $order["country"]));
				// print_r("price: ".$price);
	
				// use custom price if available
				if(isset($cart_item["custom_price"]) && $cart_item["custom_price"] !== false) {
					$custom_price = $cart_item["custom_price"];
					
					$price["price"] = $custom_price;
					$custom_price_without_vat = $custom_price / (100 + $price["vatrate"]) * 100;
					$price["price_without_vat"] = $custom_price_without_vat;
					$price["vat"] = $custom_price - $custom_price_without_vat;
				}
	
				$unit_price = $price["price"];
				$unit_vat = $price["vat"];
				$total_price = $unit_price * $quantity;
				$total_vat = $unit_vat * $quantity;
	
				// use custom name for cart item if available
				$item_name = isset($cart_item["custom_name"]) ? $cart_item["custom_name"] : $item["name"];
	
				$sql = "INSERT INTO ".$this->db_order_items." SET order_id=".$order["id"].", item_id=$item_id, name='".prepareForDB($item_name)."', quantity=$quantity, unit_price=$unit_price, unit_vat=$unit_vat, total_price=$total_price, total_vat=$total_vat";
				// print $sql;
	
	
				// Add item to order
				if($query->sql($sql)) {
					$order_item_id = $query->lastInsertId();
					
					// get order_item
					$sql = "SELECT * FROM ".$this->db_order_items." WHERE id = $order_item_id";
					if($query->sql($sql)) {
	
						$order_item = $query->result(0);
	
						$order_item["custom_price"] = isset($custom_price) ? $custom_price : null;
						$order_item["item_name"] = $item_name;
	
						// add cart_item's pickupdate to order_item
						$pickupdate = $this->getCartItemPickupdate($cart_item["id"]);
						if($pickupdate) {
							$this->addPickupdateOrderItem($pickupdate["id"], $order_item_id);
						}
	
						return $order_item;
						
					}
					
				}
	
			}
		}

		return false;
	}

	// cancel order (adapter to SuperShop::cancelOrder)
	// changes order status to cancelled
	// cancels any subscriptions or memberships included in order
	# /#controller#/cancelOrder/#order_id#
	function cancelOrder($action) {

		include_once("classes/shop/supershop.class.php");
		$SC = new SuperShop;

		$user_id = session()->value("user_id");
		$order_id = $action[1];

		return $SC->cancelOrder(["cancelOrder", $order_id, $user_id]);
		
	}

	function hasSignupfeeInCart($cart_id) {

		$query = new Query();

		$sql = "SELECT cart_items.* 
		FROM "
		.$this->db_cart_items." AS cart_items, "
		.SITE_DB.".items AS items 
		WHERE cart_items.cart_id = $cart_id
		AND cart_items.item_id = items.id
		AND items.itemtype = 'signupfee'";

		if($query->sql($sql)) {

			return true;
		}

		return false;
	}

	function hasSignupfeeInOrder($order_id) {

		$order = $this->getOrders(["order_id" => $order_id]);

		if($order) {

			$IC = new Items();

			foreach ($order["items"] as $order_item) {

				$item = $IC->getItem(["id" => $order_item["item_id"], "extend" => true]);
				
				if($item["itemtype"] == "signupfee") {

					return true;
				}
			}

		}

		return false;

	}

	

}

?>