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
	function strToHex($string){
		$hex='';
		for ($i=0; $i < strlen($string); $i++){
			$hex .= dechex(ord($string[$i]));
		}
		return $hex;
	}

	$transaction_id = strToHex($order["order_no"]); 
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
	<?= $model->formStart("registerPayment", array("class" => "card")) ?>
		<?= $model->input("payment_amount", array("type" => "hidden", "value" => $total_order_price["price"])); ?>
		<?= $model->input("payment_method", array("type" => "hidden", "value" => "mobilepay")); ?>
		<?= $model->input("order_id", array("type" => "hidden", "value" => $order["id"])); ?>
		<?= $model->input("transaction_id", array("type" => "hidden", "value" => $transaction_id)); ?>
		<fieldset>
			<img class="qr" src="/img/qr-codes/qr-signup-vesterbro.png" alt="QR-kode til indmeldelse i Vesterbro-afdelingen">
		</fieldset>


	<ul class="actions">
		<li class="cancel"><a href="/" class="button">Annullér</a></li>
		<!-- <li class="cancel"><a href="/" class="button">Spring over</a></li> -->
		<?= $model->submit("Godkend betaling af ".formatPrice($total_order_price), array("class" => "primary", "wrapper" => "li.pay")) ?>
	</ul>
	<?= $model->formEnd() ?>


	<?= $model->formStart("registerPayment", array("class" => "card")) ?>
		<?= $model->input("payment_amount", array("type" => "hidden", "value" => $total_order_price["price"])); ?>
		<?= $model->input("payment_method", array("type" => "hidden", "value" => "cash")); ?>
		<?= $model->input("order_id", array("type" => "hidden", "value" => $order["id"])); ?>
		<?= $model->input("transaction_id", array("type" => "hidden", "value" => $transaction_id)); ?>
		<fieldset>
			<?= $model->input("confirm_cash_payment", array("type" => "checkbox", "label" => "Personen har betalt ".formatPrice($total_order_price)." kontant.", "required" => true,  "value" => $transaction_id)); ?>
		</fieldset>

	<ul class="actions">
		<li class="cancel"><a href="/" class="button">Annullér</a></li>
		<!-- <li class="cancel"><a href="/" class="button">Spring over</a></li> -->
		<?= $model->submit("Godkend betaling af ".formatPrice($total_order_price), array("class" => "primary", "wrapper" => "li.pay")) ?>
	</ul>
	<?= $model->formEnd() ?>


	<p>Betalingsreference: <?= $reference ?>.</p>

<? else: ?>

	<h1>Er du ved at gennemføre en betaling?</h1>
	<p>Du bør <a href="/login">logge ind</a> på din konto og starte din betaling derfra.</p>

<? endif;?>

</div>
