<?php
/**
* @package janitor.shop
* Meant to allow local shop additions/overrides
*/

include_once("classes/shop/supershop.core.class.php");


class SuperShop extends SuperShopCore {

	/**
	*
	*/
	function __construct() {

		parent::__construct(get_class());

	}

	// register manual payment
	// also updates order state
	# /#controller#/registerPayment
	function registerPayment($action) {

		// Get posted values to make them available for models
		$this->getPostedEntities();

		if(count($action) == 1 && $this->validateList(array("payment_amount", "payment_method_id", "order_id", "transaction_id"))) {


			$order_id = $this->getProperty("order_id", "value");
			$transaction_id = $this->getProperty("transaction_id", "value");
			$payment_amount = $this->getProperty("payment_amount", "value");
			$payment_method_id = $this->getProperty("payment_method_id", "value");
			$receiving_user_id = $this->getProperty("receiving_user_id", "value");

			$order = $this->getOrders(array("order_id" => $order_id));

			if($order) {

				$query = new Query();

				$sql = "INSERT INTO ".$this->db_payments." SET order_id=$order_id, currency='".$order["currency"]."', payment_amount=$payment_amount, transaction_id='$transaction_id', payment_method_id=$payment_method_id";
				if($query->sql($sql)) {
					$payment_id = $query->lastInsertId();
					$this->validateOrder($order["id"]);

					global $page;

					$payment_method = $page->paymentMethods($payment_method_id);

					if($payment_method && $payment_method["name"] == "Cash") {

						$UC = new SuperUser();
						$department = $UC->getUserDepartment(["user_id" => $receiving_user_id]);
						
						include_once("classes/shop/tally.class.php");
						$TC = new Tally();

						$tally = $TC->getTally(["department_id" => $department["id"], "create" => true]);

						if($tally) {

							$TC->addRegisteredCashPayment($tally["id"], $payment_id);
						}
						
					}

					logger()->addLog("SuperShop->addPayment: order_id:$order_id, payment_method_id:$payment_method_id, payment_amount:$payment_amount");

					message()->addMessage("Payment added");
					return $payment_id;
				
				}
			}

		}
		message()->addMessage("Payment could not be added", array("type" => "error"));
		return false;
	}

	function getRegisteredCashOrder($payment_id) {

		$query = new Query();

		$sql = "SELECT * FROM ".$this->db_payments." as payments, ".$this->db_orders." as orders WHERE payments.order_id = orders.id AND payments.id = $payment_id";

		// print $sql."<br>\n";
		if($query->sql($sql)) {
			return $query->results();
		}

		return false;
	}

	function getMobilepayLink($amount, $mobilepay_id, $comment) {

		$mobilepay_link = "https://www.mobilepay.dk/erhverv/betalingslink/betalingslink-svar?"
			.$this->getPhonenumberText($mobilepay_id)
			.$this->getAmountText($amount)
			.$this->getCommentText($comment)
			.$this->getLockText(true);

		return $mobilepay_link;
	}

	private static function getPhonenumberText($phonenumber){
        if(!(is_string($phonenumber) && preg_match("/^[0-9]+$/", $phonenumber) === 1)){
            throw new InvalidArgumentException("Phone number should be a string containing only numbers");
        }

        return sprintf("phone=%s", $phonenumber);
    }

    private static function getAmountText($amount){
        if(is_null($amount))
            return "";
        elseif ($amount < 0)
            throw new InvalidArgumentException("Amount should be positive");
        //Mobilepay's QR code generator doesn't include a decimal point for integer amounts
        elseif (is_integer($amount))
            return sprintf("&amount=%d", $amount);
        else
            return sprintf("&amount=%.2f", $amount);
    }

    private static function getCommentText($comment){
        if(strlen($comment) > 25)
            throw new InvalidArgumentException("Comment must be at most 25 characters long");

        if($comment === "")
            return "";
        else
            return sprintf("&comment=%s", rawurlencode($comment));
    }

    private static function getLockText($lockCommentField){
        if($lockCommentField)
            return "&lock=1";
        else
            return "";
	}
	

	/**
	 * Remove cart items that have exceeded the ordering deadline
	 *
	 * Run by cron job
	 * 
	 * @return boolean
	 */
	function removeExceededDeadlineCartItems($action) {

		if(count($action) == 1) {

			$query = new Query();

			include_once("classes/shop/pickupdate.class.php");
			$PC = new Pickupdate();

			// get pickupdate 6 days from now, if it exists
			// 6 days because cart_items will only be removed when the deadline day has passed completely
			$pickupdate = $PC->getPickupdate(["pickupdate" => date("Y-m-d", strtotime("+6 days"))]);
			debug(["pickupdate", $pickupdate]);

			if($pickupdate) {

				$pickupdate_cart_items = $this->getPickupdateCartItems($pickupdate["id"]);
				debug(["pickupdate_cart_items", $pickupdate_cart_items]);
				foreach($pickupdate_cart_items as $cart_item) {

					$test = $this->deleteFromCart(["deleteFromCart", $cart_item["cart_reference"], $cart_item["id"]]);
					debug(["test delete", $test]);

				}

				return true;

			}

		}

		return false;
	}

	function getCartPickupdates($_options = false) {

		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {
					case "cart_reference"             : $cart_reference                  = $_value; break;
				}
			}
		}
		
		$cart = $this->getCarts(["cart_reference" => $cart_reference]);
		
		if($cart && $cart["items"]) {

			$query = new Query();
			$query->checkDbExistence($this->db_pickupdates);
			$query->checkDbExistence($this->db_department_pickupdate_cart_items);
			$query->checkDbExistence($this->db_department_pickupdates);


			$cart_id = $cart["id"];

			$sql = "
			SELECT 
				DISTINCT pickupdates.* 
			FROM ".$this->db_pickupdates." AS pickupdates, "
				.$this->db_department_pickupdate_cart_items." AS department_pickupdate_cart_items, "
				.$this->db_department_pickupdates." AS department_pickupdates, "
				.$this->db_cart_items." AS cart_items 
			WHERE cart_items.cart_id = $cart_id 
				AND cart_items.id = department_pickupdate_cart_items.cart_item_id 
				AND department_pickupdates.id = department_pickupdate_cart_items.department_pickupdate_id 
				AND pickupdates.id = department_pickupdates.pickupdate_id  
			ORDER BY pickupdates.pickupdate ASC";

			if($query->sql($sql)) {
	
				$cart_pickupdates = $query->results();
	
				return $cart_pickupdates;
			}
		}


		return false;
	}
	
	function getCartItemsWithoutPickupdate($_options = false) {

		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {
					case "cart_reference"             : $cart_reference                  = $_value; break;
				}
			}
		}

		$query = new Query();
		$cart = $this->getCarts(["cart_reference" => $cart_reference]);
		$cart_id = $cart["id"];

		if($cart && $cart["items"]) {

			$sql = "SELECT cart_items.* 
			FROM ".$this->db_cart_items." AS cart_items
			WHERE cart_items.id NOT IN (
				SELECT department_pickupdate_cart_items.cart_item_id 
				FROM ".$this->db_department_pickupdate_cart_items." AS department_pickupdate_cart_items 
				) 
			AND cart_items.cart_id = $cart_id";

			if($query->sql($sql)) {

				$cart_items_without_pickupdate = $query->results();

				return $cart_items_without_pickupdate;
			}
		}

		return false;
	}

	// Add item to cart
	# /janitor/admin/shop/addToCart/#cart_reference#/
	// Items and quantity in $_post
	
	/**
	 * ### Add item to cart
	 * Custom kbhff version also calls getExistingCartItem() to account for pickupdates
	 * 
	 * /janitor/admin/shop/addToCart/#cart_reference#/
	 *
	 * Values in $_POST
	 * - item_id (required)
	 * - quantity (required)
	 * - custom_price
	 * - custom_name
	 * 
	 * @param array $action
	 * @return array|false Cart object. False on error. 
	 */
	function addToCart($action) {

		if(count($action) > 1) {

			$cart_reference = $action[1];
			
			// get cart
			$cart = $this->getCarts(array("cart_reference" => $cart_reference));
			// print_r($cart);

			// get posted values to make them available for models
			$this->getPostedEntities();

			// cart exists and values are valid
			if($cart && $this->validateList(array("quantity", "item_id"))) {

				$query = new Query();
				$IC = new Items();

				include_once("classes/users/superuser.class.php");
				$UC = new SuperUser();

				$custom_name = $this->getProperty("custom_name", "value");
				$custom_price = $this->getProperty("custom_price", "value");
				$quantity = $this->getProperty("quantity", "value");
				$item_id = $this->getProperty("item_id", "value");
				$pickupdate_id = getPost("pickupdate_id", "value");
				$price = $this->getPrice($item_id, ["user_id" => $cart["user_id"]]);

				$item = $IC->getItem(array("id" => $item_id));

				$department = $UC->getUserDepartment(["user_id" => $cart["user_id"]]);
				$department_id = $department ? $department["id"] : false;

				// are there any items in cart already?
				if($cart["items"]) {

					// what kind of itemtype is being added
					// if it is a membership, then remove existing memberships from cart
					if($item["itemtype"] == "signupfee") {

						foreach($cart["items"] as $cart_item) {
							$existing_cart_item = $IC->getItem(array("id" => $cart_item["item_id"]));
							if($existing_cart_item["itemtype"] == "signupfee") {
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
						$existing_cart_item = $this->getExistingCartItem($cart["id"], $item_id, $department_id, $pickupdate_id);
					}
					

					// added item is already in cart
					if($existing_cart_item) {
						
						$existing_quantity = $existing_cart_item["quantity"];
						$new_quantity = intval($quantity) + intval($existing_quantity);
	
						// update item quantity
						$sql = "UPDATE ".$this->db_cart_items." SET quantity=$new_quantity WHERE id = ".$existing_cart_item["id"]." AND cart_id = ".$cart["id"];
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
								$this->addDepartmentPickupdateCartItem($department_id, $pickupdate_id, $cart_item_id);
							}
						}
	
						// update modified at time
						$sql = "UPDATE ".$this->db_carts." SET modified_at=CURRENT_TIMESTAMP WHERE id = ".$cart["id"];
						$query->sql($sql);
	
						$cart = $this->getCarts(array("cart_id" => $cart["id"]));
	
						// add callback to addedToCart
						$model = $IC->typeObject($item["itemtype"]);
						if(method_exists($model, "addedToCart")) {
							$model->addedToCart($item, $cart);
						}
	
						message()->addMessage("Item added to cart");
						return $cart;
	
					}
				}
			}
		}

		message()->addMessage("Item could not be added to cart", array("type" => "error"));
		return false;
	}

	function addCartItemToOrder($cart_item, $order) {
		
		if($cart_item && $order) {

			$query = new Query();
			$IC = new Items();

			include_once("classes/users/superuser.class.php");
			$UC = new SuperUser();
	
			$quantity = $cart_item["quantity"];
			$item_id = $cart_item["item_id"];
			$cart = $this->getCarts(["cart_id" => $cart_item["cart_id"]]);
			$user_id = $cart ? $cart["user_id"] : false;
			$user_department = $UC->getUserDepartment(["user_id" => $user_id]);
	
			// get item details
			$item = $IC->getItem(["id" => $item_id, "extend" => true]);
	
			if($item) {
	
				// get best price for item
				$price = $this->getPrice($item_id, array("user_id" => $user_id, "quantity" => $quantity, "currency" => $order["currency"], "country" => $order["country"]));
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
						$cart_item_department_pickupdate = $this->getCartItemDepartmentPickupdate($cart_item["id"]);
						if($cart_item_department_pickupdate && $user_department["id"] === $cart_item_department_pickupdate["department_id"]) {
							$this->addDepartmentPickupdateOrderItem($user_department["id"], $cart_item_department_pickupdate["pickupdate_id"], $order_item_id);
						}
	
						return $order_item;
						
					}
					
				}
	
			}
		}

		return false;
	}
	
	function getOrderContentString($order_items) {
		
		$order_content = "";
		foreach ($order_items as $order_item) {

			$IC = new Items();

			$item = $IC->getItem(["id" => $order_item["item_id"], "extend" => true]);

			if($item["itemtype"] == "membership") {
				$da_itemtype = " (medlemskab)";
			}
			else if($item["itemtype"] == "signupfee") {
				$da_itemtype = " (indmeldelsesgebyr)";
			}
			else {
				$da_itemtype = "";
			}

			$order_content .= $order_item["quantity"]." x ".$order_item["name"].$da_itemtype."<br />";

		}

		return $order_content;
	}

	function sendOrderMails($order) {

		include_once("classes/users/superuser.class.php");
		$UC = new SuperUser();

		$order_no = $order["order_no"];
		$user_id = $order["user_id"];
		$user = $UC->getKbhffUser(["user_id" => $user_id]);
		$total_order_price = $this->getTotalOrderPrice($order["id"]);

		$order_details = "";
		$order_items = $this->getOrderItems(["order_id" => $order["id"]]);
		if($order_items) {

			foreach ($order_items as $order_item) {
				
				$order_details .= $order_item["quantity"]." x ";
				$order_details .= $order_item["name"];

				$order_item_department_pickupdate = $this->getOrderItemDepartmentPickupdate($order_item["id"]);
				if($order_item_department_pickupdate) {

					$order_details .= " – Afhentes ".date("d.m.Y", strtotime($order_item_department_pickupdate["pickupdate"]))." (".$order_item_department_pickupdate["department"].")";
				}

				$order_details .= "<br>";

			}

		}
		
		// send notification email to admin
		mailer()->send(array(
			"recipients" => SHOP_ORDER_NOTIFIES,
			"subject" => SITE_URL . " - New order ($order_no) created on behalf of: $user_id",
			"message" => "Check out the new order: " . SITE_URL . "/janitor/admin/user/orders/" . $user_id,
			"tracking" => false
			// "template" => "system"
		));

		mailer()->send(array(
			"recipients" => $user["email"],
			"reply_to" => SHOP_ORDER_NOTIFIES,
			"values" => array(
				"NICKNAME" => $user["nickname"], 
				"ORDER_NO" => $order["order_no"], 
				"ORDER_ID" => $order["id"], 
				"ORDER_PRICE" => formatPrice($total_order_price),
				"ORDER_DETAILS" => $order_details
			),
			// "subject" => SITE_URL . " – Thank you for your order!",
			"tracking" => false,
			"template" => "order_confirmation"
		));
	}



	// CRON RELATED METHODS


	// #controller#/sendOrderCancellationWarnings
	/**
	* Send warning email about unpaid orders being cancelled soon
	*/
	function sendOrderCancellationWarnings($action) {

		if(count($action) >= 1) {

			global $page;

			include_once("classes/users/superuser.class.php");
			$UC = new SuperUser();

			include_once("classes/shop/supershop.class.php");
			$SC = new SuperShop();

			include_once("classes/shop/pickupdate.class.php");
			$PC = new Pickupdate();

			// get pickupdate 9 days from now, if it exists
			$pickupdate = $PC->getPickupdate(["pickupdate" => date("Y-m-d", strtotime("+9 days"))]);

			if($pickupdate) {
				
				$pickupdate_order_items = $this->getPickupdateOrderItems($pickupdate["id"]);
				if($pickupdate_order_items) {
					
					$pickupdate_day = (int)date("d", strtotime($pickupdate["pickupdate"]));
					$pickupdate_month = (int)date("m", strtotime($pickupdate["pickupdate"]));
					$pickupdate_year = (int)date("Y", strtotime($pickupdate["pickupdate"]));
					$deadline_day = (int)date("d", strtotime($pickupdate["pickupdate"]." - 1 week"));
					$deadline_month = (int)date("m", strtotime($pickupdate["pickupdate"]." - 1 week"));
					$deadline_year = (int)date("Y", strtotime($pickupdate["pickupdate"]." - 1 week"));

					$warned = [];
					
					foreach ($pickupdate_order_items as $pickupdate_order_item) {
						
						$order = $this->getOrders(["order_id" => $pickupdate_order_item["order_id"]]);
						
						// order is unpaid (or partially paid) and not cancelled and not already warned
						if($order && !in_array($order["id"], $warned) && $order["status"] < 3 && $order["payment_status"] < 2) {
							
							$user = $UC->getUsers(["user_id" => $order["user_id"]]);
							$username = $user ? $UC->getUsernames(["user_id" => $user["id"], "type" => "email"]) : false;
	
							$order_summary = [];
	
							foreach ($order["items"] as $order_item) {
								
								$order_item_department_pickupdate = $SC->getOrderItemDepartmentPickupdate($order_item["id"]);
								$order_item_pickupdate_day = (int)date("d", strtotime($order_item_department_pickupdate["pickupdate"]));
								$order_item_pickupdate_month = (int)date("m", strtotime($order_item_department_pickupdate["pickupdate"]));
								$order_item_pickupdate_year = (int)date("Y", strtotime($order_item_department_pickupdate["pickupdate"]));
								$order_summary[] = $order_item["quantity"]." x ".$order_item["name"]." (Afhentning d. ".$order_item_pickupdate_day."/".$order_item_pickupdate_month." ".$order_item_pickupdate_year." i afd. ".$order_item_department_pickupdate["department"].")";
							}
	
							$order_summary = implode("<br />", $order_summary);
	
							mailer()->send(array(
								"values" => array(
									"FROM" => ADMIN_EMAIL,
									"NICKNAME" => $user["nickname"],
									"DEADLINE" => $deadline_day."/".$deadline_month." ".$deadline_year,
									"PICKUPDATE" => $pickupdate_day."/".$pickupdate_month." ".$pickupdate_year,
									"ORDER_SUMMARY" => $order_summary,
									"ORDER_NO" => $order["order_no"]							),
								"recipients" => $username ? $username["username"] : false,
								"template" => "order_cancellation_warning",
								"track_clicks" => false
							));

							$warned[] = $order["id"];
	
							logger()->addLog("SuperShop->sendOrderCancellationWarnings: order cancellation warning sent to user_id:".$order["user_id"]);
						}
	
					}
					
					message()->resetMessages();
				}
			}

			return true;
		}

		return false;
	}

	/**
	 * Cancel orders that are unpaid on the deadline (1 week before the first coming pickup date)
	 * Run by cronjob
	 * 
	 * 
	 * @return void 
	 */
	function cancelUnpaidOrders($action) {

		if(count($action) >= 1) {

			global $page;

			include_once("classes/users/superuser.class.php");
			$UC = new SuperUser();

			include_once("classes/shop/supershop.class.php");
			$SC = new SuperShop();

			include_once("classes/shop/pickupdate.class.php");
			$PC = new Pickupdate();

			// get pickupdate 6 days from now, if it exists
			// 6 days because orders will only be cancelled when the deadline day has passed completely
			$pickupdate = $PC->getPickupdate(["pickupdate" => date("Y-m-d", strtotime("+6 days"))]);

			if($pickupdate) {

				$pickupdate_order_items = $this->getPickupdateOrderItems($pickupdate["id"]);
				if($pickupdate_order_items) {

					$pickupdate_day = (int)date("d", strtotime($pickupdate["pickupdate"]));
					$pickupdate_month = (int)date("m", strtotime($pickupdate["pickupdate"]));
					$pickupdate_year = (int)date("Y", strtotime($pickupdate["pickupdate"]));
					$deadline_day = (int)date("d", strtotime($pickupdate["pickupdate"]." - 1 week"));
					$deadline_month = (int)date("m", strtotime($pickupdate["pickupdate"]." - 1 week"));
					$deadline_year = (int)date("Y", strtotime($pickupdate["pickupdate"]." - 1 week"));
					
					foreach ($pickupdate_order_items as $pickupdate_order_item) {
						
						$order = $this->getOrders(["order_id" => $pickupdate_order_item["order_id"]]);
						
						// order is unpaid (or partially paid) and not cancelled
						if($order && $order["status"] < 3 && $order["payment_status"] < 2) {
							
							$user = $UC->getUsers(["user_id" => $order["user_id"]]);
							$username = $user ? $UC->getUsernames(["user_id" => $user["id"], "type" => "email"]) : false;
	
							$order_summary = [];
	
							foreach ($order["items"] as $order_item) {
								
								$order_item_department_pickupdate = $SC->getOrderItemDepartmentPickupdate($order_item["id"]);
								$order_item_pickupdate_day = (int)date("d", strtotime($order_item_department_pickupdate["pickupdate"]));
								$order_item_pickupdate_month = (int)date("m", strtotime($order_item_department_pickupdate["pickupdate"]));
								$order_item_pickupdate_year = (int)date("Y", strtotime($order_item_department_pickupdate["pickupdate"]));
								
								$order_summary[] = $order_item["quantity"]." x ".$order_item["name"]." (Afhentning d. ".$order_item_pickupdate_day."/".$order_item_pickupdate_month." ".$order_item_pickupdate_year." i afd. ".$order_item_department_pickupdate["department"].")";
							}
	
							$order_summary = implode("<br />", $order_summary);

							if($this->cancelOrder(["cancelOrder", $order["id"], $order["user_id"]])
							) {

								mailer()->send(array(
									"values" => array(
										"FROM" => ADMIN_EMAIL,
										"NICKNAME" => $user["nickname"],
										"PICKUPDATE" => $pickupdate_day."/".$pickupdate_month." ".$pickupdate_year,
										"DEADLINE" => $deadline_day."/".$deadline_month." ".$deadline_year,
										"ORDER_SUMMARY" => $order_summary,
										"ORDER_NO" => $order["order_no"]							),
									"recipients" => $username ? $username["username"] : false,
									"template" => "order_cancellation_notice",
									"track_clicks" => false
								));
	
								logger()->addLog("SuperShop->sendOrderCancellationNotices: order cancellation notice sent to user_id:".$order["user_id"]);
							}
	
						}
	
					}
					
					message()->resetMessages();
				}
			}

			return true;
		}

		return false;
	}


	// Cron method to clean up system before new renewal
	function cancelUnpaidRenewalOrdersFromLastYear() {

		$UC = new User();


		$query = new Query();
		// $sql = "SELECT o.id AS order_id, o.payment_status AS payment_status, oi.name AS order_item_name, o.user_id AS user_id FROM ".$UC->db." AS u, ".$this->db_orders." AS o, ".UT_ITEMS." AS i, ".$this->db_order_items." AS oi WHERE u.id = o.user_id AND u.status >= 0 AND o.payment_status = 0 AND o.id = oi.order_id AND i.id = oi.item_id AND (i.itemtype = 'membership' OR i.itemtype = 'signupfee') AND o.created_at < '".date("Y-m-d H:i:s", strtotime("- 1 day"))."' AND o.created_at > '".date("Y-m-d H:i:s", strtotime("- 1 year"))."'";

		// All time
		// $sql = "SELECT o.id AS order_id, o.payment_status AS payment_status, oi.name AS order_item_name, o.user_id AS user_id FROM ".$UC->db." AS u, ".$this->db_orders." AS o, ".UT_ITEMS." AS i, ".$this->db_order_items." AS oi WHERE u.id = o.user_id AND u.status >= 0 AND o.payment_status = 0 AND o.id = oi.order_id AND i.id = oi.item_id AND (i.itemtype = 'membership' OR i.itemtype = 'signupfee') AND o.created_at < '".date("Y-m-d H:i:s", strtotime("- 1 year"))."'";

		$sql = "SELECT o.id AS order_id, o.payment_status AS payment_status, oi.name AS order_item_name, o.user_id AS user_id FROM ".$UC->db." AS u, ".$this->db_orders." AS o, ".UT_ITEMS." AS i, ".$this->db_order_items." AS oi WHERE u.id = o.user_id AND u.status >= 0 AND o.payment_status = 0 AND (o.status = 0 OR o.status = 1) AND o.id = oi.order_id AND i.id = oi.item_id AND (i.itemtype = 'membership' OR i.itemtype = 'signupfee') AND o.created_at < '".date("Y-m-d H:i:s", strtotime("may 1st") > time() ? strtotime("may 1st - 1 year - 1 day") : strtotime("may 1st - 1 day"))."'";

		$query->sql($sql);

		$result = $query->results();


		print "cancelUnpaidRenewalOrdersFromLastYear\n<br>$sql\n<br>matches:".count($result)."\n<br>";
		debug([$result]);


		// Should be good, but disabled for cross referencing
		// print "cancelUnpaidRenewalOrdersFromLastYear\n$sql\nmatches:".count($result)."\n";

		// if($result) {
// 			foreach($result as $order) {
//
// 				// debug(["order", $order]);
//
// 				// Make sure order is not active in user subscription
// 				$sql = "SELECT * FROM ".$UC->db_subscriptions." AS us WHERE us.order_id = ".$order["order_id"];
// 				// debug([$sql]);
// 				$query->sql($sql);
// 				$result = $query->results();
// 				if(!$result) {
//
// 					print "deleting:".$order["order_id"].",";
//
// 					logger()->addLog("Automated task: cancelUnpaidRenewalOrdersFromLastYear: cancelled order_id:".$order["order_id"]);
//
// 					$cancelled = $this->cancelOrder(["cancelOrder", $order["order_id"], $order["user_id"]]);
// 					if($cancelled) {
// 						print " confirmed\n";
// 					}
// 					else {
// 						print " failed\n";
// 					}
//
// 				}
//
// 				break;
//
// 			}
//
// 		}

	}

}

?>