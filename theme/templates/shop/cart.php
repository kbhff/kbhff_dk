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
				<?
				// add option of updating item quantity to item 
				print $model->formStart("/butik/updateCartItemQuantity/".$cart["cart_reference"]."/".$cart_item["id"], array("class" => "updateCartItemQuantity labelstyle:inject")) ?>
					<fieldset>
						<?= $model->input("quantity", array(
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
					<? if($price["price"]): ?>
					Dette køb indeholder et medlemskab.
					<? else: ?>
					Bekræft ordren for at tilmelde dig nyhedsbrevet.
					<? endif; ?>
				</p>
				<? endif; ?>

				<? if(isset($item["associated_membership_id"])):
					 $membership = $IC->getItem(["id" => $item["associated_membership_id"], "extend" => ["subscription_method" => true]]); 
				?>
				<p class="subscription_method">
					<? if($membership["subscription_method"]["duration"] == "annually"): ?>
					Tilbagevendende betaling hvert <?= strtolower($membership["subscription_method"]["name"]) ?>.
					<? else: ?>
					Tilbagevendende betaling hver <?= strtolower($membership["subscription_method"]["name"]) ?>.
					<? endif; ?>
				</p>

				<? elseif($item["subscription_method"]): ?>
				<p class="subscription_method">
					<? if($item["subscription_method"]["duration"] == "annually"): ?>
					Tilbagevendende betaling hvert <?= strtolower($item["subscription_method"]["name"]) ?>.
					<? else: ?>
					Tilbagevendende betaling hver <?= strtolower($item["subscription_method"]["name"]) ?>.
					<? endif; ?>
				</p>
				<? endif; ?>

				<ul class="actions">
					<? // generate delete button to item 
					print $HTML->oneButtonForm("Slet", "/butik/deleteFromCart/".$cart["cart_reference"]."/".$cart_item["id"], array(
						"confirm-value" => "Sikker?",
						"wait-value" => "Vent",
						"wrapper" => "li.delete",
						"static" => true
					)) ?>
				</ul>
			</li>
			<? endforeach; ?>
		</ul>
		<? endif; ?>

		<? if($cart_pickupdates): ?>
		<ul class="pickupdates">

			<? foreach($cart_pickupdates as $pickupdate): 

				$pickupdate_cart_items = $model->getCartPickupdateItems($pickupdate["id"]);

			?>
				<? if($pickupdate_cart_items): ?>
				
			<li class="pickupdate">
				<h4 class="pickupdate"><?= date("d/m-Y", strtotime($pickupdate["pickupdate"])) ?></h4>
				<p class="department">Afhentningssted: <span class="name"><?= $department ? $department["name"] : "-" ?></span></p>
				
				<ul class="items">
					
					<? foreach($pickupdate_cart_items as $cart_item):
					$item = $IC->getItem(array("id" => $cart_item["item_id"], "extend" => array("subscription_method" => true))); 
					$price = $model->getPrice($cart_item["item_id"], array("quantity" => $cart_item["quantity"], "currency" => $cart["currency"], "country" => $cart["country"]));
					$cart_item_id = $cart_item["id"];
					?>

					<li class="item id:<?= $item["id"] ?>">
						<?= $model->formStart("/butik/updateCartItemQuantity/".$cart["cart_reference"]."/".$cart_item["id"], array("class" => "updateCartItemQuantity labelstyle:inject")) ?>
							<fieldset>
								<?= $model->input("quantity", array(
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
							Tilbagevendende betaling hvert <?= strtolower($item["subscription_method"]["name"]) ?>.
							<? else: ?>
							Tilbagevendende betaling hver <?= strtolower($item["subscription_method"]["name"]) ?>.
							<? endif; ?>
						</p>
						<? endif; ?>

						<ul class="actions">
							<?= $HTML->oneButtonForm("Slet", "/butik/deleteFromCart/".$cart["cart_reference"]."/$cart_item_id", [
								"confirm-value" => "Sikker?",
								"wait-value" => "Vent",
								"wrapper" => "li.delete",
								"success-location" => "/butik"
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
