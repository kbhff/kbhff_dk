<?php
global $action;
global $model;
$IC = new Items();
$UC = new User();

$this->pageTitle("Kurv");

$user = $UC->getUser();

$user_id = session()->value("user_id");

// if cart reference was passed to cart controller
if(count($action) > 1) {
	session()->value("cart_reference", $action[1]);
}
$cart = $model->getCart();
$cart_id = $cart ? $cart["id"] : false;





if($cart && $cart["items"]) {

	// Get the total cart price
	$total_cart_price = $model->getTotalCartPrice($cart_id);
	
	if($total_cart_price && $total_cart_price["price"] > 0) {
		
		// Get payment methods
		$payment_methods = $this->paymentMethods();
		
		// Get payment methods
		$user_payment_methods = $UC->getPaymentMethods(["extend" => true]);
		
	}
	
	$cart_pickupdates = $model->getCartPickupdates();
	$cart_items_without_pickupdate = $model->getCartItemsWithoutPickupdate();
	
	$department = false;
	if($user_id != 1) {
		$department = $UC->getUserDepartment();
	}
}

?>
<div class="scene cart i:cart">
	<h1>Din kurv</h1>

	<?= $HTML->serverMessages(); ?>

	<div class="all_items">
	<? if($cart && $cart["items"]): ?>
		<h2>Kurvens indhold</h2>

		<? if($cart_items_without_pickupdate): ?>
		<ul class="items">
			<? 
			// Loop through all cart items and show information and editing options of each item.
			foreach($cart_items_without_pickupdate as $cart_item):
				$item = $IC->getItem(array("id" => $cart_item["item_id"], "extend" => array("subscription_method" => true)));
				$price = $model->getPrice($cart_item["item_id"], array("quantity" => $cart_item["quantity"], "currency" => $cart["currency"], "country" => $cart["country"]));
			?>
			<li class="item id:<?= $item["id"] ?>">
				<? if($item["itemtype"] != "signupfee"): ?>
				 
				<?
				// add option of updating item quantity to item 
				print $model->formStart("/butik/updateCartItemQuantity/".$cart["cart_reference"]."/".$cart_item["id"], array("class" => "updateCartItemQuantity labelstyle:inject")) ?>
					<fieldset>
						<?= $model->input("quantity", array(
							"id" => "input_quantity_".$item["id"],
							"type" => "integer",
							"value" =>  $cart_item["quantity"],
							"label" => "Antal",
							"hint_message" => "State the quantity of this item"
						)) ?>
					</fieldset>
					<ul class="actions">
						<?= $model->submit("Opdatér", array("name" => "update", "wrapper" => "li.save")) ?>
					</ul>
				<?= $model->formEnd() ?>
				<? else: ?>
				<span class="quantity"><?= $cart_item["quantity"] ?> </span>
				<? endif; ?>
				<span class="x">x </span>
				<span class="name"><?= $item["name"] ?> </span>
				<span class="a">á </span>
				<span class="unit_price"><?= formatPrice($price) ?></span>
				<span class="total_price">
					<? // generate total price and vat to item 
					print formatPrice(array(
							"price" => $price["price"]*$cart_item["quantity"],
							"vat" => $price["vat"]*$cart_item["quantity"],
							"currency" => $cart["currency"],
							"country" => $cart["country"]
						),
						array("vat" => false)
					) ?>
				</span>

				<? if($item["itemtype"] == "signupfee"): ?>
				<p class="membership">
					Dette køb indeholder et medlemskab.
				</p>
				<? endif; ?>

				<? if(isset($item["associated_membership_id"])):
					 $membership = $IC->getItem(["id" => $item["associated_membership_id"], "extend" => ["subscription_method" => true]]); 
				?>
				<p class="subscription_method">
					<? if($membership["subscription_method"]["duration"] == "annually"): ?>
					Tilbagevendende betaling hvert år.
					<? else: ?>
					Tilbagevendende betaling hver <?= strtolower($membership["subscription_method"]["name"]) ?>.
					<? endif; ?>
				</p>

				<? elseif($item["subscription_method"]): ?>
				<p class="subscription_method">
					<? if($item["subscription_method"]["duration"] == "annually"): ?>
					Tilbagevendende betaling hvert år.
					<? else: ?>
					Tilbagevendende betaling hver <?= strtolower($item["subscription_method"]["name"]) ?>.
					<? endif; ?>
				</p>
				<? endif; ?>

				<? if($item["itemtype"] != "signupfee"): ?>
				<ul class="actions">
					<? // generate delete button to item 
					print $HTML->oneButtonForm("Slet", "/butik/deleteFromCart/".$cart["cart_reference"]."/".$cart_item["id"], array(
						// "static" => true,
						"confirm-value" => "Sikker?",
						"wait-value" => "Vent ...",
						"wrapper" => "li.delete",
						"success-location" => count($cart["items"]) > 1 ? $this->url : "/butik"
					)) ?>
				</ul>
				<? endif; ?>

			</li>
			<? endforeach; ?>
		</ul>
		<? endif; ?>

		<? if($cart_pickupdates): ?>
		<ul class="pickupdates">

			<? foreach($cart_pickupdates as $pickupdate): 

				$pickupdate_cart_items = $model->getPickupdateCartItems($pickupdate["id"], ["cart_reference" => $cart["cart_reference"]]);

			?>
				<? if($pickupdate_cart_items): ?>
				
			<li class="pickupdate">
				<h4 class="pickupdate"><?= date("d.m.Y", strtotime($pickupdate["pickupdate"])) ?> – Afhentning <span class="name"><?= $department ? $department["name"] : "ukendt afdeling" ?></span></h4>

				<ul class="items">
					<? foreach($pickupdate_cart_items as $cart_item):
					$item = $IC->getItem(array("id" => $cart_item["item_id"], "extend" => array("subscription_method" => true))); 
					$price = $model->getPrice($cart_item["item_id"], array("quantity" => $cart_item["quantity"], "currency" => $cart["currency"], "country" => $cart["country"]));
					$cart_item_id = $cart_item["id"];
					?>

					<li class="item id:<?= $item["id"] ?> date:<?= date("Ymd", strtotime($pickupdate["pickupdate"])) ?>">
						<?= $model->formStart("/butik/updateCartItemQuantity/".$cart["cart_reference"]."/".$cart_item["id"], array("class" => "updateCartItemQuantity labelstyle:inject")) ?>
							<fieldset>
								<?= $model->input("quantity", array(
									"id" => "input_quantity_".$item["id"]."_".date("Ymd", strtotime($pickupdate["pickupdate"])),
									"type" => "integer",
									"value" =>  $cart_item["quantity"],
									"label" => "Antal",
									"hint_message" => "State the quantity of this item"
								)) ?>
							</fieldset>
							<ul class="actions">
								<?= $model->submit("Opdatér", array("name" => "update", "wrapper" => "li.save")) ?>
							</ul>
						<?= $model->formEnd() ?>
						<span class="x">x </span>
						<span class="name"><?= $item["name"] ?> </span>
						<span class="a">á </span>
						<span class="unit_price"><?= formatPrice($price, ["conditional_decimals" => true]) ?></span>
						<span class="total_price">
							<? // generate total price and vat to item 
							print formatPrice(array(
									"price" => $price["price"]*$cart_item["quantity"],
									"vat" => $price["vat"]*$cart_item["quantity"],
									"currency" => $cart["currency"],
									"country" => $cart["country"]
								),
								array("vat" => false)
							) ?>
						</span>

						<? if($item["subscription_method"]): ?>
						<p class="subscription_method">
							<? if($item["subscription_method"]["duration"] == "annually"): ?>
							Tilbagevendende betaling hvert år.
							<? else: ?>
							Tilbagevendende betaling hver <?= strtolower($item["subscription_method"]["name"]) ?>.
							<? endif; ?>
						</p>
						<? endif; ?>

						<ul class="actions">
							<?= $HTML->oneButtonForm("Slet", "/butik/deleteFromCart/".$cart["cart_reference"]."/$cart_item_id", [
								// "static" => true,
								"confirm-value" => "Sikker?",
								"wait-value" => "Vent ...",
								"wrapper" => "li.delete",
								"success-location" => count($cart["items"]) > 1 ? $this->url : "/butik"
								]) ?>
						</ul>
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
				<span class="total_vat">
					<?= formatPrice(array("price" => $total_cart_price["vat"], "currency" => $total_cart_price["currency"])) ?>
				</span>
			</p>
			<h3>
				<span class="name">I alt</span>
				<span class="total_price">
					<?= formatPrice($total_cart_price) ?>
				</span>
			</h3>
		</div>

		<div class="checkout">
			<ul class="actions">
				<?= $HTML->oneButtonForm("Gå til betaling", "/butik/betal", array(
					"confirm-value" => false,
					"wait-value" => "Vent venligst",
					"dom-submit" => true,
					"class" => "primary",
					"name" => "continue",
					"wrapper" => "li.continue",
				)) ?>
			</ul>
		</div>

	<? else: ?>

		<h2>Din indkøbskurv er tom</h2>
		<? if(isset($user["membership"]) && $user["membership"]): ?>
		<p>Du har ingenting i kurven endnu. <br />Gå til <a href="/butik">Grøntshoppen</a>.</p>
		<? else: ?>
		
		<p>Du har ingenting i kurven endnu. <br />Tag et kig på vores <a href="/bliv-medlem">medlemskaber</a>.</p>
		<? endif; ?>

	<? endif; ?>
	</div>

</div>
