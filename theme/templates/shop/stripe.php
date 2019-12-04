<?php
global $action;
global $model;

$UC = new User();
$IC = new Items();
include_once("classes/users/member.class.php");
$MC = new Member();

// get user and order information 
$user_id = session()->value("user_id");
$order_no = $action[1];
$amount = "";
$user = $UC->getUser();

$order = $model->getOrders(array("order_no" => $order_no));
$membership = $MC->getMembership();

$is_membership = false;
$subscription_method = false;


if($order) {
	$total_order_price = $model->getTotalOrderPrice($order["id"]);
	if($total_order_price) {
		$amount = formatPrice($total_order_price);
	}

// is the users membership related to this order?
	if($membership && $membership["order"]) {
		$is_membership = ($membership["order"] && $order["id"] == $membership["order"]["id"]) ? true : false;
	}

// does the membership have a subscription duration?
	if($membership && $membership["item"] && $membership["item"]["subscription_method"] && $membership["item"]["subscription_method"]["duration"]) {
		$subscription_method = $membership["item"]["subscription_method"];
		$payment_date = $membership["renewed_at"] ? date("jS", strtotime($membership["renewed_at"])) : date("jS", strtotime($membership["created_at"]));
	}

}

if($is_membership) {
	$reference = "Member ".$membership["id"];
}
else {
	$reference = $order_no;
}

?>
<div class="scene shopPayment stripe <?= $order ? "i:stripe" : "i:scene" ?>">

<? if($order): ?>

	<h1>Betal dit medlemskab</h1>

	<? // show error messages 
if(message()->hasMessages(array("type" => "error"))): ?>
	<p class="errormessage">
<?	$messages = message()->getMessages(array("type" => "error"));
		message()->resetMessages();
		foreach($messages as $message): ?>
		<?= $message ?><br>
<?	endforeach;?>
	</p>
<?	endif; ?>

	<ul class="orders">
	<? // loop through order items and show price, quantity and total order price.
	foreach($order["items"] as $i => $item): ?>
		<li class="unit_price"> <?= $item["quantity"]." x ".$item["name"]." a ". formatPrice(array("price" => $item["unit_price"], "currency" => $order["currency"])) ?> <span class="price"><?= formatPrice(array("price" => $item["total_price"], "currency" => $order["currency"]))?></span></li> 
	<? endforeach; ?>
		<li>Heraf moms <span class="price vat_price"><?= formatPrice(array("price" => $total_order_price["vat"], "currency" => $total_order_price["currency"])) ?></span></li>
		<li class="total_price">I alt <span class="price"><?= formatPrice($total_order_price) ?> </span></li>
	</ul>
			

	<?= $model->formStart("/butik/betaling/".$order_no."/stripe/process", array("class" => "card")) ?>

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

	<p class="note">
		Betalingsreference: <?= $reference ?>. <br />
		Vi bruger <a href="https://stripe.com" target="_blank">Stripe</a> til at behandle betalingen. <br />
		Vi opbevarer ingen kortinformationer på vores server. <br />
		Al kommunikation er krypteret. <br />
	</p>

<? else: ?>

	<h1>Er du ved at gennemføre en betaling?</h1>
	<p>Du bør <a href="/login">logge ind</a> på din konto og starte din betaling derfra.</p>

<? endif;?>

</div>
