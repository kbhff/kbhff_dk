<?php
global $action;
global $model;

$this->pageTitle("Kvittering");

$UC = new User();
$MC = new Member();

$user_id = session()->value("user_id");
message()->resetMessages();

$order = false;
$receipt_type = false;

$is_membership = false;
$subscription_method = false;
$payment_date = false;

$active_account = false;
$new_order = false;
$bulk_orders = false;

// get current user id
$user_id = session()->value("user_id");
$user = $UC->getUser();
// has account been activated
if($user) {
	$active_account = $user["status"];
}


$order_no = $action[2];
if($order_no) {
	$order = $model->getOrders(array("order_no" => $order_no));


	// get potential user membership
	$membership = $MC->getMembership();


	if($order) {
		$total_order_price = $model->getTotalOrderPrice($order["id"]);
		$remaining_order_price = $model->getRemainingOrderPrice($order["id"]);


		if($membership && $membership["order"]) {
			// is the users membership related to this order?
			$is_membership = ($membership["order"] && $order["id"] == $membership["order"]["id"]) ? true : false;
		}

		if($is_membership && $membership && $membership["item"] && $membership["item"]["subscription_method"] && $membership["item"]["subscription_method"]["duration"]) {
			$subscription_method = $membership["item"]["subscription_method"];
			$payment_date = $membership["renewed_at"] ? date("jS", strtotime($membership["renewed_at"])) : date("jS", strtotime($membership["created_at"]));
		}

	}

}


// receipt type indicated in url
if(isset($action[3])) {
	$receipt_type = $action[3];
}



?>
<div class="scene shopReceipt i:scene">


<? if($order): ?>

	<h1>Tak for din ordre!</h1>
	<div class="order_info">

		<h2>
			Din ordre er bekræftet <br />
			<? if($remaining_order_price["price"] > 0): ?>
				<? if($receipt_type == "stripe"): ?>
				– betalingen behandles
				<? else: ?>
				– afventer din betaling
				<? endif; ?>
			<? else: ?>
			– og betalt
			<? endif; ?>
		</h2>

		<ul class="orders">
			<li>
				<h3>Ordrenummer: <?= $order["order_no"] . ($order["comment"] ? (" – " . $order["comment"]) : "") ?></h3>
				<ul class="orderitems">
				<? foreach($order["items"] as $order_item): ?>
					<li><?= $order_item["quantity"] ?> x <?= $order_item["name"] ?></li>
				<? endforeach; ?>
				</ul>
			</li>
		</ul>

		<h3>
			<span class="name">I alt</span>
			<span class="total_price">
				<?= formatPrice($total_order_price, array("vat" => false)) ?>
			</span>
		</h3>

		<p>
			Du vil om et øjeblik modtage en ordrebekræftelse på <em><?= $user["email"] ?></em>.
		</p>

	</div>

	<? if($is_membership): ?>
	<div class="membership_info">
		<h2>Velkommen til KBHFF!</h2>
		<p>Tillykke med, at du nu er en del af Københavns Fødevarefællesskab.</p>
	</div>
	<? endif; ?>


	<? if(!$active_account): ?>
	<div class="activation_info">
		<p>Husk, at du skal aktivere din konto før du kan bestille grøntsager. Aktiveringskoden er blevet sendt til <em><?= $user["email"] ?></em>.</p>
	</div>
	<? elseif($user_id == 1): ?>
	<ul class="actions">
		<li><a class="button primary clickable" href="/login">Log ind</a></li>
	</ul>
	<? else: ?>
	<ul class="actions">
		<li><a class="button primary clickable" href="/profil">Gå til Min Side</a></li>
	</ul>
	
	<? endif; ?>

<? elseif($user_id > 1):?>

	<h1>Der er noget, der ikke stemmer...</h1>
	<p>Vi kunne ikke finde nogen ordrer, der matcher denne forespørgsel. <a href="mailto:payment@think.dk?subject=Payment%20receipt%20error&body=Order%20No:%20<?= $order_no ?>">Kontakt os</a> for at løse problemet.</p>

<? else: ?>

	<h1>Leder du efter din kvittering?</h1>
	<p>Du skal <a href="/login?forward_url=<?= $this->url ?>">logge ind</a> på din konto, før du kan se kvitteringer.</p>

<? endif; ?>


</div>