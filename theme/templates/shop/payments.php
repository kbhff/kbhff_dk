<?php
global $action;
global $model;
$UC = new User();

$this->pageTitle("Payments");

// get current user id
$user_id = session()->value("user_id");
//$order_no = $action[1];
// $amount = "";

// Will only return orders from current user, so no need to check order ownership
$orders = $model->getUnpaidOrders();

// Calculate total outstanding payment
$total_payment = 0;
$order_list = [];
// $order_comment_list = [];

if($orders) {

	// Loop through all orders to get total payment amount
	foreach($orders as $index => $order) {


		$remaining_order_price = $model->getRemainingOrderPrice($order["id"]);
		$orders[$index]["price"] = $remaining_order_price;

		// $order_comment_list[] = $order["order_no"] . " - " . $order["comment"];
		$order_list[] = $order["id"];

		$total_payment += $remaining_order_price["price"];


		$payment_methods = $this->paymentMethods();

		// Get payment methods
		$user_payment_methods = $UC->getPaymentMethods(["extend" => true]);

	}
}

?>
<div class="scene shopPayment shopPayments i:payments">
	<h1>Forfaldne betalinger</h1>

<? 
// Outstanding payments
if($orders && $total_payment): ?>


	<?= $HTML->serverMessages() ?>


	<dl class="amount">
		<dt class="amount">Skyldigt beløb</dt>
		<dd class="amount"><?= formatPrice(["price" => $total_payment, "currency" => $remaining_order_price["currency"]]) ?></dd>
	</dl>


	<h2>For betaling af:</h2>
	<ul class="orders">
	<? foreach($orders as $order): 
		$full_order = $model->getOrders(["order_id" => $order["id"]]); ?>
		<li>
			<h3>Ordrenummer: <a href="/shop/payment/<?= $full_order["order_no"] ?>"><?= $full_order["order_no"] ?></a> <?= ($full_order["comment"] ? (" – " . $full_order["comment"]) : "") ?>, <?= formatPrice($order["price"]) ?></h3>
			<ul class="orderitems">
			<? foreach($full_order["items"] as $order_item): ?>
				<li><?= $order_item["quantity"] ?> x <?= $order_item["name"] ?></li>
			<? endforeach; ?>
			</ul>
		</li>
	<? endforeach; ?>
	</ul>


	<div class="payment_method">
		<h2>Vælg en betalingsmetode</h2>

		<? if($user_payment_methods): ?>
			<h3>Dine betalingsmetoder</h3>
			<p>Vælg en af dine eksisterende betalingsmetoder for at fortsætte behandlingen af disse ordrer.</p>
			<ul class="payment_methods">

			<? foreach($user_payment_methods as $user_payment_method): ?>

				<? if($user_payment_method && $user_payment_method["cards"]): ?>

					<? foreach($user_payment_method["cards"] as $card): ?>
				<li class="payment_method user_payment_method<?= $user_payment_method["classname"] ? " ".$user_payment_method["classname"] : "" ?>">
					<ul class="actions">
						<?= $HTML->oneButtonForm(
						"Betal alle ordrer med kort, der ender på " . $card["last4"], 
						"/shop/selectUserPaymentMethodForOrders",
						array(
							"inputs" => array(
								"order_ids" => implode($order_list, ","), 
								"user_payment_method_id" => $user_payment_method["id"], 
								"payment_method_id" => $user_payment_method["payment_method_id"],
								"gateway_payment_method_id" => $card["id"]
							),
							"confirm-value" => false,
							"wait-value" => "Vent venligst",
							"dom-submit" => true,
							"class" => "primary",
							"name" => "continue",
							"wrapper" => "li.continue.".$user_payment_method["classname"],
						)) ?>
					</ul>
					<p><?= $user_payment_method["description"] ?></p>
				</li>
					<? endforeach; ?>

				<? else: ?>
				<li class="payment_method user_payment_method<?= $user_payment_method["classname"] ? " ".$user_payment_method["classname"] : "" ?>">
					<ul class="actions">
						<?= $HTML->oneButtonForm(
						"Betal alle ordrer med " . $user_payment_method["name"], 
						"/shop/selectUserPaymentMethodForOrders",
						array(
							"inputs" => array(
								"order_ids" => implode($order_list, ","), 
								"user_payment_method_id" => $user_payment_method["id"], 
								"payment_method_id" => $user_payment_method["payment_method_id"]
							),
							"confirm-value" => false,
							"wait-value" => "Vent venligst",
							"dom-submit" => true,
							"class" => "primary",
							"name" => "continue",
							"wrapper" => "li.continue.".$user_payment_method["classname"],
						)) ?>
					</ul>
					<p><?= $user_payment_method["description"] ?></p>
				</li>
				<? endif; ?>

			<? endforeach; ?>

			</ul>
		<? endif; ?>

		<? if($payment_methods): ?>
			<h3>Vores <?= $user_payment_methods ? "øvrige " : "" ?>betalingsmuligheder</h3>
			<p><?= $user_payment_methods ? "Eller v" : "V" ?>ælg en betalingsmetode til fortsat behandling af disse ordrer.</p>
			<ul class="payment_methods">

			<? foreach($payment_methods as $payment_method): ?>
				<? if($payment_method["state"] === "public"): ?>

				<li class="payment_method<?= $payment_method["classname"] ? " ".$payment_method["classname"] : "" ?>">

					<ul class="actions">
						<?= $HTML->oneButtonForm(
						"Betal alle ordrer med " . $payment_method["name"], 
						"/shop/selectPaymentMethodForOrders", 
						array(
							"inputs" => array(
								"order_ids" => implode($order_list, ","), 
								"payment_method_id" => $payment_method["id"]
							),
							"confirm-value" => false,
							"wait-value" => "Vent venligst",
							"dom-submit" => true,
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


	<!--h2>Please choose a payment method:</h2>

	<ul class="payment_methods">

	<? foreach($payment_methods as $payment_method): ?>
		<? if($payment_method["state"] === "public"): ?>

		<li class="payment_method<?= $payment_method["classname"] ? " ".$payment_method["classname"] : "" ?>">

			<ul class="actions">
				<?= $HTML->oneButtonForm("Betal med " . $payment_method["name"], "/shop/selectBulkPaymentMethod", array(
					"inputs" => array(
						"order_ids" => implode($order_list, ","), 
						"payment_method" => $payment_method["id"]
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

	</ul-->

<? 
// No payments
elseif($user_id > 1): ?>

	<h2>Storartede nyheder</h2>
	<p>Du har ingen udeståender.</p>

<? 
// User not logged in
else:

	$model = new User();
	$username = stringOr(getPost("username"));
	?>

	<h2>Leder du efter betalingssiden?</h2>
	<p>Du skal først logge ind på din konto.</p>


	<?= $model->formStart("?login=true", array("class" => "login labelstyle:inject")) ?>
		<?= $model->input("login_forward", ["type" => "hidden", "value" => $this->url]); ?>


		<?= $HTML->serverMessages() ?>


		<fieldset>
			<?= $model->input("username", array("required" => true, "value" => $username)); ?>
			<?= $model->input("password", array("required" => true)); ?>
		</fieldset>

		<ul class="actions">
			<?= $model->submit("Log ind", array("class" => "primary", "wrapper" => "li.login")) ?>
		</ul>
	<?= $model->formEnd() ?>
	

<? endif;?>

</div>