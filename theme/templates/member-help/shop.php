<?php
global $action;
global $model; // SuperUser
global $UC; // User
global $SC;
global $PC;
global $DC;
$IC = new Items();

$this->pageTitle("Grøntshoppen");


// in the memberhelp scenario, we differentiate between clerk and member
// the clerk is also a member but that is not relevant in this context
// the clerk is acting on behalf of the member
$clerk_user_id = session()->value("user_id");
$member_user_id = $action[1];


// Clerk is logged in
if($clerk_user_id != 1) {

	// get or add cart for member
	$carts = $SC->getCarts(["user_id" => $member_user_id]);
	if($carts) {
		$cart = $carts[0];
	}
	else{
		$_POST["user_id"] = $member_user_id;
		$cart = $SC->addCart(["addCart"]);
		unset($_POST);
	}

	$cart_reference = $cart["cart_reference"];
	$member_user = $model->getKbhffUser(["user_id" => $member_user_id]);
	$department = $model->getUserDepartment(["user_id" => $member_user_id]);
	if($department) {
		$member_name = $member_user['nickname'] ? $member_user['nickname'] : $member_user['firstname'] . " " . $member_user['lastname'];
		$member_name_possesive = preg_match("/s$/", $member_name) ? $member_name."'" : $member_name."s";
		$products = $DC->getDepartmentProducts($department["id"]);
		$pickupdates = $PC->getPickupdates(["after" => date("Y-m-d", strtotime("+1 week"))]);
		$department_pickupdates = $DC->getDepartmentPickupdates($department["id"]);
	}
	$orders = $SC->getOrders(["user_id" => $member_user_id]);
	$unpaid_membership = $model->hasUnpaidMembership(["user_id" => $member_user_id]);



	// Only get payment methods if cart has items
	if($cart["items"]) {

		// Get the total cart price
		$total_cart_price = $SC->getTotalCartPrice($cart["id"]);

		if($total_cart_price && $total_cart_price["price"] > 0) {

			// Get payment methods
			$payment_methods = $this->paymentMethods();

		}

		$cart_pickupdates = $SC->getCartPickupdates(["cart_reference" => $cart_reference]);
		$cart_items_without_pickupdate = $SC->getCartItemsWithoutPickupdate(["cart_reference" => $cart_reference]);
	}

	$order_items_pickupdates = false;
	if($orders) {

		// $order_items_without_pickupdates = $SC->getOrderItemsWithoutPickupdate($member_user_id);
		
		$order_items_pickupdates = $SC->getOrderItemsPickupdates($member_user_id, ["after" => date("Y-m-d")]);
	}



}

// Clerk not logged in yet
else {

	// enable re-population of fields
	$clerk_username = stringOr(getPost("username"));

}


?>
<div class="scene shop i:shop">

 	<?
	// User is not logged in yet
	if($clerk_user_id == 1): ?>


	<h1>Bestilling af grøntsager</h1>

	<div class="login">
		<h2>Log ind</h2>
		<p>Du skal logge ind før du kan fortsætte.</p>
		<?= $UC->formStart("/medlemshjaelp/butik/".$member_user_id."?login=true", array("class" => "login labelstyle:inject")) ?>
			<?= $UC->input("login_forward", ["type" => "hidden", "value" => "/medlemshjaelp/butik/".$member_user_id]); ?>
			<fieldset>
				<?= $UC->input("username", array("type" => "string", "label" => "Email or mobile number", "required" => true, "value" => $clerk_username, "pattern" => "[\w\.\-_]+@[\w\-\.]+\.\w{2,10}|([\+0-9\-\.\s\(\)]){5,18}", "hint_message" => "You can log in using either your email or mobile number.", "error_message" => "You entered an invalid email or mobile number.")); ?>
				<?= $UC->input("password", array("type" => "password", "label" => "Password", "required" => true, "hint_message" => "Type your password", "error_message" => "Your password should be between 8-20 characters.")); ?>
			</fieldset>

			<ul class="actions">
				<?= $UC->submit("Log ind", array("class" => "primary", "wrapper" => "li.login")) ?>
			</ul>
		<?= $UC->formEnd() ?>

		<p class="forgot">Har du <a href="/login/glemt" target="_blank">glemt din adgangskode</a>?</p>
	</div>

	<? 
	// member_user is inactive member
	elseif($member_user["membership"] && !$member_user["membership"]["subscription_id"]): ?>
	<div class="inactive_member">
		<h2><?= $member_name_possesive ?> medlemskab er inaktivt</h2>
		<p>For at kunne bestille grøntsager skal man <a href="/medlemshjaelp/brugerprofil/<?= $member_user_id ?>/genaktiver">genaktivere medlemskabet</a>.</p>
	</div>

	<? elseif(!$member_user["membership"]): ?>
	<div class="section not_member">
	<h2><?= $member_name ?> er ikke medlem</h2>
	<? 

	$carts = $SC->getCarts(["user_id" => $member_user_id]);
	$cart = $carts ? $carts[0] : false;

	if($cart && $SC->hasSignupfeeInCart($cart["id"])): ?>
	<p><?= $member_name ?> er endnu ikke medlem, men har et indmeldelsesgebyr i sin kurv – <a href="/medlemshjaelp/butik/kurv/<?= $cart["cart_reference"] ?>">Gå til kurv</a></p>
	<? else: ?>
	<p>Denne bruger er oprettet i systemet, men har ikke tilknyttet et medlemskab. Kontakt gerne <a href="mailto:it@kbhff.dk">IT-gruppen</a> og send dem et screenshot af dette skærmbillede.
	</p><p>Brugeren kan selv oprette et medlemskab ved at logge ind på sin egen konto med brugernavnet <em><?= $member_user["email"] ?></em>.</p>
	<? endif; ?>
	</div>
	

	<?
	// clerk is already logged in, show memberhelp-shop
	else: ?>

	<div class="c-wrapper">
		<div class="c-box obs">
			<h2 class="obs"><span class="highlight">OBS! </span>Handler på vegne af <span class="highlight"><a href="/medlemshjaelp/brugerprofil/<?= $member_user_id ?>"><?= $member_name ?></a></span></h2>
		</div>
	</div>
	
	<h1>Bestilling af grøntsager</h1>

	<div class="c-wrapper">

		<div class="c-two-thirds">

			<? if($unpaid_membership && $unpaid_membership["type"] == "signupfee"): ?>
			<div class="c-box alert unpaid signupfee">
				<h3>OBS! <?= $member_name ?> mangler at betale sit indmeldelsesgebyr</h3>
				<p>Indmeldelsesgebyret skal betales før medlemmet kan bestille grøntsager.</p>
				<ul class="actions">
					<li class="pay"><a href="/medlemshjaelp/betaling/<?= $unpaid_membership["order_no"] ?>" class="button">Betal indmeldelsesgebyr nu</a></li>
				</ul>
			</div>
			<? elseif($unpaid_membership && $unpaid_membership["type"] == "membership"): ?>
			<div class="c-box alert unpaid membership">
				<h3>OBS! <?= $member_name ?> mangler at betale kontingent</h3>
				<p>Kontingentet skal betales før medlemmet kan bestille grøntsager.</p>
				<ul class="actions">
					<li class="pay"><a href="/medlemshjaelp/betaling/<?= $unpaid_membership["order_no"] ?>" class="button">Betal kontingent nu</a></li>
				</ul>
			</div>

			<? elseif($products): ?>
			<ul class="products">

				<? foreach($products as $product): 
					$price = $SC->getPrice($product["id"], ["user_id" => $member_user_id]);
					$media = $IC->sliceMediae($product, "single_media");

				?>

				<li class="product">
					<div class="c-box">

						<div class="product">
							<? if($media): ?>
							<div class="image item_id:<?= $media["item_id"] ?> format:<?= $media["format"] ?> variant:<?= $media["variant"] ?>"></div>
							<? else: ?>
							<div class="image"></div>
							<? endif; ?>

							<h3><span class="name"><?= $product["name"] ?></span> <span class="price"><?= formatPrice($price, ["conditional_decimals" => true]) ?></span></h3>
							<p><?= $product["description"] ?></p>
						</div>

						<h4 class="pickupdates">Tilføj bestillinger til afhentning på bestemte datoer:</h4>
						<? if($pickupdates): ?>
						<div class="pickupdates">
							<ul class="pickupdates">
								<? foreach($pickupdates as $pickupdate): 
									$product_available = false;
								
									// check if product is available on pickupdate
									if($product["end_availability_date"]) {
										if($pickupdate["pickupdate"] >= $product["start_availability_date"] && $pickupdate["pickupdate"] <= $product["end_availability_date"]) {
											$product_available = true;
										}
									}
									else {
										if($pickupdate["pickupdate"] >= $product["start_availability_date"]) {
											$product_available = true;
										}
									}
								?>

								<li class="pickupdate">

								<? // check if department is open on given pickupdate ?>
								<? if(arrayKeyValue($department_pickupdates, "id", $pickupdate["id"])): ?>

									<? // check product availability ?>
									<? if($product_available): ?>
							
									<?= $HTML->oneButtonForm("+", "/medlemshjaelp/butik/addToCart/".$cart_reference, [
										"confirm-value" => false,
										"wait-value" => "Vent",
										"inputs" => [
											"item_id" => $product["id"],
											"quantity" => 1,
											"pickupdate_id" => $pickupdate["id"]
										],
										"wrapper" => "div.add",
										// "success-location" => "/medlemshjaelp/butik/".$member_user_id
									]) ?>
							
									<? else: ?>

									<div class="unavailable" title="Ikke tilgængelig">Ikke tilgængelig</div>

									<? endif; ?>	

								<? else: ?>

									<div class="closed" title="Afdelingen er lukket">Afdelingen er lukket</div>

								<? endif; ?>

									<p class="date"><?= date("d/m", strtotime($pickupdate["pickupdate"])) ?></p>

								</li>

								<? endforeach; ?>
							</ul>

						</div>
						<? else: ?>
						<p class="no_dates">Ingen aktuelle afhentningsdage.</p>
						<? endif; ?>

					</div>
				</li>

				<? endforeach; ?>
			</ul>
			
			<? else: ?>
			<p>Ingen produkter til salg i <?= $department ? $department["name"] : "ukendt afdeling" ?>.</p>
			<? endif; ?>
			

		</div>

		<? if(!$unpaid_membership): ?>
		<div class="c-one-third sidebar">
		
			<div class="cart c-primary-box">
				<h3>Indkøbskurv</h3>
				<? if($cart["items"]): ?>
					<? if($cart_items_without_pickupdate): ?>
				<ul class="items">
					<? 
						// Loop through all cart items and show information and editing options of each item.
						foreach($cart_items_without_pickupdate as $cart_item):
							$item = $IC->getItem(array("id" => $cart_item["item_id"], "extend" => array("subscription_method" => true)));
							$price = $SC->getPrice($cart_item["item_id"], array("user_id" => $member_user_id, "quantity" => $cart_item["quantity"], "currency" => $cart["currency"], "country" => $cart["country"]));
							$cart_item_id = $cart_item["id"];
					?>
					<li class="item id:<?= $item["id"] ?>">
						<span class="quantity"><?= $cart_item["quantity"] ?></span>
						<span class="x">x </span>
						<span class="name"><?= $item["name"] ?> </span>
						<span class="a">á </span>
						<span class="unit_price"><?= formatPrice($price, ["conditional_decimals" => true]) ?></span>
					</li>
					<? 	endforeach; ?>
				</ul>
					<? endif; ?>

					<? if($cart_pickupdates): ?>
				<ul class="pickupdates">
					
					<? foreach($cart_pickupdates as $pickupdate): 

						$pickupdate_cart_items = $SC->getCartPickupdateItems($pickupdate["id"], ["cart_reference" => $cart_reference]);

					?>
						<? if($pickupdate_cart_items): ?>
						
					<li class="pickupdate">
						<h4 class="pickupdate"><?= date("d/m-Y", strtotime($pickupdate["pickupdate"])) ?> – <span class="name"><?= $department["name"] ?></span></h4>

						<ul class="items">

							<? foreach($pickupdate_cart_items as $cart_item):
							$item = $IC->getItem(array("id" => $cart_item["item_id"], "extend" => array("subscription_method" => true))); 
							$price = $SC->getPrice($cart_item["item_id"], array("user_id" => $member_user_id, "quantity" => $cart_item["quantity"], "currency" => $cart["currency"], "country" => $cart["country"]));
							$cart_item_id = $cart_item["id"];
							?>

							<li class="item id:<?= $item["id"] ?>">
								<span class="quantity"><?= $cart_item["quantity"] ?></span>
								<span class="x">x </span>
								<span class="name"><?= $item["name"] ?> </span>
								<span class="a">á </span>
								<span class="unit_price"><?= formatPrice($price, ["conditional_decimals" => true]) ?></span>
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

				<ul class="actions">
					<li><a class="button" href="/medlemshjaelp/butik/kurv/<?= $cart_reference ?>">Gå til kurven</a></li>
				</ul>

				<? else: ?>

				<p><?= $member_name ?> har ingenting i kurven endnu. </p>

					<? if(!$unpaid_membership): ?>
				<p>Føj en eller flere varer til kurven først.</p>
					<? endif; ?>

				<? endif; ?>
			</div>

			<div class="orders c-box">
				<h3>Aktuelle bestillinger</h3>
				<? if($order_items_pickupdates): ?>
				<ul class="list">
					<li class="header">
						<span class="pickupdate">Afh.dato</span>
						<span class="product">Vare(r)</span>
					</li>
					<? foreach($order_items_pickupdates as $pickupdate): 
						$pickupdate_order_items = $SC->getPickupdateOrderItems($pickupdate["id"], ["user_id" => $member_user_id]);
						if($pickupdate_order_items):
							foreach($pickupdate_order_items as $order_item): ?>
					<li class="listing">
						<span class="pickupdate"><?= date("d/m-Y", strtotime($pickupdate["pickupdate"])) ?></span>
						<? if($order_item["quantity"] > 1): ?>
						<span class="quantity"><?= $order_item["quantity"] ?></span>
						<span class="x"><?= " x " ?></span>
						<? endif; ?>
						<span class="product"><?= $order_item["name"] ?></span>
					</li>
						<? endforeach;
						endif;
					endforeach; ?>
				</ul>
				<? else: ?>
				<p><?= $member_name ?> har ingen aktuelle grøntsagsbestillinger.</p>
				<? endif; ?>

				<!-- <p>Gå til <a href="/medlemshjaelp/brugerprofil/<?= $member_user_id ?>" class="profile"><?= $member_name_possesive ?> side</a> for at se gamle bestillinger og rette datoer for aktuelle bestillinger.</p> -->
				<p>Gå til <a href="/medlemshjaelp/brugerprofil/<?= $member_user_id ?>" class="profile"><?= $member_name_possesive ?> side</a> for at rette datoer for aktuelle bestillinger.</p>

			</div>
		</div>
		<? endif; ?>

	</div>
	






	<? endif; ?>



</div>
