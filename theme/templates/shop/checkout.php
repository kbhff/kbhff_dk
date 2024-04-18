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
	$unpaid_membership = $UC->hasUnpaidMembership();

	// Only get payment methods if cart has items
	if($cart && $cart["items"]) {

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


		// Get address info
		$delivery_address = $UC->getAddresses(array("address_id" => $cart["delivery_address_id"]));
		$billing_address = $UC->getAddresses(array("address_id" => $cart["billing_address_id"]));

	}

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
		<?= $UC->formStart("/butik/betal?login=true", array("class" => "login labelstyle:inject")) ?>
			<?= $UC->input("login_forward", ["type" => "hidden", "value" => "/butik/betal"]); ?>
			<fieldset>
				<?= $UC->input("username", array(
					"type" => "string", 
					"label" => "Brugernavn",
					"required" => true, 
					"value" => $username, 
					"pattern" => "^(1|[0-9]{4,5}|[\+0-9\-\.\s\(\)]{5,18}|[\w\.\-_\+]+@[\w\-\.]+\.\w{2,10})$",
					"hint_message" => "Brug dit medlemsnr., email eller telefonnummer som brugernavn",
					"error_message" => "Det ligner ikke et gyldigt brugernavn",
				)); ?>
				<?= $UC->input("password", array("type" => "password", "label" => "Password", "required" => true, "hint_message" => "Type your password", "error_message" => "Your password should be between 8-20 characters.")); ?>
			</fieldset>

			<ul class="actions">
				<?= $UC->submit("Log ind", array("class" => "primary", "wrapper" => "li.login")) ?>
			</ul>
		<?= $UC->formEnd() ?>
		<p class="forgot">Har du <a href="/login/glemt" target="_blank">glemt din adgangskode</a>?</p>
		<p class="signup">Endnu ikke medlem? <a href="/bliv-medlem?clear_cookie=true">Meld dig ind nu</a>.</p>
	</div>

	<? elseif($unpaid_membership): 
		$unpaid_membership_type_dk = $unpaid_membership["type"] == "signupfee" ? "indmeldelsesgebyr" : "kontingent";
	?>
	<div class="unpaid_membership">
		<h2>Før du går videre...</h2>
		<p>Du mangler at betale <?= $unpaid_membership_type_dk ?>. Det skal betales før du kan lave en grøntsagsbestilling.</p>
		<ul class="actions">
			<li class="pay"><a href="/butik/betaling/<?= $unpaid_membership["order_no"] ?>" class="button">Betal <?= $unpaid_membership_type_dk ?> nu</a></li>
		</ul>
	</div>

	<?
	// user is already logged in, show checkout overview
	else: ?>

	<div class="all_items">
		<h2>Din kurv</h2>
		<? if($cart && $cart["items"]): ?>
		
		<? if($cart_items_without_pickupdate): ?>
		<ul class="items">
			<? foreach($cart_items_without_pickupdate as $cart_item):
				$item = $IC->getItem(array("id" => $cart_item["item_id"], "extend" => array("subscription_method" => true))); 
				$price = $model->getPrice($cart_item["item_id"], array("quantity" => $cart_item["quantity"], "currency" => $cart["currency"], "country" => $cart["country"]));
				$cart_item_id = $cart_item["id"];
			?>
			<li class="item id:<?= $item["id"] ?>">
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
					<?= $HTML->oneButtonForm("Slet", "/butik/deleteFromCart/".$cart["cart_reference"]."/$cart_item_id", [
						"confirm-value" => "Sikker?",
						"wait-value" => "Vent ...",
						"wrapper" => "li.delete",
						"success-location" => count($cart["items"]) > 1 ? $this->url : "/butik"
						]) ?>
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

				if($pickupdate_cart_items): ?>
				
			<li class="pickupdate">
				<h4 class="pickupdate"><?= date("d.m.Y", strtotime($pickupdate["pickupdate"])) ?> – Afhentning <span class="name"><?= $department ? $department["name"] : "ukendt afdeling" ?></span></h4>

				<ul class="items">
					<? foreach($pickupdate_cart_items as $cart_item):
					$item = $IC->getItem(array("id" => $cart_item["item_id"], "extend" => array("subscription_method" => true))); 
					$price = $model->getPrice($cart_item["item_id"], array("quantity" => $cart_item["quantity"], "currency" => $cart["currency"], "country" => $cart["country"]));
					$cart_item_id = $cart_item["id"];
					?>

					<li class="item id:<?= $item["id"] ?>">
						<span class="quantity"><?= $cart_item["quantity"] ?></span>
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
				<span class="total_price">
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

		<? else: ?>
		<p>Du har ingenting i kurven endnu. <br />Tag et kig på vores <a href="/bliv-medlem">medlemskaber</a>.</p>
		<? endif; ?>
	</div>


		<? 
		// Only show payment options if cart has items
		if($cart && $cart["items"] && $total_cart_price && $total_cart_price["price"] !== 0): ?>


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
					<!-- <p><?= $user_payment_method["description"] ?></p> -->
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
					<!-- <p><?= $user_payment_method["description"] ?></p> -->
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
		elseif($cart && $cart["items"] && $total_cart_price && $total_cart_price["price"] === 0): ?>


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


	<div class="contact">
		<h2>Dine brugeroplysninger </h2>
		<dl class="list">
			<dt>Fulde navn</dt>
			<dd><?= $user["firstname"] || $user["lastname"] ? $user["firstname"] . " " . $user["lastname"] : "N/A" ?></dd>
			<dt>Email</dt>
			<dd><?= $user["email"] ? $user["email"] : "N/A" ?></dd>
			<dt>Mobiltelefon</dt>
			<dd><?= $user["mobile"] ? $user["mobile"] : "N/A" ?></dd>
		</dl>
		<ul class="actions">
			<li><a href="/butik/profil" class="button">Ret oplysninger</a></li>
		</ul>
	</div>


	<? endif; ?>

</div>
