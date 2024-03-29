<?php
global $action;
global $model;

$this->pageTitle("Payment");

$UC = new User();
$MC = new Member();

// get current user id
$user_id = session()->value("user_id");


$order_no = $action[1];
$remaining_order_price = false;

// Will only return orders from current user, so no need to check order ownership
$order = $model->getOrders(array("order_no" => $order_no));
$membership = $MC->getMembership();

if($order && $order["payment_status"] != 2 && $order["status"] != 3) {

	$remaining_order_price = $model->getRemainingOrderPrice($order["id"]);

	// Get payment methods
	$user_payment_methods = $UC->getPaymentMethods(["extend" => true]);

	$payment_methods = $this->paymentMethods();

}

?>
<div class="scene shopPayment i:payment">

<? if($order && $remaining_order_price && $remaining_order_price["price"]): ?>

	<h1>Din ordre (<?= $order["order_no"]  ?>) er bekræftet</h1>
	<p>
		Din ordre er bekræftet og ordrens indhold er reserveret til dig.
		Du modtager snarligst en bekræftelsesmail.
	</p>
	<p>
		Vi påbegynder behandlingen af din ordre så snart vi modtager din betaling.
	</p>

	<dl class="amount">
		<dt class="amount">Skyldigt beløb</dt>
		<dd class="amount"><?= formatPrice($remaining_order_price) ?></dd>
	</dl>


	<ul class="orders">
		<li>
			<h3>Ordrenummer: <?= $order["order_no"] . ($order["comment"] ? (" – " . $order["comment"]) : "") ?></h3>
			<ul class="orderitems">
			<? foreach($order["items"] as $order_item): ?>
				<li><?= $order_item["quantity"] ?> x <?= $order_item["name"] ?></li>
				<li>
					<ul class="actions">
						<?= $HTML->oneButtonForm("Annuller", "/butik/cancelOrder/".$order["id"], [
							"confirm-value" => "Sikker?",
							"wait-value" => "Vent ...",
							"success-location" => "/profil"
						]) ?>
					</ul>
				</li>
			<? endforeach; ?>
			</ul>
		</li>
	</ul>

	<? 
	// Only show payment options if order has items or price
	if($order["items"] && $remaining_order_price && $remaining_order_price["price"] !== 0): ?>


	<div class="payment_method">
		<h2>Vælg en betalingsmetode</h2>

		<? if($user_payment_methods): ?>
			<h3>Dine betalingsmetoder</h3> 
			<p>Vælg en af dine eksisterende betalingsmetoder for at fortsætte behandlingen af denne ordre.</p>
			<ul class="payment_methods">

			<? foreach($user_payment_methods as $user_payment_method): ?>

				<? if($user_payment_method && $user_payment_method["cards"]): ?>

					<? foreach($user_payment_method["cards"] as $card): ?>
				<li class="payment_method user_payment_method<?= $user_payment_method["classname"] ? " ".$user_payment_method["classname"] : "" ?>">
					<ul class="actions">
						<?= $HTML->oneButtonForm(
						"Betal ordre med kort, der ender på " . $card["last4"], 
						"/butik/selectUserPaymentMethodForOrder",
						array(
							"inputs" => array(
								"order_id" => $order["id"], 
								"user_payment_method_id" => $user_payment_method["id"], 
								"payment_method_id" => $user_payment_method["payment_method_id"],
								"gateway_payment_method_id" => $card["id"]
							),
							"confirm-value" => false,
							"static" => true,
							"class" => "primary",
							"name" => "continue",
							"wrapper" => "li.continue.".$user_payment_method["classname"],
						)) ?>
					</ul>
					<!-- <p><?= $user_payment_method["description"] ?></p> -->
				</li>
					<? endforeach; ?>

				<? else: ?>
				<li class="payment_method user_payment_method<?= $user_payment_method["classname"] ? " ".$user_payment_method["classname"] : "" ?>">
					<ul class="actions">
						<?= $HTML->oneButtonForm(
						"Betal ordre med " . $user_payment_method["name"], 
						"/butik/selectUserPaymentMethodForOrder",
						array(
							"inputs" => array(
								"order_id" => $order["id"], 
								"user_payment_method_id" => $user_payment_method["id"], 
								"payment_method_id" => $user_payment_method["payment_method_id"]
							),
							"confirm-value" => false,
							"static" => true,
							"class" => "primary",
							"name" => "continue",
							"wrapper" => "li.continue.".$user_payment_method["classname"],
						)) ?>
					</ul>
					<!-- <p><?= $user_payment_method["description"] ?></p> -->
				</li>
				<? endif; ?>

			<? endforeach; ?>

			</ul>
		<? endif; ?>

		<? if($payment_methods): ?>
			<h3>Vores <?= $user_payment_methods ? "øvrige " : "" ?>betalingsmuligheder</h3>
			<p><?= $user_payment_methods ? "Eller v" : "V" ?>ælg en betalingsmetode til fortsat behandling af din ordre.</p>
			<ul class="payment_methods">

			<? foreach($payment_methods as $payment_method): ?>
				<? if($payment_method["state"] === "public"): ?>

				<li class="payment_method<?= $payment_method["classname"] ? " ".$payment_method["classname"] : "" ?>">

					<ul class="actions">
						<?= $HTML->oneButtonForm(
						"Betal med " . $payment_method["name"], 
						"/butik/selectPaymentMethodForOrder", 
						array(
							"inputs" => array(
								"order_id" => $order["id"], 
								"payment_method_id" => $payment_method["id"]
							),
							"confirm-value" => false,
							"static" => true,
							"class" => "primary",
							"name" => "continue",
							"wrapper" => "li.continue.".$payment_method["classname"],
						)) ?>
					</ul>
					<p><?= $payment_method["description"] ?></p>

				</li>
				<? endif; ?>
			<? endforeach; ?>

			</ul>
		<? endif; ?>
	</div>


	<h2>Eller kontakt os for at betale.</h2>
	<p>
		<a href="mailto:payment@think.dk?subject=Payment%20of%20order%20<?= $order["order_no"] ?>">Kontakt os</a> venligst, hvis systemet ikke kan behandle din betaling. Så klarer vi betalingen på anden vis.
	</p>
	<? endif; ?>


<? // No payments
elseif(session()->value("user_group_id") > 1): ?>

	<h1>Betalingsmuligheder</h1>
	<h2>Storartede nyheder</h2>
	<p>Du har ingen udeståender.</p>
	<ul class="actions">
		<li><a href="/profil" class="button primary">Gå til Min Side</a></li>
	</ul>

<? 
// User not logged in
else:

	$model = new User();
	$username = stringOr(getPost("username"));
	?>

	<h1>Betalingsmuligheder</h1>
	<h2>Leder du efter betalingssiden?</h2>
	<p>Du skal først logge ind på din konto.</p>


	<?= $model->formStart("?login=true", array("class" => "login labelstyle:inject")) ?>
		<?= $model->input("login_forward", ["type" => "hidden", "value" => $this->url]); ?>


		<?= $HTML->serverMessages() ?>


		<fieldset>
			<?= $model->input("username", array("required" => true, "value" => $username, "label" => "Brugernavn", "hint_message" => "Brug dit medlemsnr., email eller telefonnummer som brugernavn", "error_message" => "Det ligner ikke et gyldigt brugernavn",)); ?>
			<?= $model->input("password", array("required" => true, "label" => "Adgangskode", "hint_message" => "Skriv din adgangskode","error_message" => "Ugyldig adgangskode",)); ?>
		</fieldset>

		<ul class="actions">
			<?= $model->submit("Log ind", array("class" => "primary", "wrapper" => "li.login")) ?>
		</ul>
	<?= $model->formEnd() ?>
	

<? endif;?>

</div>	