<?php
global $action;
global $model;
global $PC;
global $DC;
$IC = new Items();
$UC = new User();

$this->pageTitle("Grøntshoppen");


// get current user id
$user_id = session()->value("user_id");


// get current cart
$cart = $model->getCart();
// debug([$cart]);
$products = false;

// User is logged in
if($user_id != 1) {


	// update user on cart
	if($cart) {
		$_POST["user_id"] = $user_id;
		$model->updateCart(array("updateCart"));
		$cart = $model->getCart();
	}
	// if(!$cart) {
	// 	$cart = $model->addCart(["addCart"]);
	// }


	$user = $UC->getUser();
	$user_department = $UC->getUserDepartment();
	
	if($user_department) {
		$products = $DC->getDepartmentProducts($user_department["id"]);
		$pickupdates = $PC->getPickupdates(["after" => date("Y-m-d", strtotime("+1 week"))]);
		$user_department_pickupdates = $DC->getDepartmentPickupdates($user_department["id"]);
		
	}
	$orders = $model->getOrders();
	$unpaid_membership = $UC->hasUnpaidMembership();

	$cart_pickupdates = false;
	$cart_items_without_pickupdate = false;
	$order_items_pickupdates = false;

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

		$cart_pickupdates = $model->getCartPickupdates($cart["id"]);
		$cart_items_without_pickupdate = $model->getCartItemsWithoutPickupdate();

	}

	if($orders) {
		
		$order_items_pickupdates = $model->getOrderItemsPickupdates($user_id, ["after" => date("Y-m-d")]);
	}



}

// User not logged in yet
else {

	// enable re-population of fields
	$username = stringOr(getPost("username"));
	// $firstname = stringOr(getPost("firstname"));
	// $lastname = stringOr(getPost("lastname"));
	// $email = stringOr(getPost("email"));
	// $mobile = stringOr(getPost("mobile"));
	// $terms = stringOr(getPost("terms"));
	// $maillist = stringOr(getPost("maillist"));

}

// debug([$user_id, $cart, $membership]);

?>
<div class="scene shop i:shop">

	<div class="banner i:banner variant:random format:jpg"></div>
	
	<h1>Bestilling af grøntsager</h1>


	<?
	// User is not logged in yet
	if($user_id == 1): ?>


	<div class="login">
		<h2>Log ind</h2>
		<p>Log ind nu, hvis du allerede er medlem.</p>

		<?= $HTML->serverMessages(["type" => "error"]) ?>

		<?= $UC->formStart("/butik?login=true", array("class" => "login labelstyle:inject")) ?>
			<?= $UC->input("login_forward", ["type" => "hidden", "value" => "/butik"]); ?>
			<fieldset>
				<?= $UC->input("username", array("type" => "string", "label" => "Email or mobile number", "required" => true, "value" => $username, "pattern" => "[\w\.\-_]+@[\w\-\.]+\.\w{2,10}|([\+0-9\-\.\s\(\)]){5,18}", "hint_message" => "You can log in using either your email or mobile number.", "error_message" => "You entered an invalid email or mobile number.")); ?>
				<?= $UC->input("password", array("type" => "password", "label" => "Password", "required" => true, "hint_message" => "Type your password", "error_message" => "Your password should be between 8-20 characters.")); ?>
			</fieldset>

			<ul class="actions">
				<?= $UC->submit("Log ind", array("class" => "primary", "wrapper" => "li.login")) ?>
			</ul>
		<?= $UC->formEnd() ?>

		<p class="forgot">Har du <a href="/login/glemt" target="_blank">glemt din adgangskode</a>?</p>
		<p class="signup">Endnu ikke medlem? <a href="/bliv-medlem">Meld dig ind nu</a>.</p>
	</div>
	
	<? 
	// user is inactive member
	elseif($user["membership"] && !$user["membership"]["subscription_id"]): ?>
	<div class="inactive_member">
		<h2>Dit medlemskab er inaktivt</h2>
		<p>For at kunne bestille grøntsager skal du først <a href="/profil/medlemskab/genaktiver">genaktivere dit medlemskab</a>.</p>
	</div>

	<? elseif(!$user["membership"]): ?>
	<div class="section not_member">
		<h2>Du er ikke medlem</h2>
		<? 
		$cart = $model->getCart();
		if($cart && $model->hasSignupfeeInCart($cart["id"])): ?>
		<p>Du er endnu ikke medlem, men du har et indmeldelsesgebyr i din kurv.</p>
		<ul class="actions">
			<li class="pay"><a href="/butik/betal" class="button primary">Gå til betaling</a></li>
		</ul>
		<? else: ?>
		<p>Meld dig ind nu – <a href="/bliv-medlem">se vores medlemskaber her</a>.</p>
		<? endif; ?>

	</div>


	<?
	// user is already logged in, show checkout overview
	else: ?>

	<?= $HTML->serverMessages(["type" => "error"]) ?>

	<div class="c-wrapper">

		<div class="c-two-thirds">
			
			<? // products exist ?>
			<? if($products): ?>

				<? // pickupdate(s) exist that are at least 1 week in the future ?>
				<? if($pickupdates): ?> 

					<ul class="products">
					<? foreach ($products as $product): ?>
						
						<?
						$product_departments = $DC->getProductDepartments($product["id"]);
						$first_possible_pickupdate = date("Y-m-d", strtotime($product["start_availability_date"]." Wednesday"));

						$product["available_at"] = [];

						$price = $model->getPrice($product["id"]);
						$media = $IC->sliceMediae($product, "single_media");
						?>

						<? // product is stocked in at least one department ?>
						<? if($product_departments): ?>
							<?
							foreach ($product_departments as $product_department) {
								
								$product_department_pickupdates = $DC->getDepartmentPickupdates($product_department["id"], ["after" => date("Y-m-d")]); 
								
								// loop through system pickupdates
								foreach ($pickupdates as $pickupdate) {
									
									// department is open on pickupdate
									if(arrayKeyValue($product_department_pickupdates, "id", $pickupdate["id"]) !== false) {
										
										
										if($product["end_availability_date"]) {

											// product is available in department on pickupdate
											if($first_possible_pickupdate <= $pickupdate["pickupdate"] && $product["end_availability_date"] >= $pickupdate["pickupdate"]) {
												$product["departments"][$product_department["id"]]["pickupdates"][$pickupdate["id"]]["status"] = "available";
												
												if(array_search($product_department["id"], $product["available_at"]) === false) {
													$product["available_at"][] = $product_department["id"];
												}
											}
											else {
												$product["departments"][$product_department["id"]]["pickupdates"][$pickupdate["id"]]["status"] = "unavailable";
											}
										}
										else {
											
											// product is available in department on pickupdate
											if($first_possible_pickupdate <= $pickupdate["pickupdate"]) {

												$product["departments"][$product_department["id"]]["pickupdates"][$pickupdate["id"]]["status"] = "available";

												if(array_search($product_department["id"], $product["available_at"]) === false) {
													$product["available_at"][] = $product_department["id"];
												}
											}
											else {
												$product["departments"][$product_department["id"]]["pickupdates"][$pickupdate["id"]]["status"] = "unavailable";
											}

										}
									}
									else {
										$product["departments"][$product_department["id"]]["pickupdates"][$pickupdate["id"]]["status"] = "closed";
									}
									
								}

							}
							?>
						
							<? // product is stocked in current department ?>
							<? if(isset($product["departments"][$user_department["id"]])): ?>

								<? // product is available in a department on at least one of the pickupdates ?>
								<? if($product["available_at"]): ?>

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

									<? // product is available in user's department on at least one of the pickupdates ?>
									<? if(arrayKeyValue($product["departments"][$user_department["id"]]["pickupdates"], "status", "available" ) !== false): ?>
								<h4 class="pickupdates">Tilføj bestillinger til afhentning på bestemte datoer:</h4>
								
								<div class="pickupdates">
								
									<ul class="pickupdates">

										<? foreach($pickupdates as $pickupdate): ?>
										
										<li class="pickupdate">

										<? // product is available ?>
										<? if($product["departments"][$user_department["id"]]["pickupdates"][$pickupdate["id"]]["status"] == "available"): ?>
										
											<?= $HTML->oneButtonForm("+", "/butik/addToCart", [
												"confirm-value" => false,
												"wait-value" => "Vent ...",
												"inputs" => [
													"item_id" => $product["id"],
													"quantity" => 1,
													"pickupdate_id" => $pickupdate["id"]
												],
												"wrapper" => "div.add",
												// "success-location" => "/butik"
											]) ?>

										<? // product is unavailable ?>
										<? elseif($product["departments"][$user_department["id"]]["pickupdates"][$pickupdate["id"]]["status"] == "unavailable"): ?>

											<div class="unavailable" title="Ikke tilgængelig">Ikke tilgængelig</div>

										<? // department is closed ?>
										<? elseif($product["departments"][$user_department["id"]]["pickupdates"][$pickupdate["id"]]["status"] == "closed"): ?>
											<div class="closed" title="Afdelingen er lukket">Afdelingen er lukket</div>

										<? endif; ?>

											<p class="date"><?= date("d.m", strtotime($pickupdate["pickupdate"])) ?></p>

										</li>

										<? endforeach; ?>
									</ul>

								</div>
								
									<? // product is not available in user's department, but is available in at least one of the other departments  ?>
									<? else: ?>
										<? 
										foreach ($product["available_at"] as $department_id) {
											$availability_department = $DC->getDepartment(["id" => $department_id]);

											if($availability_department) {
												$availability_department_names[] = $availability_department["name"];
											}
										}

										?>
										
										<p class="available_elsewhere">Dette produkt er ikke tilgængeligt i denne afdeling, men kan bestilles i følgende afdelinger: <?= implode(", ", $availability_department_names) ?>.</p>

									<? endif; ?>

							</div>
						</li>
								<? endif; ?>
							<? // product is stocked in other departments ?>
							<? elseif($product_department): ?>
								<? // TODO: in the future a department might choose not to stock a certain product at all, which could then be handled here ?>
								<? // either the product should not be shown, or maybe it could refer to other departments ?>
							<? endif; ?>
						<? endif; ?>
					<? endforeach; ?>
					</ul>
				<? else: ?>
				<p class="no_dates">Ingen aktuelle afhentningsdage.</p>
				<? endif; ?>
			
			<? else: ?>
			<p>Ingen produkter til salg.</p>
			<? endif; ?>


		</div>

		<div class="c-one-third sidebar">

			<div class="cart c-primary-box">
				<h3>Indkøbskurv</h3>
				<? if($cart && $cart["items"]): ?>
					<? if($cart_items_without_pickupdate): ?>
				<ul class="items">
					<? 
					// Loop through all cart items and show information and editing options of each item.
					foreach($cart_items_without_pickupdate as $cart_item):
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
					</li>
					<? endforeach; ?>
				</ul>
					<? endif; ?>

					<? if($cart_pickupdates): ?>
				<ul class="pickupdates">
					
						<? foreach($cart_pickupdates as $pickupdate): 
							$pickupdate_cart_items = $model->getCartPickupdateItems($pickupdate["id"]);
							if($pickupdate_cart_items): ?>
						
					<li class="pickupdate">
						<h4 class="pickupdate"><?= date("d.m.Y", strtotime($pickupdate["pickupdate"])) ?> – <span class="name"><?= $user_department["name"] ?></span></h4>

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
					<li ><a class="button" href="/butik/kurv">Gå til kurven</a></li>
				</ul>

				<? else: ?>
				<p>Du har ingenting i kurven endnu. <br />Føj en eller flere varer til kurven først.</p>
				<? endif; ?>
			</div>

			<? if($unpaid_membership && $unpaid_membership["type"] == "signupfee"): ?>
			<div class="c-box alert unpaid signupfee">
				<h3>OBS! Du mangler at betale dit indmeldelsesgebyr</h3>
				<p>Indmeldelsesgebyret vil blive indkrævet i forbindelse med næste grøntsagsbestilling. Man kan også betale det separat ved at klikke nedenfor.</p>
				<ul class="actions">
					<li class="pay"><a href="/butik/betaling/<?= $unpaid_membership["order_no"] ?>" class="button">Betal indmeldelsesgebyr nu</a></li>
				</ul>
			</div>
			<? elseif($unpaid_membership && $unpaid_membership["type"] == "membership"): ?>
			<div class="c-box alert unpaid membership">
				<h3>OBS! Du mangler at betale kontingent</h3>
				<p>Kontingentbetaling vil blive indkrævet i forbindelse med næste grøntsagsbestilling. Man kan også betale det separat ved at klikke nedenfor.</p>
				<ul class="actions">
					<li class="pay"><a href="/butik/betaling/<?= $unpaid_membership["order_no"] ?>" class="button">Betal kontingent nu</a></li>
				</ul>
			</div>
			<? endif; ?>

			<div class="orders c-box">
				<h3>Dine aktuelle bestillinger</h3>
				<? if($order_items_pickupdates): ?>
				<ul class="list">
					<li class="header">
						<span class="pickupdate">Afh.dato</span>
						<span class="product">Vare(r)</span>
					</li>
					<? foreach($order_items_pickupdates as $pickupdate): 
						$pickupdate_order_items = $model->getPickupdateOrderItems($pickupdate["id"], ["user_id" => $user_id]);
						if($pickupdate_order_items):
							foreach($pickupdate_order_items as $order_item): ?>
							<li class="listing">
								<span class="pickupdate"><?= date("d.m.Y", strtotime($pickupdate["pickupdate"])) ?></span>
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

				<p>Du har ingen aktuelle grøntsagsbestillinger.</p>

				<? endif; ?>

				<!-- <p>Gå til <a href="/profil" class="profile">Min side</a> for at se gamle bestillinger og rette datoer for aktuelle bestillinger.</p> -->
				<p>Gå til <a href="/profil" class="profile">Min side</a> for at rette datoer for aktuelle bestillinger.</p>
			</div>
		</div>
	</div>

	<? endif; ?>



</div>
