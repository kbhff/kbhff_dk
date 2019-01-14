<?php
global $action;
global $model;

$user_id = session()->value("user_id");

//$UC = new User();
//$username = stringOr(getPost("username"));


// if cart reference was passed to cart controller

if(count($action) > 1) {
	session()->value("cart_reference", $action[1]);
}
$cart = $model->getCart();



$IC = new Items();

?>
<div class="scene cart i:cart">
	<h1>Your cart</h1>
	<?
	//print all stored messages if any
	print $HTML->serverMessages();
	?>
<? 
	// if cart has items, add action of checkout button to html output
	if($cart["items"]) :
	?>
	<div class="checkout">
		<ul class="actions">
			<?= $JML->oneButtonForm("Checkout", "/shop/checkout", array(
				"confirm-value" => false,
				"dom-submit" => true,
				"success-location" => "/shop/checkout",
				"class" => "primary",
				"name" => "continue",
				"wrapper" => "li.continue",
			)) ?>
		</ul>
	</div>
<? endif; ?>

	<div class="all_items">
		<h2>Cart contents</h2>
		<? if($cart["items"]): ?>
		<ul class="items">
			<? 
			// if cart has items, loops through all cart items and show each specific item and its price, quantity and editing options(delete and update quantity)
			foreach($cart["items"] as $cart_item):
				$item = $IC->getItem(array("id" => $cart_item["item_id"], "extend" => array("subscription_method" => true)));
				$price = $model->getPrice($cart_item["item_id"], array("quantity" => $cart_item["quantity"], "currency" => $cart["currency"], "country" => $cart["country"]));
			?>
			<li class="item id:<?= $item["id"] ?>">
				<h3>
					<?
					// add option of updating item quantity to item 
					print $model->formStart("/shop/updateCartItemQuantity/".$cart["cart_reference"]."/".$cart_item["id"], array("class" => "updateCartItemQuantity labelstyle:inject")) ?>
						<fieldset>
							<?= $model->input("quantity", array(
								"type" => "integer",
								"value" =>  $cart_item["quantity"],
								"hint_message" => "State the quantity of this item"
							)) ?>
						</fieldset>
						<ul class="actions">
							<?= $model->submit("Update", array("name" => "update", "wrapper" => "li.save")) ?>
						</ul>
					<?= $model->formEnd() ?>
					<span class="x">x </span>
					<span class="name"><?= $item["name"] ?> </span>
					<span class="a">á </span>
					<span class="unit_price"><?= formatPrice($price) ?></span>
					<span class="total_price">
						<? // add total price and vat to item 
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
				<? if($item["subscription_method"] && $price["price"]): ?>
				<p class="subscription_method">
					Re-occuring payment every <?= strtolower($item["subscription_method"]["name"]) ?>.
				</p>
				<? endif; ?>

				<? if($item["itemtype"] == "membership"): ?>
				<p class="membership">
					This purchase includes a membership.
				</p>
				<? endif; ?>

				<ul class="actions">
					<? // add action of delete button to item 
					print $JML->oneButtonForm("Delete", "/shop/deleteFromCart/".$cart["cart_reference"]."/".$cart_item["id"], array(
						"wrapper" => "li.delete",
						"static" => true
					)) ?>
				</ul>
			</li>
			<? endforeach; ?>

			<li class="total">
				<h3>
					<span class="name">Total</span>
					<span class="total_price">
						<? // add total price of cart to html output
						print formatPrice($model->getTotalCartPrice($cart["id"]), array("vat" => true)) ?>
					</span>
				</h3>
			</li>
		</ul>
		<? else: ?>
		<p>You don't have any items in your cart yet. <br />Check out our <a href="/bliv-medlem">memberships</a> now.</p>
		<ul class="items">
			<li class="total">
				<h3>
					<span class="name">Total</span>
					<span class="total_price">
						<?= formatPrice($model->getTotalCartPrice($cart["id"]), array("vat" => true)) ?>
					</span>
				</h3>
			</li>
		</ul>
		<? endif; ?>
	</div>

	<? // if cart has items, add action of checkout button to html output
	if($cart["items"]) :?>
	<div class="checkout">
		<ul class="actions">
			<?= $JML->oneButtonForm("Checkout", "/shop/checkout", array(
				"confirm-value" => false,
				"dom-submit" => true,
				"class" => "primary",
				"name" => "continue",
				"wrapper" => "li.continue",
			)) ?>
		</ul>
	</div>
<? 	endif; ?>
</div>
