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

						$tally = $TC->getTally(["department_id" => $department["id"]]);

						if($tally) {

							$TC->addRegisteredCashPayment($tally["id"], $payment_id);
						}
						
					}

					$page->addLog("SuperShop->addPayment: order_id:$order_id, payment_method_id:$payment_method_id, payment_amount:$payment_amount");

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
	 * Remove cart items that belongs to a past pickupdate
	 *
	 * Run by cron job
	 * 
	 * @return boolean
	 */
	function removePastPickupdateCartItems($action) {

		if(count($action) == 1) {

			$query = new Query();

			$sql = "DELETE cart_items 
			FROM ".$this->db_cart_items." AS cart_items
			JOIN ".$this->db_pickupdate_cart_items." AS pickupdate_cart_items 
			ON cart_items.id = pickupdate_cart_items.cart_item_id 
			JOIN ".$this->db_pickupdates." AS pickupdates 
			ON pickupdates.id = pickupdate_cart_items.pickupdate_id
			WHERE pickupdates.pickupdate < CURDATE()"; 

			if($query->sql($sql)) {

				return true;
			}

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

		if(count($action == 1)) {

			$query = new Query();


			// get pickupdates that are less than a week from now
			$pickupdates = [];

			foreach ($pickupdates as $pickupdate) {

				$order_items = $this->getPickupdateOrderItems($pickupdate["id"]);

				foreach ($order_items as $order_item) {

					$order = $this->getOrders(["id" => $order_item["order_id"]]);
					
					$this->cancelOrder(["cancelOrder", $order["id"], $order["user_id"]]);
				}
				
				message()->resetMessages();
			}

			return true;
		}

		return false;
	}

}

?>