<?php
global $action;
global $model;
$IC = new Items();
$UC = new User();

$username = stringOr(getPost("username"));
$firstname = stringOr(getPost("firstname"));
$lastname = stringOr(getPost("lastname"));
$email = stringOr(getPost("email"));
$mobile = stringOr(getPost("mobile"));


// get current user id
$user_id = session()->value("user_id");

// update user on cart
if($user_id != 1) {
	$_POST["user_id"] = $user_id;
	$model->updateCart(array("updateCart"));
}

// get current cart
$cart = $model->getCart();

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


$delivery_address = $UC->getAddresses(array("address_id" => $cart["delivery_address_id"]));
$billing_address = $UC->getAddresses(array("address_id" => $cart["billing_address_id"]));

?>
<div class="scene checkout i:checkout">
	<h1>Sign up</h1>

	<?= $HTML->serverMessages() ?>

<? if($membership): ?>

	<div class="signup">
		<h2>You are signing up for a <br />&quot;<?= $membership ?>&quot; membership</h2>
		<p>Enter your details below and create your membership account now.</h2>
		<?= $UC->formStart("signup", array("class" => "signup labelstyle:inject")) ?>
			<?= $UC->input("maillist", array("type" => "hidden", "value" => "curious")); ?>
			<fieldset>
				<?= $UC->input("firstname", array("value" => $firstname)); ?>
				<?= $UC->input("lastname", array("value" => $lastname)); ?>
				<?= $UC->input("email", array("value" => $email, "required" => true, "value" => $email, "hint_message" => "Type your email.", "error_message" => "You entered an invalid email.")); ?>
				<?= $UC->input("mobile", array("value" => $mobile)); ?>
				<?= $UC->input("password", array("hint_message" => "Type your new password - or leave it blank and we'll generate one for you.", "error_message" => "Your password must be between 8 and 20 characters.")); ?>
				<?= $UC->input("terms"); ?>
			</fieldset>

			<ul class="actions">
				<?= $UC->submit("Continue", array("class" => "primary", "wrapper" => "li.signup")) ?>
			</ul>
		<?= $model->formEnd() ?>
	</div>

<? else: ?>

	<div class="emptycart">
		<h2>You didn't select a membership yet.</h2>
		<p>Check out our <a href="/memberships">memberships</a> now.</p>

	</div>

<? endif; ?>

	<div class="account">
		<h3>Already have an account?</h3>
		<p>If you already have an account you can change your membership on <a href="/janitor/admin/profile">account profile</a>.</p>
	</div>

</div>
