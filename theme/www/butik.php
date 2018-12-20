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

	// /shop/kvittering
	if($action[0] == "kvittering") {


		if(count($action) == 3 && $action[2] == "fejl") {

			$page->page(array(
				"templates" => "shop/receipt/error.php"
			));
			exit();

		}

		// if payment id exists (gateway payment receipt)
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

	# /butik/betaling/#order_no#
	else if($action[0] == "betaling" && count($action) == 2) {

		$page->page(array(
			"templates" => "shop/stripe.php"
		));
		exit();
	}


	// process payment
	else if(count($action) == 4 && $action[0] == "betaling" && $action[3] == "process" && $page->validateCsrfToken()) {

		$payment_id = $model->processOrderPayment($action);
		if($payment_id) {
			message()->resetMessages();
			// redirect to leave POST state
			header("Location: /butik/kvittering/".$action[1]."/".$action[2]."/".$payment_id);
			exit();

		}
		else {
			message()->resetMessages();
			message()->addMessage("Der skete en fejl i registreringen af betalingen.", array("type" => "error"));
			// redirect to leave POST state
			header("Location: /butik/kvittering/".$action[1]."/fejl");
			exit();

		}

	}


}

// plain signup directly
// /signup
$page->page(array(
	"templates" => "shop/cart.php"
));

?>
