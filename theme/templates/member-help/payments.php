<?php
global $action;
global $model; // SuperUser
global $UC; // User
global $SC;


$this->pageTitle("Betalinger");

// get current user id
$user_id = $action[1];
// $amount = "";

// Will only return orders from current user, so no need to check order ownership
$orders = $SC->getUnpaidOrders(["user_id" => $user_id]);

// Calculate total outstanding payment
$total_payment = 0;
$order_list = [];
// $order_comment_list = [];

if($orders) {
	
	// Loop through all orders to get total payment amount
	foreach($orders as $index => $order) {
		$department = $UC->getUserDepartment(["user_id" => $order["user_id"]]);
		$total_order_price = $SC->getTotalOrderPrice($order["id"]);
		if($total_order_price) {
			$amount = formatPrice($total_order_price);
		}

		$transaction_id = $order["order_no"]; 


		$remaining_order_price = $SC->getRemainingOrderPrice($order["id"]);
		$orders[$index]["price"] = $remaining_order_price;

		// $order_comment_list[] = $order["order_no"] . " - " . $order["comment"];
		$order_list[] = $order["id"];

		$total_payment += $remaining_order_price["price"];


		$payment_methods = $this->paymentMethods();

		$mobilepay_payment_method_id = false; 
		$cash_payment_method_id = false; 

		if($payment_methods) {
			foreach ($payment_methods as $payment_method) {
				if($payment_method["classname"] == "mobilepay") {
					$mobilepay_payment_method_id = $payment_method["id"];
				}
				elseif ($payment_method["classname"] == "cash") {
					$cash_payment_method_id = $payment_method["id"]; 
				}
			}
		}

		// Get payment methods
		$user_payment_methods = $UC->getPaymentMethods(["extend" => true]);

	}
}

?>
<div class="scene member_help_payment i:payments">
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
		$full_order = $SC->getOrders(["order_id" => $order["id"]]); ?>
		<li>
			<h3>Ordrenummer: <a href="/butik/payment/<?= $full_order["order_no"] ?>"><?= $full_order["order_no"] ?></a> <?= ($full_order["comment"] ? (" – " . $full_order["comment"]) : "") ?>, <?= formatPrice($order["price"]) ?></h3>
			<ul class="orderitems">
			<? foreach($full_order["items"] as $order_item): ?>
				<li><?= $order_item["quantity"] ?> x <?= $order_item["name"] ?></li>
			<? endforeach; ?>
			</ul>
		</li>
	<? endforeach; ?>
	</ul>

	<div class="payment_options">
		<?= $model->formStart("registerPayment/".$order["order_no"], ["class" => "mobilepay"]) ?>
			<fieldset class="mobilepay">
				<?= $model->input("payment_amount", array("type" => "hidden", "value" => $total_order_price["price"])); ?>
				<?= $model->input("payment_method_id", array("type" => "hidden", "value" => $mobilepay_payment_method_id)); ?>
				<?= $model->input("order_id", array("type" => "hidden", "value" => $order["id"])); ?>
				<?= $model->input("transaction_id", array("type" => "hidden", "value" => $transaction_id)); ?>
				<div class="mobilepay qr">
					<h5>QR-kode</h5>
					<img src="data:image/png;base64,<?= base64_encode(qr_codes()->create($SC->getMobilepayLink($total_order_price["price"], $department["mobilepay_id"], $order["order_no"]), ["size" => 158])); ?>" alt="QR-kode til indmeldelse i <?= $department["name"] ?>-afdelingen">
				</div>
				<div class="mobilepay code">
					<h5>MobilePay-nummer</h5>
					<p>(<?=$department["name"]?>)</p>
					<p class="payment_info"><span class="highlight"><?=$department["mobilepay_id"]?></span></p>
					<h5>Medlemsoprettelseskode</h5>
					<p>(Skrives i kommentarfeltet)</p>
					<p class="payment_info"><span class="highlight"><?=$transaction_id?></span></p>
				</div>
				<?= $model->input("confirm_mobilepay_payment", array("type" => "checkbox", "label" => "Personen har betalt ".formatPrice($total_order_price)." med MobilePay.", "required" => true)); ?>
			</fieldset>
	
		<ul class="actions">
			<!-- <li class="cancel"><a href="/" class="button">Annullér</a></li> -->
			<!-- <li class="cancel"><a href="/" class="button">Spring over</a></li> -->
			<?= $model->submit("Godkend betaling af ".formatPrice($total_order_price), array("class" => "primary", "wrapper" => "li.pay")) ?>
		</ul>
		<?= $model->formEnd() ?>

		<?= $model->formStart("betaling/stripe/ordre/".$order["order_no"]."/process", array("class" => "card")) ?>

			<fieldset>
				<?= $model->input("card_number", array("type" => "tel", "label" => "Kortnummer", "hint_message" => "Indtast dit kortnummer", "error_message" => "Ugyldigt kortnummer")); ?>
				<?= $model->input("card_exp_month", array("type" => "tel", "hint_message" => "Måned", "error_message" => "Ugyldig")); ?>
				<span class="slash">/</span>
				<?= $model->input("card_exp_year", array("type" => "tel", "hint_message" => "År", "error_message" => "Ugyldig")); ?>
				<?= $model->input("card_cvc", array("type" => "tel", "hint_message" => "Kontrolnummer", "error_message" => "Ugyldig")); ?>

			</fieldset>

			<ul class="actions">
				<?= $model->submit("Betal ".formatPrice($total_order_price), array("class" => "primary", "wrapper" => "li.pay")) ?>
			</ul>
		<?= $model->formEnd() ?>
	
		<?= $model->formStart("registerPayment/".$order["order_no"], ["class" => "cash"]) ?>
			<fieldset class="cash">
				<?= $model->input("payment_amount", array("type" => "hidden", "value" => $total_order_price["price"])); ?>
				<?= $model->input("payment_method_id", array("type" => "hidden", "value" => $cash_payment_method_id)); ?>
				<?= $model->input("order_id", array("type" => "hidden", "value" => $order["id"])); ?>
				<?= $model->input("transaction_id", array("type" => "hidden", "value" => $transaction_id)); ?>
				<?= $model->input("receiving_user_id", array("type" => "hidden", "value" => session()->value("user_id"))); ?>
				<div class="cash instructions">
					<p>Bekræft nedenfor at personen har betalt kontant.</p>
				</div>
				<?= $model->input("confirm_cash_payment", array("type" => "checkbox", "label" => "Personen har betalt ".formatPrice($total_order_price)." kontant.", "required" => true)); ?>
			</fieldset>
	
		<ul class="actions">
			<?= $model->submit("Godkend betaling af ".formatPrice($total_order_price), array("class" => "primary", "wrapper" => "li.pay")) ?>
		</ul>
		<?= $model->formEnd() ?>
	</div>

	<!-- <div class="payment_method">
		<h2>Vælg en betalingsmetode</h2>

		<? if($payment_methods): ?>
			<h3>Vores betalingsmuligheder</h3>
			<p><?= $user_payment_methods ? "Eller v" : "V" ?>ælg en betalingsmetode til fortsat behandling af disse ordrer.</p>
			<ul class="payment_methods">

			<? foreach($payment_methods as $payment_method): ?>
				<? if($payment_method["state"] === "public" || $payment_method["state"] === "memberhelp"): ?>

				<li class="payment_method<?= $payment_method["classname"] ? " ".$payment_method["classname"] : "" ?>">

					<ul class="actions">
						<?= $HTML->oneButtonForm(
						"Betal alle ordrer med " . $payment_method["name"], 
						"/butik/selectPaymentMethodForOrders", 
						array(
							"inputs" => array(
								"order_ids" => implode(",", $order_list), 
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
	</div> -->

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


	<?= $SC->formStart("?login=true", array("class" => "login labelstyle:inject")) ?>
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