<?php
global $action;
global $model;

$UC = new SuperUser();
$IC = new Items();
$model = new SuperShop();

$order_no = $action[1];
$amount = "";
// $user = $UC->getUser();

$order = $model->getOrders(array("order_no" => $order_no));
// print_r($order); 

$department = $UC->getUserDepartment(["user_id" => $order["user_id"]]);
// print_r($department);

$is_membership = false;
$subscription_method = false;

$payment_methods = $this->paymentMethods();
// print_r($payment_methods); exit();
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

// print_r($mobilepay_payment_method_id); exit();

if($order) {
	$total_order_price = $model->getTotalOrderPrice($order["id"]);
	if($total_order_price) {
		$amount = formatPrice($total_order_price);
	}


	// if($membership && $membership["order"]) {
	// 	$is_membership = ($membership["order"] && $order["id"] == $membership["order"]["id"]) ? true : false;
	// }


	// if($membership && $membership["item"] && $membership["item"]["subscription_method"] && $membership["item"]["subscription_method"]["duration"]) {
	// 	$subscription_method = $membership["item"]["subscription_method"];
	// 	$payment_date = $membership["renewed_at"] ? date("jS", strtotime($membership["renewed_at"])) : date("jS", strtotime($membership["created_at"]));
	// }

}

if($is_membership) {
	$reference = "Member ".$membership["id"];
}
else {
	$reference = $order_no;
}

//$this->headerIncludes(["https://checkout.stripe.com/checkout.js"]);

?>

<? if($order && $order["payment_status"] == 2): ?>
	<div class="i:scene">

		<h1>Hovsa?</h1>
		<p>Denne ordre (<?= $order["order_no"] ?>) er allerede betalt, så der er intet at gøre her.</p>
	</div>
<? else: ?>

<div class="scene member_help_payment <?= $order ? "i:member_help_payment" : "i:scene" ?>">
	<? if($order && $mobilepay_payment_method_id && $cash_payment_method_id): ?>

<? 
	// print_r($order);

	$transaction_id = $order["order_no"]; 
?>

	<h1>Betaling</h1>

	<?	if(message()->hasMessages()): ?>
	<p class="errormessage">
	<?		$messages = message()->getMessages(array("type" => "error"));
		foreach($messages as $message): ?>
			<?= $message ?><br>
	<?		endforeach;?>
	</p>
	<?	message()->resetMessages(); ?>
	<?	endif; ?>
	
	
	<ul class="orders">
	<? foreach($order["items"] as $i => $item): ?>
		<li class="unit_price"> <?= $item["quantity"]." x ".$item["name"]." a ". formatPrice(array("price" => $item["unit_price"], "currency" => $order["currency"])) ?> <span class="price"><?= formatPrice(array("price" => $item["total_price"], "currency" => $order["currency"]))?></span></li> 
	<? endforeach; ?>
		<li>Heraf moms <span class="price vat_price"><?= formatPrice(array("price" => $total_order_price["vat"], "currency" => $total_order_price["currency"])) ?></span></li>
		<li class="total_price">I alt <span class="price"><?= formatPrice($total_order_price) ?> </span></li>
	</ul>
	<div class="payment_options">
		<?= $model->formStart("registerPayment/".$order_no, ["class" => "mobilepay"]) ?>
			<fieldset class="mobilepay">
				<?= $model->input("payment_amount", array("type" => "hidden", "value" => $total_order_price["price"])); ?>
				<?= $model->input("payment_method_id", array("type" => "hidden", "value" => $mobilepay_payment_method_id)); ?>
				<?= $model->input("order_id", array("type" => "hidden", "value" => $order["id"])); ?>
				<?= $model->input("transaction_id", array("type" => "hidden", "value" => $transaction_id)); ?>
				<div class="mobilepay qr">
					<h5>QR-kode</h5>
					<img src="data:image/png;base64,<?= base64_encode(qr_codes()->create($model->getMobilepayLink($total_order_price["price"], $department["mobilepay_id"], $order["order_no"]), ["size" => 158])); ?>" alt="QR-kode til indmeldelse i <?= $department["name"] ?>-afdelingen">
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

		<?= $model->formStart("betaling/stripe/ordre/".$order_no."/process", array("class" => "card")) ?>

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
	
		<?= $model->formStart("registerPayment/".$order_no, ["class" => "cash"]) ?>
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

	<? else: ?>
	
		<h1>Er du ved at gennemføre en betaling?</h1>
		<p>Du bør <a href="/login">logge ind</a> på din konto og starte din betaling derfra.</p>
	<? endif; ?>
</div>

<? endif;?>
