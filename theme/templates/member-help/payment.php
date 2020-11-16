<?php
global $action;
global $model;
global $SC;

$UC = new SuperUser();
$IC = new Items();
$model = new SuperShop();

$order_no = $action[1];
$amount = "";
// $user = $UC->getUser();

$order = $model->getOrders(array("order_no" => $order_no));
$member_user_id = $order["user_id"];

$order_items_without_pickupdates = $SC->getOrderItemsWithoutPickupdate(["order_id" => $order["id"]]);
$order_pickupdates = $SC->getOrderPickupdates($order["id"], ["user_id" => $member_user_id]);



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


	$member_user = $UC->getKbhffUser(["user_id" => $member_user_id]);
	if($member_user) {
		$member_name = $member_user['nickname'] ? $member_user['nickname'] : $member_user['firstname'] . " " . $member_user['lastname'];
		$member_name_possesive = preg_match("/s$/", $member_name) ? $member_name."'" : $member_name."s";
	}

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
<? elseif($order && $order["status"] == 3): ?>
	<div>
		<h1>Hovsa?</h1>
		<p>Denne order (<?= $order["order_no"] ?>) er annulleret, så der er intet at gøre her.</p>
	</div>
<? else: ?>

<div class="scene member_help_payment <?= $order ? "i:member_help_payment" : "i:scene" ?>">
	<? if($order && $mobilepay_payment_method_id && $cash_payment_method_id): ?>

<? 
	// print_r($order);

	$transaction_id = $order["order_no"]; 
?>
	<div class="c-wrapper">
		<div class="c-box obs">
			<h2 class="obs"><span class="highlight">OBS! </span>Handler på vegne af <span class="highlight"><a href="/medlemshjaelp/brugerprofil/<?= $member_user_id ?>"><?= $member_name ?></a></span></h2>
		</div>
	</div>

	<h1>Betaling</h1>

	<?= $HTML->serverMessages(["type" => "error"]) ?>


	<? if($order_items_without_pickupdates): ?>
	<ul class="items orders">
		<? foreach($order_items_without_pickupdates as $order_item): ?>
		<li class="unit_price">

			<span class="quantity"><?= $order_item["quantity"] ?></span>
			<span class="x">x </span>
			<span class="name"><?= $order_item["name"] ?> </span>
			<span class="a">á </span>
			<span class="unit_price"><?= formatPrice(array("price" => $order_item["unit_price"], "currency" => $order["currency"])) ?></span>
			<span class="total_price">
				<?= formatPrice(array(
						"price" => $order_item["total_price"], 
						"vat" => $order_item["total_vat"], 
						"currency" => $order["currency"], 
						"country" => $order["country"]
					), 
					array("vat" => false)
				) ?>
			</span>
		</li> 
		<? endforeach; ?>
	</ul>
	
	<? endif; ?>

	<? if($order_pickupdates): ?>
	<ul class="pickupdates">

			<? foreach($order_pickupdates as $pickupdate): 

				$pickupdate_order_items = $model->getPickupdateOrderItems($pickupdate["id"], ["order_id" => $order["id"]]);

			?>
			<? if($pickupdate_order_items): ?>
			
		<li class="pickupdate">
			<h4 class="pickupdate"><?= date("d/m-Y", strtotime($pickupdate["pickupdate"])) ?> – Afhentning <span class="name"><?= $department ? $department["name"] : "ukendt afdeling" ?></span></h4>

			<ul class="items orders">
				<? foreach($pickupdate_order_items as $order_item):
				$item = $IC->getItem(array("id" => $order_item["item_id"], "extend" => array("subscription_method" => true))); 
				$price = $model->getPrice($order_item["item_id"], array("user_id" => $member_user_id, "quantity" => $order_item["quantity"], "currency" => $order["currency"], "country" => $order["country"]));
				$order_item_id = $order_item["id"];
				?>

				<li class="item_id:<?= $order_item["item_id"] ?>">

					 <? /* = $order_item["quantity"]." x ".$order_item["name"]." a ". formatPrice(array("price" => $order_item["unit_price"], "currency" => $order["currency"])) ?> <span class="price"><?= formatPrice(array("price" => $order_item["total_price"], "currency" => $order["currency"]))?></span> */ ?>

		 			<span class="quantity"><?= $order_item["quantity"] ?></span>
		 			<span class="x">x </span>
		 			<span class="name"><?= $order_item["name"] ?> </span>
		 			<span class="a">á </span>
		 			<span class="unit_price"><?= formatPrice(array("price" => $order_item["unit_price"], "currency" => $order["currency"])) ?></span>
		 			<span class="total_price">
		 				<?= formatPrice(array(
		 						"price" => $order_item["total_price"], 
		 						"vat" => $order_item["total_vat"], 
		 						"currency" => $order["currency"], 
		 						"country" => $order["country"]
		 					), 
		 					array("vat" => false)
		 				) ?>
		 			</span>

				 </li>



				<? endforeach; ?>
			</ul>
		</li>

			<? endif; ?>
			<? endforeach; ?>
	</ul>
	<? endif; ?>

	<div class="total">
		<p>
			<span class="name">Heraf moms</span>
			<span class="total_price">
				<?= formatPrice(array("price" => $total_order_price["vat"], "currency" => $total_order_price["currency"])) ?>
			</span>
		</p>
		<h3>
			<span class="name">I alt</span>
			<span class="total_price">
				<?= formatPrice($total_order_price) ?>
			</span>
		</h3>
			
	</div>

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

		<? if(!$model->hasSignupfeeInOrder($order["id"])): ?>
		<ul class="actions">
			<li class="cancel"><a href="/medlemshjaelp/butik/cancelOrder/<?= $order_no ?>/<?= $member_user_id ?>" class="button">Annullér ordre</a></li>
		</ul>
		<? endif; ?>
	</div>

	<? else: ?>
	
		<h1>Er du ved at gennemføre en betaling?</h1>
		<p>Du bør <a href="/login">logge ind</a> på din konto og starte din betaling derfra.</p>
	<? endif; ?>
</div>

<? endif;?>

