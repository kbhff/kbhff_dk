<?php
global $action;
global $model;
$UC = new User();


$order = false;

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
if(count($action) == 4) {

	$order_no = $action[1];
	$payment_id = $action[3];

	if($order_no) {
		$order = $model->getOrders(array("order_no" => $order_no));

		include_once("classes/users/member.class.php");
		$MC = new Member();

		// get potential user membership
		$membership = $MC->getMembership();


		if($order) {

			$payment_id = $action[3];
			$payment = $model->getPayments(["payment_id" => $payment_id]);
			
			// is the users membership related to this order?
			if($membership && $membership["order"]) {
				$is_membership = ($membership["order"] && $order["id"] == $membership["order"]["id"]) ? true : false;
			}
			
			// does the membership have a duration of subscription?
			if($membership && $membership["item"] && $membership["item"]["subscription_method"] && $membership["item"]["subscription_method"]["duration"]) {
				$subscription_method = $membership["item"]["subscription_method"];
				$payment_date = $membership["renewed_at"] ? date("jS", strtotime($membership["renewed_at"])) : date("jS", strtotime($membership["created_at"]));
			}

		}

	}

}


?>
<div class="scene shopReceipt i:scene">
	<div class="article">
	<? if($order): ?>
	
		<h1>Tak for betalingen</h1>
		<div class="articlebody">
			<h2>Din betaling på <?= formatPrice(["price" => $payment["payment_amount"], "currency" => $payment["currency"]]) ?> er godkendt</h2>
		
		<? endif; ?>
		
		
		<? if($is_membership): ?>
			<p>Dit medlemskab er oprettet, og du kan bestille dine grøntsager.</p>
		<? endif; ?>
		
		
		<? if(!$active_account): ?>
			<p>Husk at aktivere din konto ved at verificere din email. Ellers kan du ikke bestille grøntsager. Kig i din indbakke efter aktiveringsemailen.</p>
		<? else: ?>
			<ul class="actions">
				<li><a class="button primary clickable" href="/">Log ind</a></li>
			</ul>
		<? endif; ?>
		</div>
	</div>



</div>
