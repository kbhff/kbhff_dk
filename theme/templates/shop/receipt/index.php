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


// get current user id
$user_id = session()->value("user_id");
$user = $UC->getUser();
// has account been activated
if($user) {
	$active_account = $user["status"];
}


// order no indicated in url
if(isset($action[1])) {

	$order_no = $action[1];
	if($order_no) {
		$order = $model->getOrders(array("order_no" => $order_no));


		// get potential user membership
		$membership = $UC->getMembership();


		if($order) {
			$total_order_price = $model->getTotalOrderPrice($order["id"]);
			$remaining_order_price = $model->getRemainingOrderPrice($order["id"]);


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

// receipt type indicated in url
if(isset($action[2])) {
	$receipt_type = $action[2];
}



?>
<div class="scene shopReceipt i:scene">

<? if($order): ?>

	<h1>Tak for det.</h1>


<?	if($receipt_type == "banktransfer"): ?>

	<h2>Betal med bankoverførsel</h2>

	<p>
	<? if($subscription_method && $payment_date && $remaining_order_price["price"] == $total_order_price["price"]): ?>
		Set up a re-occuring payment every <?= strtolower($subscription_method["name"]) ?> on the <?= $payment_date ?> to our bankaccount, using the information below:
	<? else: ?>
		Make a payment to our bankaccount, using the information below:
	<? endif; ?>
	</p>

	<dl>
		<dt class="amount">Amount</dt>
		<dd class="amount"><?= formatPrice($remaining_order_price) ?></dd>

		<dt class="recipient">Recipient</dt>
		<dd class="recipient">think.dk</dd>

		<dt class="reference">Reference</dt>
		<? if($is_membership): ?>
		<dd class="reference">Member <?= $membership["id"] ?></dd>
		<? else: ?>
		<dd class="reference"><?= $order_no ?></dd>
		<? endif; ?>

		<dt class="bank">Bank</dt>
		<dd class="bank">Fælleskassen</dd>

		<dt class="account">Account no</dt>
		<dd class="account">8411 4145172</dd>

		<dt class="iban">IBAN</dt>
		<dd class="iban">DK3184110004145172</dd>

		<dt class="swift">SWIFT/BIC</dt>
		<dd class="swift">FAELDKK1</dd>
	</dl>


<?	elseif($receipt_type == "paypal"): ?>


	<h2>Pay with PayPal</h2>
	<p>
		Make a payment to our PayPal account, using the information below:
	</p>

	<dl>
		<dt class="amount">Amount</dt>
		<dd class="amount"><?= formatPrice($remaining_order_price) ?></dd>

		<dt class="recipient">PayPal account</dt>
		<dd class="recipient">payment@think.dk</dd>

		<dt class="reference">Reference</dt>
		<? if($is_membership): ?>
		<dd class="reference">Member <?= $membership["id"] ?></dd>
		<? else: ?>
		<dd class="reference"><?= $order_no ?></dd>
		<? endif; ?>
	</dl>


<?	elseif($receipt_type == "cash"): ?>


	<h2>Cash payment</h2>
	<p>Just bring <?= formatPrice($remaining_order_price) ?> in cash next time you come to the Center.</p>


<?	endif; ?>


<? else: ?>


	<h2>Thank you for supporting change.</h2>


<? endif; ?>


<? if($is_membership): ?>
	<p>We are thrilled to have you on board - go ahead and check out our <a href="/events">upcoming events</a>!</p>
<? endif; ?>


<? if(!$active_account): ?>
	<p>Remember to activate your account, otherwise you won't get our newsletter. Check your inbox for the Activation email.</p>
<? endif; ?>


</div>
