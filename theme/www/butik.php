<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();
$model = new Shop();


$page->bodyClass("shop");
$page->pageTitle("Shop");


if(is_array($action) && count($action)) {

	// /shop/receipt
	if($action[0] == "receipt") {


		if(count($action) == 3 && $action[2] == "error") {

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

	

}

// plain signup directly
// /signup
$page->page(array(
	"templates" => "shop/cart.php"
));

?>
