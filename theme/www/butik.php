<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();
$model = new Shop();


$page->bodyClass("shop");
$page->pageTitle("Butik");


if(is_array($action) && count($action)) {
	
	//butik/betaling/#order_no# (submitted from bliv-medlem/bekraeft|spring-over)
	if($action[0] == "betaling" && count($action) == 2) {

		$page->page(array(
			"templates" => "shop/stripe.php"
		));
		exit();
	}
	
	// process payment
	// butik/betaling/#order_no#/stripe/process (submitted from butik/betaling/#order_no#)
	else if(count($action) == 4 && $action[0] == "betaling" && $action[3] == "process" && $page->validateCsrfToken()) {
		// process gateway data and create payment-id
		$payment_id = $model->processOrderPayment($action);
		// successful payment and creation of payment-id
		if($payment_id) {
			message()->resetMessages();
			// redirect to leave POST state
			header("Location: /butik/kvittering/".$action[1]."/".$action[2]."/".$payment_id);
			exit();

		}
		// Something went wrong
		else {
			message()->resetMessages();
			message()->addMessage("Der skete en fejl i registreringen af betalingen.", array("type" => "error"));
			// redirect to leave POST state
			header("Location: /butik/kvittering/".$action[1]."/fejl");
			exit();

		}

	}
	// /butik/kvittering (submitted from butik/betaling/#order_no#/stripe/process)
	else if($action[0] == "kvittering") {

		// butik/kvittering/#order_no#/fejl
		if(count($action) == 3 && $action[2] == "fejl") {

			$page->page(array(
				"templates" => "shop/receipt/error.php"
			));
			exit();

		}

		// if payment id exists and payment is processed successfully (gateway payment receipt)
		// butik/kvittering/#order_no#/#gateway#/#payment_id#
		else if(count($action) == 4) {

			$page->page(array(
				"templates" => "shop/receipt/".$action[2].".php"
			));
			exit();

		}
		// ALL OTHER VARIATIONS (THAN ERROR) ARE HANDLED IN RECEIPT TEMPLATE
		else {

			$page->page(array(
				"templates" => "shop/receipt/index.php"
			));
			exit();

		}

	}

}

// go to cart directly
// /butik
$page->page(array(
	"templates" => "shop/cart.php"
));

?>
