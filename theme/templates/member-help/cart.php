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

if($cart["items"]) {

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
<? if($cart): ?>
<div class="scene cart">

	<div class="c-wrapper">
		<div class="c-box obs">
			<h2 class="obs"><span class="highlight">OBS! </span>Handler på vegne af <span class="highlight"><a href="/medlemshjaelp/brugerprofil/<?= $member_user_id ?>"><?= $member_user['nickname'] ? $member_user['nickname'] : $member_user['firstname'] . " " . $member_user['lastname'] ?></a></span></h2>
		</div>
	</div>

	<?
	//print all stored messages
	print $HTML->serverMessages(["type" => "error"]);
	?>

	<div class="all_items">
		<? if($cart["items"]): ?>
		<h2>Gennemse bestilling</h2>
		<? if($cart_items_without_pickupdate): ?>
		<ul class="items">
			<? 
			// Loop through all cart items and show information and editing options of each item.
			foreach($cart_items_without_pickupdate as $cart_item):
				$item = $IC->getItem(array("id" => $cart_item["item_id"], "extend" => array("subscription_method" => true)));
				$price = $SC->getPrice($cart_item["item_id"], array("quantity" => $cart_item["quantity"], "currency" => $cart["currency"], "country" => $cart["country"]));
			?>
			<li class="item id:<?= $item["id"] ?>">
				<h3>
					<?
					// add option of updating item quantity to item 
					print $SC->formStart("/medlemshjaelp/butik/updateCartItemQuantity/".$cart["cart_reference"]."/".$cart_item["id"], array("class" => "updateCartItemQuantity labelstyle:inject")) ?>
						<fieldset>
							<?= $SC->input("quantity", array(
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
					<span class="unit_price"><?= formatPrice($price) ?></span>
					<span class="total_price">
						<? // generate total price and vat to item 
						print formatPrice(array(
								"price" => $price["price"]*$cart_item["quantity"],
								"vat" => $price["vat"]*$cart_item["quantity"],
								"currency" => $cart["currency"],
								"country" => $cart["country"]
							),
							array("vat" => true)
						) ?>
					</span>
				</h3>
				<? // print subscription information 
				if($item["subscription_method"] && $price["price"]): ?>
				<p class="subscription_method">
					Betaling gentages hver <?= strtolower($item["subscription_method"]["name"]) ?>.
				</p>
				<? endif; ?>

				<? // print membership information
				if($item["itemtype"] == "signupfee"): ?>
				<p class="membership">
					Dit køb inkluderer et medlemskab.
				</p>
				<? endif; ?>

				<ul class="actions">
					<? // generate delete button to item 
					print $HTML->oneButtonForm("Slet", "/medlemshjaelp/butik/deleteFromCart/".$cart["cart_reference"]."/".$cart_item["id"], array(
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
	
					$pickupdate_cart_items = $SC->getCartPickupdateItems($pickupdate["id"], ["cart_reference" => $cart_reference]);
	
				?>
					<? if($pickupdate_cart_items): ?>
					
				<li class="pickupdate">
					<h4 class="pickupdate"><?= date("d/m-Y", strtotime($pickupdate["pickupdate"])) ?></h4>
					<p class="department">Afhentningssted: <span class="name"><?= $department["name"] ?></span></p>
					
					<ul class="items">
						
						<? foreach($pickupdate_cart_items as $cart_item):
						$item = $IC->getItem(array("id" => $cart_item["item_id"], "extend" => array("subscription_method" => true))); 
						$price = $SC->getPrice($cart_item["item_id"], array("quantity" => $cart_item["quantity"], "currency" => $cart["currency"], "country" => $cart["country"]));
						$cart_item_id = $cart_item["id"];
						?>
	
						<li class="item id:<?= $item["id"] ?>">
							<p>
								<?= $SC->formStart("/medlemshjaelp/butik/updateCartItemQuantity/".$cart["cart_reference"]."/".$cart_item["id"], array("class" => "updateCartItemQuantity labelstyle:inject")) ?>
									<fieldset>
										<?= $SC->input("quantity", array(
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
							</p>
							<ul class="actions">
								<?= $HTML->oneButtonForm("Slet", "/medlemshjaelp/butik/deleteFromCart/".$cart["cart_reference"]."/$cart_item_id", [
									"confirm-value" => "Sikker?",
									"wrapper" => "li.delete",
									"success-location" => "/medlemshjaelp/butik/kurv/".$cart_reference
									]) ?>
							</ul>
						</li>
	
						<? endforeach; ?>
					</ul>
				</li>
	
					<? endif; ?>
				<? endforeach; ?>
			</ul>
			<div class="total">
				<h3>
					<span class="name">I alt</span>
					<span class="total_price">
						<?= formatPrice($total_cart_price) ?>
					</span>
				</h3>
			</div>
		<? endif; ?>
		
		<? else: ?>
		<h2>Din indkøbskurv er tom</h2>
		<p>Gå til <a href="/bliv-medlem">medlemskaber </a>for at se, hvad vi tilbyder.</p>
		<ul class="items">
			<li class="total">
				<h3>
					<span class="name">Total</span>
					<span class="total_price">
						<?= formatPrice($SC->getTotalCartPrice($cart["id"]), array("vat" => true)) ?>
					</span>
				</h3>
			</li>
		</ul>
		<? endif; ?>
	</div>

	<? // Generate checkout button
	if($cart["items"]) :?>
	<div class="checkout">
		<ul class="actions">
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
</div>
<? else: ?>
<div>
	<h1>Hovsa?</h1>
	<p>Denne kurv eksisterer ikke. Det kan skyldes at den er blevet omdannet til en ordre.</p>
</div>

<? endif; ?>

