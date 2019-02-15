<?php
global $action;
global $model;
$UC = new User();


$order = false;
$receipt_type = false;

$is_membership = false;
$subscription_method = false;
$payment_date = false;

$active_account = false;


// get current user
$user_id = session()->value("user_id");
$user = $UC->getUser();
// has account been activated
if($user) {
	$active_account = $user["status"];
}


// order no indicated in url
if(isset($action[1])) {
	// get order from order no
	$order_no = $action[1];
	if($order_no) {
		$order = $model->getOrders(array("order_no" => $order_no));


		// get potential user membership
		$membership = $UC->getMembership();

		// get order prices
		if($order) {
			$total_order_price = $model->getTotalOrderPrice($order["id"]);
			$remaining_order_price = $model->getRemainingOrderPrice($order["id"]);

			// is the users membership related to this order?
			if($membership && $membership["order"]) {
				
				$is_membership = ($membership["order"] && $order["id"] == $membership["order"]["id"]) ? true : false;
			}
			// does membership have a duration of subscription?
			if($membership && $membership["item"] && $membership["item"]["subscription_method"] && $membership["item"]["subscription_method"]["duration"]) {
				$subscription_method = $membership["item"]["subscription_method"];
				$payment_date = $membership["renewed_at"] ? date("jS", strtotime($membership["renewed_at"])) : date("jS", strtotime($membership["created_at"]));
			}

		}

	}

}

// receipt type indicated in url
if(isset($action[2])) {
	$receipt_type = $action[2];
}



?>
<div class="scene shopReceipt i:scene">

<? if($order): ?>

	<h1>Tak for din ordre!</h1>


<?	if($receipt_type == "cash"): ?>


	<h2>Betaling med kontant.</h2>
	<p>Husk at tage <?= formatPrice($remaining_order_price) ?> med i kontanter næste gang.</p>


<?	endif; ?>


<? else: ?>


	<h2>Din ordre er gået igennem!</h2>


<? endif; ?>


<? // membership is related to order
	if($is_membership): ?>
	<p>Tillykke med, at du nu er en del af Københavns Fødevarefællesskab! </p>
<? endif; ?>


<? // account has not been activated
	if(!$active_account): ?>
	<p>Husk at aktivere din konto. Tjek din email for aktiveringskoden.</p>
<? endif; ?>


</div>
