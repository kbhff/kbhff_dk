<?php
global $action;
global $model;
$UC = new User();


$order = false;

$is_membership = false;
$subscription_method = false;
$payment_date = false;

$active_account = false;


// get current user id
$user_id = session()->value("user_id");
$user = $UC->getUser();
// has account been activated
if($user) {
	$active_account = $user["status"];
}


// order no indicated in url
if(count($action) == 4) {

	$order_no = $action[1];
	$payment_id = $action[3];

	if($order_no) {
		$order = $model->getOrders(array("order_no" => $order_no));


		// get potential user membership
		$membership = $UC->getMembership();


		if($order) {

			$payment_id = $action[3];
			$payment = $model->getPayments(["payment_id" => $payment_id]);

			if($membership && $membership["order"]) {
				// is the users membership related to this order?
				$is_membership = ($membership["order"] && $order["id"] == $membership["order"]["id"]) ? true : false;
			}

			if($membership && $membership["item"] && $membership["item"]["subscription_method"] && $membership["item"]["subscription_method"]["duration"]) {
				$subscription_method = $membership["item"]["subscription_method"];
				$payment_date = $membership["renewed_at"] ? date("jS", strtotime($membership["renewed_at"])) : date("jS", strtotime($membership["created_at"]));
			}

		}

	}

}


?>
<div class="scene shopReceipt i:scene">

<? if($order): ?>

	<h1>Thank you for supporting change.</h1>

	<h2>Your payment of <?= formatPrice(["price" => $payment["payment_amount"], "currency" => $payment["currency"]]) ?> has been processed successfully.</h2>

<? endif; ?>


<? if($is_membership): ?>
	<p>We are thrilled to have you on board - go ahead and check out our <a href="/events">upcoming events</a>!</p>
<? endif; ?>


<? if(!$active_account): ?>
	<p>Remember to activate your account, otherwise you won't get our newsletter. Check your inbox for the Activation email.</p>
<? endif; ?>


</div>