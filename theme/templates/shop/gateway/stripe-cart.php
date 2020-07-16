<?php
global $action;
global $model;

$this->pageTitle("Stripe");

$IC = new Items();

// get current user id
$user_id = session()->value("user_id");
$cart_reference = $action[3];
$total_cart_price = false;


$cart = $model->getCarts(["cart_reference" => $cart_reference]);
if($cart) {

	$total_cart_price = $model->getTotalCartPrice($cart["id"]);

	$has_error = false;
	if(message()->hasMessages(["type" => "error"])) {
		$has_error = true;
	}

}

?>
<div class="scene shopPayment stripe i:stripe">

<? if($user_id > 1 && $cart && $total_cart_price && $total_cart_price["price"] > 0): ?>


	<h1>Indtast dine kortoplysninger</h1>


	<?= $HTML->serverMessages() ?>


	<?= $model->formStart("/shop/payment-gateway/stripe/cart/".$cart["cart_reference"]."/process", array("class" => "card")) ?>
	
		<fieldset>
			<?= $model->input("card_number", array("type" => "tel")); ?>
			<?= $model->input("card_exp_month", array("type" => "tel")); ?><span class="slash">/</span><?= $model->input("card_exp_year", array("type" => "tel")); ?>
			<?= $model->input("card_cvc", array("type" => "tel")); ?>
			
		</fieldset>

		<ul class="actions">
			<?= $model->submit("Betal ".formatPrice($total_cart_price), array("class" => "primary", "wrapper" => "li.pay")) ?>
		</ul>
	<?= $model->formEnd() ?>


	<div class="cartitems">
		<h3>Ordreoversigt</h3>
		<ul class="cartitems">
		<? foreach($cart["items"] as $cart_item):
		$item = $IC->getItem(["id" => $cart_item["item_id"], "extend" => true]) ?>
			<li><?= $cart_item["quantity"] ?> x <?= $item["name"] ?></li>
		<? endforeach; ?>
		</ul>

		<ul class="actions">
			<?= $model->link("Ændr din ordre", "/shop/cart", array("class" => "button", "wrapper" => "li.modify")) ?>
		</ul>

	</div>

	<? if($has_error): ?>
	<div class="confirm">
		<h2>Eller bekræft din ordre og betal senere</h2>
		<p>Du kan også vælge at færdiggøre din ordre og betale senere.</p>
		<ul class="actions">
			<?= $HTML->oneButtonForm("Bekræft ordre", "/shop/confirmOrder/".$cart["cart_reference"], array(
				"confirm-value" => false,
				"wait-value" => "Bekræfter",
				"dom-submit" => true,
//				"static" => true,
				"class" => "primary",
				"name" => "continue",
				"wrapper" => "li.continue",
			)) ?>
		</ul>
	</div>
	<? endif; ?>

	<p class="note">
		Vi bruger <a href="https://stripe.com" target="_blank">Stripe</a> til at behandle betalingen. <br />Ingen kortoplysninger gemmes på vores servere. <br />Al kommunikation er krypteret.
	</p>

<? elseif($user_id > 1 && !$cart): ?>

	<h1>Kurven blev ikke fundet</h1>
	<p>Gå til <a href="/shop/cart">din kurv</a> for at fortsætte en eventuelt afbrudt betalingsproces.</p>

<? else: ?>

	<h1>Leder du efter betalingssiden?</h1>
	<p>Du skal først <a href="/login?login_forward=/shop/payments">logge ind</a> på din konto og betale derfra.</p>

<? endif;?>

</div>