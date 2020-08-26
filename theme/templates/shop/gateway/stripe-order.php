<?php
global $action;
global $model;

$this->pageTitle("Stripe");

$MC = new Member();
$IC = new Items();

// get current user id
$user_id = session()->value("user_id");
$order_no = $action[3];
$remaining_order_price = false;
$is_membership = false;

$order = $model->getOrders(array("order_no" => $order_no));

if($order) {
	$remaining_order_price = $model->getRemainingOrderPrice($order["id"]);
	$membership = $MC->getMembership();

	if($membership && $membership["order"]) {
		$is_membership = ($membership["order"] && $order["id"] == $membership["order"]["id"]) ? true : false;
	}

}

?>
<div class="scene shopPayment stripe <?= $order ? "i:stripe" : "i:scene" ?>">

<? if($user_id > 1 && $order && $remaining_order_price && $remaining_order_price["price"] > 0): ?>

	<h1>Indtast dine kortoplysninger</h1>


	<?= $HTML->serverMessages() ?>


	<?= $model->formStart("/butik/betalingsgateway/stripe/ordre/".$order_no."/process", array("class" => "card")) ?>
	
		<fieldset>
			<?= $model->input("card_number", array("label" => "Kortnummer", "hint_message" => "Indtast dit kortnummer", "error_message" => "Ugyldigt kortnummer", "type" => "tel")); ?>
			<?= $model->input("card_exp_month", array("label" => "Måned", "type" => "tel", "hint_message" => "Måned", "error_message" => "Ugyldig")); ?><span class="slash">/</span><?= $model->input("card_exp_year", array("label" => "År", "type" => "tel", "hint_message" => "År", "error_message" => "Ugyldig")); ?>
			<?= $model->input("card_cvc", array("type" => "tel", "hint_message" => "Kontrolnummer", "error_message" => "Ugyldig")); ?>
		</fieldset>

		<ul class="actions">
			<?= $model->submit("Betal ".formatPrice($remaining_order_price), array("class" => "primary", "wrapper" => "li.pay")) ?>
		</ul>
	<?= $model->formEnd() ?>


	<div class="orderitems">
		<h3>Ordreoverblik</h3>
		<ul class="orderitems">
		<? foreach($order["items"] as $order_item):
		$item = $IC->getItem(["id" => $order_item["item_id"], "extend" => true]) ?>
			<li><?= $order_item["quantity"] ?> x <?= $item["name"] ?></li>
		<? endforeach; ?>
		</ul>
	</div>

	<p class="note">
		Vi bruger <a href="https://stripe.com" target="_blank">Stripe</a> til at behandle betalingen. <br />Ingen kortoplysninger gemmes på vores servere. <br />Al kommunikation er krypteret.
	</p>


<? elseif($user_id > 1 && !$order): ?>

	<h1>Ordren blev ikke fundet</h1>
	<p>Tjek, om du har andre <a href="/butik/betalinger">udeståender</a>.</p>

<? else: ?>

	<h1>Leder du efter betalingssiden?</h1>
	<p>Du skal først <a href="/login?login_forward=/butik/betalinger">logge ind</a> på din konto og betale derfra.</p>

<? endif;?>

</div>