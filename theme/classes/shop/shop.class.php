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

		parent::__construct(get_class());


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


				// make sure only one membership exists in cart at any given time

				// is there any items in cart already?
				if($cart["items"]) {

					// what kind of itemtype is being added
					$item = $IC->getItem(array("id" => $item_id));

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



				// check if item is already in cart?
				if($cart["items"] && arrayKeyValue($cart["items"], "item_id", $item_id) !== false) {
					$existing_item_index = arrayKeyValue($cart["items"], "item_id", $item_id);


					$existing_item = $cart["items"][$existing_item_index];
					$existing_quantity = $existing_item["quantity"];
					$new_quantity = intval($quantity) + intval($existing_quantity);

					$sql = "UPDATE ".$this->db_cart_items." SET quantity=$new_quantity WHERE id = ".$existing_item["id"]." AND cart_id = ".$cart["id"];
	//					print $sql;
				}
				// insert new cart item
				else {

					$sql = "INSERT INTO ".$this->db_cart_items." SET cart_id=".$cart["id"].", item_id=$item_id, quantity=$quantity";
	//					print $sql;
				}

				if($query->sql($sql)) {

					// update modified at time
					$sql = "UPDATE ".$this->db_carts." SET modified_at=CURRENT_TIMESTAMP WHERE id = ".$cart["id"];
					$query->sql($sql);

					return $this->getCart();

				}
			}
		}
		return false;
	}
	
	function deleteSignupfeesAndMembershipsFromCart($cart_reference) {
		
		if($cart = $this->deleteItemtypeFromCart("signupfee", $_COOKIE["cart_reference"])) {
			
			if($cart = $this->deleteItemtypeFromCart("membership", $_COOKIE["cart_reference"])) {
				return $cart;
			}		
		}
		return false;	
	}
	
}

?>