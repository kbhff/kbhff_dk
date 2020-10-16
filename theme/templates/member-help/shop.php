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
	$member_user = $model->getUser(["user_id" => $member_user_id]);
	$department = $model->getUserDepartment(["user_id" => $member_user_id]);
	$member_name = $member_user['nickname'] ? $member_user['nickname'] : $member_user['firstname'] . " " . $member_user['lastname'];
	$member_name_possesive = preg_match("/s$/", $member_name) ? $member_name."'" : $member_name."s";
	$products = $DC->getDepartmentProducts($department["id"]);
	$pickupdates = $PC->getPickupdates(["after" => date("Y-m-d", strtotime("next wednesday"))]);
	$department_pickupdates = $DC->getDepartmentPickupdates($department["id"]);
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

	if($orders) {

		// $order_items_without_pickupdates = $SC->getOrderItemsWithoutPickupdate($member_user_id);
		
		$order_items_pickupdates = $SC->getOrderItemsPickupdates($member_user_id);
	}



}

// Clerk not logged in yet
else {

	// enable re-population of fields
	$clerk_username = stringOr(getPost("username"));

}


?>
<div class="scene shop i:shop">

	<div class="banner i:banner variant:random format:jpg"></div>
	
	<h1>Bestilling af grøntsager</h1>


 	<?
	// User is not logged in yet
	if($clerk_user_id == 1): ?>


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
				<li class="forgot">Har du <a href="/login/forgot" target="_blank">glemt dit password</a>?</li>
			</ul>
		<?= $UC->formEnd() ?>
	</div>


	<?
	// clerk is already logged in, show memberhelp-shop
	else: ?>
	<div class="c-wrapper">

		<div class="c-box obs">
			<h2 class="obs"><span class="highlight">OBS! </span>Handler på vegne af <span class="highlight"><?= $member_name ?></span></h2>
		</div>
		<div class="c-two-thirds">

			<? if($products): ?>
			<ul class="products i:products">

				<? foreach($products as $product): 
					$price = $SC->getPrice($product["id"]);
					$media = $IC->sliceMediae($product, "single_media");
					

				?>

				<li class="product">
					<div class="c-box">
						<h3><span class="name"><?= $product["name"] ?></span> <span class="price"><?= formatPrice($price, ["conditional_decimals" => true]) ?></span></h3>
						<? if($media): ?>
						<div class="image item_id:<?= $media["item_id"] ?> format:<?= $media["format"] ?> variant:<?= $media["variant"] ?>"></div>
						<? endif; ?>
						<p><?= $product["description"] ?></p>
						
						<h4>Tilføj bestillinger til afhentning på bestemte datoer:</h4>
						<? if($pickupdates): ?>
						
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
								
							<li>
								<ul class="pickupdate">
								
								<? // check if department is open on given pickupdate ?>
								<? if(arrayKeyValue($department_pickupdates, "id", $pickupdate["id"])): ?>

									<? // check product availability ?>
									<? if($product_available): ?>
									
									<?= $HTML->oneButtonForm("+", "/medlemshjaelp/butik/addToCart/".$cart_reference, [
										"confirm-value" => "Sikker?",
										"inputs" => [
											"item_id" => $product["id"],
											"quantity" => 1,
											"pickupdate_id" => $pickupdate["id"]
										],
										"wrapper" => "li.add",
										"success-location" => "/medlemshjaelp/butik/".$member_user_id
									]) ?>
									
									<? else: ?>
									<li class="unavailable">Ikke tilgængelig</li>
									<? endif; ?>	

								<? else: ?>
									
									<li class="closed">Afdelingen er lukket</li>
									
								<? endif; ?>
							
									<li class="date"><?= date("d/m", strtotime($pickupdate["pickupdate"])) ?></li>
								
								</ul>
							</li>

							<? endforeach; ?>
						</ul>

						<? else: ?>
						<p>Ingen aktuelle afhentningsdage.</p>
						<? endif; ?>
					</div>
				</li>

				<? endforeach; ?>
			</ul>
			
			<? else: ?>
			<p>Ingen produkter.</p>
			<? endif; ?>
			

		</div>

		<div class="c-one-third ">

			<div class="cart i:shopfrontCart c-primary-box">
				<h3>Indkøbskurv</h3>
				<? if($cart["items"] && $cart_items_without_pickupdate): ?>
				<ul class="items">
					<? 
					// Loop through all cart items and show information and editing options of each item.
					foreach($cart_items_without_pickupdate as $cart_item):
						$item = $IC->getItem(array("id" => $cart_item["item_id"], "extend" => array("subscription_method" => true)));
						$price = $SC->getPrice($cart_item["item_id"], array("quantity" => $cart_item["quantity"], "currency" => $cart["currency"], "country" => $cart["country"]));
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
						</p>

						<ul class="actions">
							<? // generate delete button to item 
							print $HTML->oneButtonForm("Slet", "/butik/deleteFromCart/".$cart["cart_reference"]."/".$cart_item["id"], array(
								"confirm-value" => "Sikker?",
								"wrapper" => "li.delete",
								"success-location" => "/medlemshjaelp/butik/".$member_user_id
							)) ?>
						</ul>
					</li>
					<? endforeach; ?>
				</ul>
				<? endif; ?>
				<? if($cart["items"] && $cart_pickupdates): ?>
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
										"success-location" => "/medlemshjaelp/butik/".$member_user_id
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
				<ul class="actions">
					<li ><a class="button" href="/medlemshjaelp/butik/kurv/<?= $cart_reference ?>">Gå til bekræftelse og betaling</a></li>
				</ul>
				<? else: ?>
				<p><?= $member_name ?> har ingenting i kurven endnu. <br />Føj en eller flere varer til kurven først.</p>
				<? endif; ?>
			</div>

			<? if($unpaid_membership && $unpaid_membership["type"] == "signupfee"): ?>
			<div class="c-box alert unpaid signupfee">
				<h3>OBS! Du mangler at betale dit indmeldelsesgebyr</h3>
				<p>Indmeldelsesgebyret vil automatisk blive tilføjet din næste bestilling. Du kan også betale det separat ved at klikke nedenfor.</p>
				<ul class="actions">
					<li class="pay"><a href="/butik/betaling/<?= $unpaid_membership["order_no"] ?>" class="button">Betal indmeldelsesgebyr nu</a></li>
				</ul>
			</div>
			<? elseif($unpaid_membership && $unpaid_membership["type"] == "membership"): ?>
			<div class="c-box alert unpaid membership">
				<h3>OBS! Du mangler at betale kontingent</h3>
				<p>Kontingentbetaling vil automatisk blive tilføjet din næste bestilling. Du kan også betale det separat ved at klikke nedenfor.</p>
				<ul class="actions">
					<li class="pay"><a href="/butik/betaling/<?= $unpaid_membership["order_no"] ?>" class="button">Betal kontingent nu</a></li>
				</ul>
			</div>
			<? endif; ?>

			<div class="orders c-box">
				<h3>Aktuelle bestillinger</h3>
				<? if($order_items_pickupdates): ?>
				<!-- <p>Gå til <a href="/profil" class="profile">Min side</a> for at se gamle bestillinger og rette datoer for aktuelle bestillinger.</p> -->
					<ul class="list">
						<li class="labels">
							<span class="pickupdates">Afh.dato</span>
							<span class="products">Vare(r)</span>
						</li>
						<li class="listings-container">
							<? foreach($order_items_pickupdates as $pickupdate): 
							$pickupdate_order_items = $SC->getPickupdateOrderItems($pickupdate["id"], ["user_id" => $member_user_id]);
							?>
								<? if($pickupdate_order_items): ?>
							
								<ul class="listings">
									<? foreach($pickupdate_order_items as $order_item): ?>
									<li class="listing">
										<span class="pickupdate"><?= date("d/m-Y", strtotime($pickupdate["pickupdate"])) ?></span>
										<span class="product"><?= $order_item["name"] ?></span>
									</li>
									<? endforeach; ?>
								</ul>

								<? endif; ?>	
							<? endforeach; ?>
						</li>
					</ul>
					
				<? else: ?>
				<p><?= $member_name ?> har ingen aktuelle bestillinger.</p>
				<!-- <p>Gå til <a href="/profil">Min side</a> for at se gamle bestillinger.</p> -->
				<? endif; ?>

			</div>
		</div>
	</div>
	






	<? endif; ?>



</div>
