<?php
global $action;
global $model; // SuperUser
global $UC; // User
global $SC;
$IC = new Items();

$cart_reference = $action[2];
$cart = $SC->getCarts(["cart_reference" => $cart_reference]);
$clerk_user_id = session()->value("user_id");

// Clerk is logged in
if($clerk_user_id != 1 && $cart) {
	
	$member_user_id = $cart["user_id"];
	$member_user = $model->getKbhffUser(["user_id" => $member_user_id]);
	$department = $model->getUserDepartment(["user_id" => $member_user_id]);
	$member_name = $member_user['nickname'] ? $member_user['nickname'] : $member_user['firstname'] . " " . $member_user['lastname'];
	$member_name_possesive = preg_match("/s$/", $member_name) ? $member_name."'" : $member_name."s";

	// Only get payment methods if cart has items
	if($cart && $cart["items"]) {

		// Get the total cart price
		$total_cart_price = $SC->getTotalCartPrice($cart["id"]);

		if($total_cart_price && $total_cart_price["price"] > 0) {

			// Get payment methods
			$payment_methods = $this->paymentMethods();

		}

		$cart_pickupdates = $SC->getCartPickupdates(["cart_reference" => $cart_reference]);
		$cart_items_without_pickupdate = $SC->getCartItemsWithoutPickupdate(["cart_reference" => $cart_reference]);

	}



}

// Clerk not logged in yet
else {

	// enable re-population of fields
	$clerk_username = stringOr(getPost("username"));

}

if($cart && $cart["items"]) {

	// Get the total cart price
	$total_cart_price = $SC->getTotalCartPrice($cart["id"]);
	
	if($total_cart_price && $total_cart_price["price"] > 0) {
		
		// Get payment methods
		$payment_methods = $this->paymentMethods();
		
	}
	
	$cart_pickupdates = $SC->getCartPickupdates(["cart_reference" => $cart_reference]);
	$cart_items_without_pickupdate = $SC->getCartItemsWithoutPickupdate(["cart_reference" => $cart_reference]);
	
	if($member_user_id != 1) {
		$department = $model->getUserDepartment(["user_id" => $member_user_id]);
	}
}

?>
<div class="scene cart i:cart">

<? if($cart): ?>

	<div class="c-wrapper">
		<div class="c-box obs">
			<h2 class="obs"><span class="highlight">OBS! </span>Handler på vegne af <span class="highlight"><a href="/medlemshjaelp/brugerprofil/<?= $member_user_id ?>"><?= $member_user['nickname'] ? $member_user['nickname'] : $member_user['firstname'] . " " . $member_user['lastname'] ?></a></span></h2>
		</div>
	</div>

	<?= $HTML->serverMessages(["type" => "error"]); ?>

	<div class="all_items">
		<? if($cart["items"]): ?>
		<h2>Gennemse bestilling</h2>
		<? if($cart_items_without_pickupdate): ?>
		<ul class="items">
			<? 
			// Loop through all cart items and show information and editing options of each item.
			foreach($cart_items_without_pickupdate as $cart_item):
				$item = $IC->getItem(array("id" => $cart_item["item_id"], "extend" => array("subscription_method" => true)));
				$price = $SC->getPrice($cart_item["item_id"], array("user_id" => $member_user_id, "quantity" => $cart_item["quantity"], "currency" => $cart["currency"], "country" => $cart["country"]));
			?>
			<li class="item id:<?= $item["id"] ?>">
				<? if($item["itemtype"] != "signupfee"): ?>
				<?
				// add option of updating item quantity to item 
				print $SC->formStart("/medlemshjaelp/butik/updateCartItemQuantity/".$cart["cart_reference"]."/".$cart_item["id"], array("class" => "updateCartItemQuantity labelstyle:inject")) ?>
					<fieldset>
						<?= $SC->input("quantity", array(
							"id" => "input_quantity_".$item["id"],
							"type" => "integer",
							"value" =>  $cart_item["quantity"],
							"label" => "Antal",
							"hint_message" => "State the quantity of this item"
						)) ?>
					</fieldset>
					<ul class="actions">
						<?= $SC->submit("Opdatér", array("name" => "update", "wrapper" => "li.save")) ?>
					</ul>
				<? else: ?>
				
				<span class="quantity"><?= $cart_item["quantity"] ?></span>
				<? endif; ?>
				<?= $SC->formEnd() ?>
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

				<? if($item["itemtype"] != "signupfee"): ?>
				<ul class="actions">
					<? // generate delete button to item 
					print $HTML->oneButtonForm("Slet", "/medlemshjaelp/butik/deleteFromCart/".$cart["cart_reference"]."/".$cart_item["id"], array(
						"confirm-value" => "Sikker?",
						"wait-value" => "Vent ...",
						"wrapper" => "li.delete",
						"success-location" => count($cart["items"]) > 1 ? $this->url : "/medlemshjaelp/butik/".$member_user_id
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
	
					$pickupdate_cart_items = $SC->getCartPickupdateItems($pickupdate["id"], ["cart_reference" => $cart_reference]);
	
				?>
					<? if($pickupdate_cart_items): ?>
					
				<li class="pickupdate">
					<h4 class="pickupdate"><?= date("d/m-Y", strtotime($pickupdate["pickupdate"])) ?></h4>
					<p class="department">Afhentningssted: <span class="name"><?= $department["name"] ?></span></p>
					
					<ul class="items">
						
						<? foreach($pickupdate_cart_items as $cart_item):
						$item = $IC->getItem(array("id" => $cart_item["item_id"], "extend" => array("subscription_method" => true))); 
						$price = $SC->getPrice($cart_item["item_id"], array("user_id" => $member_user_id, "quantity" => $cart_item["quantity"], "currency" => $cart["currency"], "country" => $cart["country"]));
						$cart_item_id = $cart_item["id"];
						?>
	
						<li class="item id:<?= $item["id"] ?>">
							<?= $SC->formStart("/medlemshjaelp/butik/updateCartItemQuantity/".$cart["cart_reference"]."/".$cart_item["id"], array("class" => "updateCartItemQuantity labelstyle:inject")) ?>
								<fieldset>
									<?= $SC->input("quantity", array(
										"id" => "input_quantity_".$item["id"]."_".date("Ymd", strtotime($pickupdate["pickupdate"])),
										"type" => "integer",
										"value" =>  $cart_item["quantity"],
										"label" => "Antal",
										"hint_message" => "State the quantity of this item"
									)) ?>
								</fieldset>
								<ul class="actions">
									<?= $SC->submit("Opdatér", array("name" => "update", "wrapper" => "li.save")) ?>
								</ul>
							<?= $SC->formEnd() ?>
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
								<?= $HTML->oneButtonForm("Slet", "/medlemshjaelp/butik/deleteFromCart/".$cart["cart_reference"]."/$cart_item_id", [
									"confirm-value" => "Sikker?",
									"wait-value" => "Vent ...",
									"wrapper" => "li.delete",
									"success-location" => count($cart["items"]) > 1 ? $this->url : "/medlemshjaelp/butik/".$member_user_id
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
		
		<? else: ?>

		<h2>Din indkøbskurv er tom</h2>
		<p>Du har ingenting i kurven endnu. <br />Gå til <a href="/medlemshjaelp/butik/<?= $member_user_id ?>">Grøntshoppen</a>.</p>

	<? endif; ?>
	</div>

	<? // Generate checkout button
	if($cart && $cart["items"]) :?>
	<div class="checkout">
		<ul class="actions">
			<? if($member_user["membership"]): ?>
			<li class="shop"><a class="button" href="/medlemshjaelp/butik/<?= $member_user_id ?>">Køb mere</a></li>
			<? endif; ?>
			<?= $HTML->oneButtonForm("Bekræft og gå til betaling", "/medlemshjaelp/butik/newOrderFromCart/".$cart_reference."/".$cart["id"], array(
				"confirm-value" => false,
				"wait-value" => "Vent venligst",
				"dom-submit" => true,
				"class" => "primary",
				"name" => "continue",
				"wrapper" => "li.continue",
			)) ?>
		</ul>
	</div>
<? 	endif; ?>

<? else: ?>

	<h1>Hovsa?</h1>
	<p>Denne kurv eksisterer ikke. Det kan skyldes at den er blevet omdannet til en ordre.</p>

<? endif; ?>
</div>
