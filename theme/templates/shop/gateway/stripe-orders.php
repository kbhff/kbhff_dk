<?php
global $action;
global $model;
$UC = new User();

$this->pageTitle("Betaling");

// get current user id
$user_id = session()->value("user_id");

$order_ids = false;
$total_payment = 0;


// order IDs indicated in url
if(isset($action[3])) {

	$order_ids = $action[3];
	if($order_ids) {
		$order_ids = explode(",", $order_ids);


		// Loop through all orders to get total payment amount
		foreach($order_ids as $order_id) {

			$order = $model->getOrders(array("order_id" => $order_id));
			if($order) {

				$remaining_order_price = $model->getRemainingOrderPrice($order["id"]);

				$order_list[] = $order["order_no"];

				$total_payment += $remaining_order_price["price"];

			}

		}

	}

}


?>
<div class="scene shopPayment stripe <?= $order ? "i:stripe" : "i:scene" ?>">

<? if($user_id > 1 && $order_ids && $total_payment): ?>


	<h1>Indtast dine kortoplysninger</h1>


	<?= $HTML->serverMessages() ?>


	<?= $model->formStart("/butik/betalingsgateway/stripe/ordrer/".implode(",", $order_ids)."/process", array("class" => "card")) ?>
	
		<fieldset>
			<?= $model->input("card_number", array("label" => "Kortnummer", "hint_message" => "Indtast dit kortnummer", "error_message" => "Ugyldigt kortnummer", "type" => "tel")); ?>
			<?= $model->input("card_exp_month", array("label" => "Måned", "type" => "tel", "hint_message" => "Måned", "error_message" => "Ugyldig")); ?><span class="slash">/</span><?= $model->input("card_exp_year", array("label" => "År", "type" => "tel", "hint_message" => "År", "error_message" => "Ugyldig")); ?>
			<?= $model->input("card_cvc", array("type" => "tel", "hint_message" => "Kontrolnummer", "error_message" => "Ugyldig")); ?>
		</fieldset>

		<ul class="actions">
			<?= $model->submit("Betal ".formatPrice(["price" => $total_payment, "currency" => $remaining_order_price["currency"]]), array("class" => "primary", "wrapper" => "li.pay")) ?>
		</ul>
	<?= $model->formEnd() ?>

	<div class="orderitems">
		<h3>For orders</h3>
		<p><?= implode(", ", $order_list) ?></p>
	</div>

	<p class="note">
		Vi bruger <a href="https://stripe.com" target="_blank">Stripe</a> til at behandle betalingen. <br />Ingen kortoplysninger gemmes på vores servere. <br />Al kommunikation er krypteret.
	</p>

<? elseif($user_id > 1 && !$total_payment): ?>

	<h1>Ordrerne blev ikke fundet</h1>
	<p>Tjek, om du har andre <a href="/butik/betalinger">udeståender</a>.</p>

<? else: ?>

	<h1>Leder du efter betalingssiden?</h1>
	<p>Du skal først <a href="/login?forward_url=<?= $this->url ?>">logge ind</a> på din konto og betale derfra.</p>

<? endif;?>

</div>