<?php
global $action;
global $model;
$IC = new Items();
$UC = new User();

$this->pageTitle("Betaling");


// get current user id
$user_id = session()->value("user_id");


// get current cart
$cart = $model->getCart();
// debug([$cart]);

$membership = false;
// attempt to find membership in cart
if($cart && $cart["items"]) {

	foreach($cart["items"] as $cart_item) {

		$item = $IC->getItem(array("id" => $cart_item["item_id"], "extend" => true));
		if($item["itemtype"] == "membership") {
			$membership = $item["name"];
			break;
		}

	}

}

// User is logged in
if($user_id != 1) {


	// update user on cart
	$_POST["user_id"] = $user_id;
	$model->updateCart(array("updateCart"));
	$cart = $model->getCart();


	$user = $UC->getUser();
	$department = $UC->getUserDepartment();

	// Only get payment methods if cart has items
	if($cart["items"]) {
		
		// Get the total cart price
		$total_cart_price = $model->getTotalCartPrice($cart["id"]);
		
		if($total_cart_price && $total_cart_price["price"] > 0) {
			
			// Get payment methods
			$payment_methods = $this->paymentMethods();
			
			// Get payment methods
			$user_payment_methods = $UC->getPaymentMethods(["extend" => true]);
			
		}
		
		$cart_pickupdates = $model->getCartPickupdates();
		$cart_items_without_pickupdate = $model->getCartItemsWithoutPickupdate();

	}


	// Get address info
	$delivery_address = $UC->getAddresses(array("address_id" => $cart["delivery_address_id"]));
	$billing_address = $UC->getAddresses(array("address_id" => $cart["billing_address_id"]));

}

// User not logged in yet
else {

	// enable re-population of fields
	$username = stringOr(getPost("username"));
	$firstname = stringOr(getPost("firstname"));
	$lastname = stringOr(getPost("lastname"));
	$email = stringOr(getPost("email"));
	$mobile = stringOr(getPost("mobile"));
	$terms = stringOr(getPost("terms"));
	$maillist = stringOr(getPost("maillist"));

}

// debug([$user_id, $cart, $membership]);

?>
<div class="scene checkout i:checkout">
	<h1>Betaling</h1>


	<?= $HTML->serverMessages() ?>


	<?
	// User is not logged in yet
	if($user_id == 1): ?>


	<div class="login">
		<h2>Log ind</h2>
		<p>Log ind nu, hvis du allerede er medlem.</p>
		<?= $UC->formStart("/butik/betaling?login=true", array("class" => "login labelstyle:inject")) ?>
			<?= $UC->input("login_forward", ["type" => "hidden", "value" => "/butik/betaling"]); ?>
			<fieldset>
				<?= $UC->input("username", array("type" => "string", "label" => "Email or mobile number", "required" => true, "value" => $username, "pattern" => "[\w\.\-_]+@[\w\-\.]+\.\w{2,10}|([\+0-9\-\.\s\(\)]){5,18}", "hint_message" => "You can log in using either your email or mobile number.", "error_message" => "You entered an invalid email or mobile number.")); ?>
				<?= $UC->input("password", array("type" => "password", "label" => "Password", "required" => true, "hint_message" => "Type your password", "error_message" => "Your password should be between 8-20 characters.")); ?>
			</fieldset>

			<ul class="actions">
				<?= $UC->submit("Log ind", array("class" => "primary", "wrapper" => "li.login")) ?>
				<li class="forgot">Har du <a href="/login/forgot" target="_blank">glemt dit password</a>?</li>
			</ul>
		<?= $UC->formEnd() ?>
	</div>

	<div class="signup">
		<p>Eller <a href="/bliv-medlem">bliv medlem</a> nu.</p>
	</div>


	<?
	// user is already logged in, show checkout overview
	else: ?>

	<div class="contact">
		<h2>Dine brugeroplysninger <a href="/butik/profil">(Redigér)</a></h2>
		<dl class="list">
			<dt>Fulde navn</dt>
			<dd><?= $user["firstname"] ?> <?= $user["lastname"] ?></dd>
			<dt>Email</dt>
			<dd><?= $user["email"] ?></dd>
			<dt>Mobiltelefon</dt>
			<dd><?= $user["mobile"] ?></dd>
		</dl>
	</div>

	<div class="all_items">
		<h2>Din kurv <a href="/butik/kurv">(Redigér)</a></h2>
		<? if($cart["items"] && $cart_items_without_pickupdate): ?>
		<ul class="items">
			<? foreach($cart_items_without_pickupdate as $cart_item):
				$item = $IC->getItem(array("id" => $cart_item["item_id"], "extend" => array("subscription_method" => true))); 
				$price = $model->getPrice($cart_item["item_id"], array("quantity" => $cart_item["quantity"], "currency" => $cart["currency"], "country" => $cart["country"]));
				$cart_item_id = $cart_item["id"];
			?>
			<li class="item id:<?= $item["id"] ?>">
				<p>
					<span class="quantity"><?= $cart_item["quantity"] ?></span>
					<span class="x">x </span>
					<span class="name"><?= $item["name"] ?> </span>
					<span class="a">á </span>
					<span class="unit_price"><?= formatPrice($price) ?></span>
					<span class="total_price">
						<?= formatPrice(array(
								"price" => $price["price"]*$cart_item["quantity"], 
								"vat" => $price["vat"]*$cart_item["quantity"], 
								"currency" => $cart["currency"], 
								"country" => $cart["country"]
							), 
							array("vat" => true)
						) ?>
					</span>
				</p>
				<? if($item["subscription_method"] && $price["price"]): ?>
				<p class="subscription_method">
					Tilbagevendende betaling hver <?= strtolower($item["subscription_method"]["name"]) ?>.
				</p>
				<? endif; ?>

				<? if($item["itemtype"] == "membership"): ?>
				<p class="membership">
					<? if($price["price"]): ?>
					Dette køb omfatter et medlemskab.
					<? else: ?>
					Bekræft ordren for at tilmelde dig nyhedsbrevet.
					<? endif; ?>
				</p>
				<? endif; ?>
				
				<ul class="actions">
					<?= $HTML->oneButtonForm("Slet", "/butik/deleteFromCart/".$cart["cart_reference"]."/$cart_item_id", [
						"confirm-value" => "Sikker?",
						"wrapper" => "li.delete",
						"success-location" => "/butik"
						]) ?>
				</ul>
				
			</li>
			<? endforeach; ?>
		</ul>
		<? if($cart["items"] && $cart_pickupdates): ?>
		<ul class="pickupdates">
					
			<? foreach($cart_pickupdates as $pickupdate): 

				$pickupdate_cart_items = $model->getCartPickupdateItems($pickupdate["id"]);

			?>
				<? if($pickupdate_cart_items): ?>
				
			<li class="pickupdate">
				<h4 class="pickupdate"><?= date("d/m-Y", strtotime($pickupdate["pickupdate"])) ?></h4>
				<p class="department">Afhentningssted: <span class="name"><?= $department["name"] ?></span></p>
				
				<ul class="items">
					
					<? foreach($pickupdate_cart_items as $cart_item):
					$item = $IC->getItem(array("id" => $cart_item["item_id"], "extend" => array("subscription_method" => true))); 
					$price = $model->getPrice($cart_item["item_id"], array("quantity" => $cart_item["quantity"], "currency" => $cart["currency"], "country" => $cart["country"]));
					$cart_item_id = $cart_item["id"];
					?>

					<li class="item id:<?= $item["id"] ?>">
						<p>
							<span class="quantity"><?= $cart_item["quantity"] ?></span>
							<span class="x">x </span>
							<span class="name"><?= $item["name"] ?> </span>
							<span class="a">á </span>
							<span class="unit_price"><?= formatPrice($price, ["conditional_decimals" => true]) ?></span>
						</p>
						<ul class="actions">
							<?= $HTML->oneButtonForm("Slet", "/butik/deleteFromCart/".$cart["cart_reference"]."/$cart_item_id", [
								"confirm-value" => "Sikker?",
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
		<p>Du har ingenting i kurven endnu. <br />Tag et kig på vores <a href="/memberships">medlemskaber</a>.</p>
		<? endif; ?>
	</div>


	<? 
	// Only show payment options if cart has items
	if($cart["items"] && $total_cart_price && $total_cart_price["price"] !== 0): ?>


	<div class="payment_method">
		<h2>Vælg en betalingsmetode</h2>

		<? if($user_payment_methods): ?>
			<h3>Dine betalingsmetoder</h3>
			<p>Vælg en af dine eksisterende betalingsmetoder for at fortsætte behandlingen af denne ordre.</p>
			<ul class="payment_methods">

			<? foreach($user_payment_methods as $user_payment_method): ?>

				<? if($user_payment_method && $user_payment_method["cards"]): ?>

					<? foreach($user_payment_method["cards"] as $card): ?>
				<li class="payment_method user_payment_method<?= $user_payment_method["classname"] ? " ".$user_payment_method["classname"] : "" ?>">
					<ul class="actions">
						<?= $HTML->oneButtonForm(
						"Betal ordre med kort, der ender på " . $card["last4"], 
						"/butik/confirmCartAndSelectUserPaymentMethod",
						array(
							"inputs" => array(
								"cart_id" => $cart["id"], 
								"user_payment_method_id" => $user_payment_method["id"], 
								"payment_method_id" => $user_payment_method["payment_method_id"],
								"gateway_payment_method_id" => $card["id"]
							),
							"confirm-value" => false,
							"wait-value" => "Vent venligst",
							"dom-submit" => true,
							"class" => "primary",
							"name" => "continue",
							"wrapper" => "li.continue.".$user_payment_method["classname"],
						)) ?>
					</ul>
					<p><?= $user_payment_method["description"] ?></p>
				</li>
					<? endforeach; ?>

				<? else: ?>
				<li class="payment_method user_payment_method<?= $user_payment_method["classname"] ? " ".$user_payment_method["classname"] : "" ?>">
					<ul class="actions">
						<?= $HTML->oneButtonForm(
						"Betal ordre med " . $user_payment_method["name"], 
						"/butik/confirmCartAndSelectUserPaymentMethod",
						array(
							"inputs" => array(
								"cart_id" => $cart["id"], 
								"user_payment_method_id" => $user_payment_method["id"], 
								"payment_method_id" => $user_payment_method["payment_method_id"]
							),
							"confirm-value" => false,
							"wait-value" => "Vent venligst",
							"dom-submit" => true,
							"class" => "primary",
							"name" => "continue",
							"wrapper" => "li.continue.".$user_payment_method["classname"],
						)) ?>
					</ul>
					<p><?= $user_payment_method["description"] ?></p>
				</li>
				<? endif; ?>

			<? endforeach; ?>

			</ul>
		<? endif; ?>

		<? if($payment_methods): ?>
			<h3>Vores <?= $user_payment_methods ? "øvrige " : "" ?>betalingsmuligheder</h3>
			<p><?= $user_payment_methods ? "Eller v" : "V" ?>ælg en betalingsmetode til forsat behandling af din ordre.</p>
			<ul class="payment_methods">

			<? foreach($payment_methods as $payment_method): ?>
				<? if($payment_method["state"] === "public"): ?>

				<li class="payment_method<?= $payment_method["classname"] ? " ".$payment_method["classname"] : "" ?>">

					<ul class="actions">
						<?= $HTML->oneButtonForm(
						"Betal med " . $payment_method["name"], 
						"/butik/confirmCartAndSelectPaymentMethod", 
						array(
							"inputs" => array(
								"cart_id" => $cart["id"], 
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
	</div>


	<? 
	// Cart has items but total price is 0 – skip payment and confirm order
	elseif($cart["items"] && $total_cart_price && $total_cart_price["price"] === 0): ?>


	<div class="confirm">
		<h2>Bekræft din ordre</h2>
		<p>Tjek, at indholdet af din kurv er korrekt, og bekræft ordren for at afslutte processen. </p>

		<ul class="actions">
			<?= $HTML->oneButtonForm("Bekræft din ordre", "/butik/confirmOrder/".$cart["cart_reference"], array(
				"confirm-value" => false,
				"wait-value" => "Bekræfter",
				"dom-submit" => true,
				"class" => "primary",
				"name" => "continue",
				"wrapper" => "li.continue",
			)) ?>
		</ul>
	</div>


	<? endif; ?>



	<div class="delivery">
		<h2>Leveringsadresse <a href="/butik/adresse/levering">(Redigér)</a></h2>

		<? if($cart["delivery_address_id"]): ?>
		<dl class="list">
			<dt>Navn</dt>
			<dd><?= $delivery_address["address_name"] ?></dd>
			<dt>Att</dt>
			<dd><?= $delivery_address["att"] ?></dd>
			<dt>Adresse 1</dt>
			<dd><?= $delivery_address["address1"] ?></dd>
			<dt>Adresse 2</dt>
			<dd><?= $delivery_address["address2"] ?></dd>
			<dt>Postnummer og by</dt>
			<dd><?= $delivery_address["postal"] ?> <?= $delivery_address["city"] ?></dd>
			<dt>Land</dt>
			<dd><?= $delivery_address["country"] ?></dd>
		</dl>

		<? else: ?>

		<p>Du kan <a href="/butik/adresse/levering">tilføje en leveringsadresse</a>, hvis du vil have den vist på din faktura, men det er ikke et krav.</p>
		
		<? endif; ?>
	</div>

	<div class="billing">
		<h2>Faktureringsadresse <a href="/butik/adresse/fakturering">(Redigér)</a></h2>

		<? if($cart["billing_address_id"]): ?>
		<dl class="list">
			<dt>Navn</dt>
			<dd><?= $billing_address["address_name"] ?></dd>
			<dt>Att</dt>
			<dd><?= $billing_address["att"] ?></dd>
			<dt>Adresse 1</dt>
			<dd><?= $billing_address["address1"] ?></dd>
			<dt>Adresse 2</dt>
			<dd><?= $billing_address["address2"] ?></dd>
			<dt>Postnummer og by</dt>
			<dd><?= $billing_address["postal"] ?> <?= $billing_address["city"] ?></dd>
			<dt>Stat</dt>
			<dd><?= $billing_address["state"] ?></dd>
			<dt>Land</dt>
			<dd><?= $billing_address["country"] ?></dd>
		</dl>
		<? else: ?>

		<p>Du kan <a href="/butik/adresse/fakturering">tilføje en faktureringsadresse</a>, hvis du vil have den vist på din faktura, men det er ikke et krav. </p>
		
		<? endif; ?>
	</div>

	<? endif; ?>

</div>
