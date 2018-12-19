<?php
global $action;
global $model;

$UC = new SuperUser();
$IC = new Items();
$model = new SuperShop();

// get current user id
// $user_id = session()->value("user_id");
$order_no = $action[1];
$amount = "";
// $user = $UC->getUser();

$order = $model->getOrders(array("order_no" => $order_no));
// print_r($order); 

// $membership = $UC->getMembership();


$is_membership = false;
$subscription_method = false;


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
<div class="scene member_help_payment <?= $order ? "i:member_help_payment" : "i:scene" ?>">

<? if($order): ?>

<? 
	// print_r($order);

	$transaction_id = $order["order_no"]; 
?>

	<h1>Opret nyt medlem</h1>

	<?	if(message()->hasMessages()): ?>
		<p class="errormessage">
	<?		$messages = message()->getMessages(array("type" => "error"));
		foreach($messages as $message): ?>
			<?= $message ?><br>
	<?		endforeach;?>
		</p>
		<p class="message">
	<?		$messages = message()->getMessages(array("type" => "message"));
		foreach($messages as $message): ?>
			<?= $message ?><br>
	<?		endforeach;?>
		</p>
	<?	message()->resetMessages(); ?>
	<?	endif; ?>

	<p>Indmeldelsesgebyr: <?= $total_order_price["price"] ?>.</p>
	<div class="payment_options">
		<?= $model->formStart("registerPayment", ["class" => "mobilepay"]) ?>
			<fieldset class="mobilepay">
				<?= $model->input("payment_amount", array("type" => "hidden", "value" => $total_order_price["price"])); ?>
				<?= $model->input("payment_method", array("type" => "hidden", "value" => "mobilepay")); ?>
				<?= $model->input("order_id", array("type" => "hidden", "value" => $order["id"])); ?>
				<?= $model->input("transaction_id", array("type" => "hidden", "value" => $transaction_id)); ?>
				<!-- <div class="mobilepay qr">
					<h5>QR-kode</h5>
					<img src="/img/qr-codes/qr-signup-vesterbro.png" alt="QR-kode til indmeldelse i Vesterbro-afdelingen">
				</div> -->
				<div class="mobilepay code">
					<h5>MobilePay-nummer</h5>
					<p>(Vesterbro)</p>
					<p class="payment_info"><span class="highlight">XXXXX</span></p>
					<h5>Medlemsoprettelseskode</h5>
					<p>(Skrives i kommentarfeltet)</p>
					<p class="payment_info"><span class="highlight"><?=$transaction_id?></span></p>
				</div>
				<?= $model->input("confirm_cash_payment", array("type" => "checkbox", "label" => "Personen har betalt ".formatPrice($total_order_price)." med MobilePay.", "required" => true)); ?>
			</fieldset>
	
		<ul class="actions">
			<li class="cancel"><a href="/" class="button">Annullér</a></li>
			<!-- <li class="cancel"><a href="/" class="button">Spring over</a></li> -->
			<?= $model->submit("Godkend betaling af ".formatPrice($total_order_price), array("class" => "primary", "wrapper" => "li.pay")) ?>
		</ul>
		<?= $model->formEnd() ?>
	
		<?= $model->formStart("registerPayment", ["class" => "cash"]) ?>
			<fieldset class="cash">
				<?= $model->input("payment_amount", array("type" => "hidden", "value" => $total_order_price["price"])); ?>
				<?= $model->input("payment_method", array("type" => "hidden", "value" => "cash")); ?>
				<?= $model->input("order_id", array("type" => "hidden", "value" => $order["id"])); ?>
				<?= $model->input("transaction_id", array("type" => "hidden", "value" => $transaction_id)); ?>
				<div class="cash instructions">
					<p>Bekræft nedenfor at personen har betalt kontant.</p>
				</div>
				<?= $model->input("confirm_cash_payment", array("type" => "checkbox", "label" => "Personen har betalt ".formatPrice($total_order_price)." kontant.", "required" => true)); ?>
			</fieldset>
	
		<ul class="actions">
			<li class="cancel"><a href="/" class="button">Annullér</a></li>
			<!-- <li class="cancel"><a href="/" class="button">Spring over</a></li> -->
			<?= $model->submit("Godkend betaling af ".formatPrice($total_order_price), array("class" => "primary", "wrapper" => "li.pay")) ?>
		</ul>
		<?= $model->formEnd() ?>
	</div>


	<p>Betalingsreference: <?= $reference ?>.</p>

<? else: ?>

	<h1>Er du ved at gennemføre en betaling?</h1>
	<p>Du bør <a href="/login">logge ind</a> på din konto og starte din betaling derfra.</p>

<? endif;?>

</div>
