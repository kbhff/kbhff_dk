<?php
global $action;
global $model; // SuperUser
global $UC; // User
global $SC;


$this->pageTitle("Betalinger");

// get current user id
$user_id = $action[1];
// $amount = "";

// Will only return orders from current user, so no need to check order ownership
$orders = $SC->getUnpaidOrders(["user_id" => $user_id]);

// Calculate total outstanding payment
$total_payment = 0;
$order_list = [];
// $order_comment_list = [];

if($orders) {
	
	// Loop through all orders to get total payment amount
	foreach($orders as $index => $order) {
		$total_order_price = $SC->getTotalOrderPrice($order["id"]);
		if($total_order_price) {
			$amount = formatPrice($total_order_price);
		}

		$transaction_id = $order["order_no"]; 


		$remaining_order_price = $SC->getRemainingOrderPrice($order["id"]);
		$orders[$index]["price"] = $remaining_order_price;

		// $order_comment_list[] = $order["order_no"] . " - " . $order["comment"];
		$order_list[] = $order["id"];

		$total_payment += $remaining_order_price["price"];


	}
}

?>
<div class="scene member_help_payment i:member_help_payment">
	<h1>Forfaldne betalinger</h1>

<? 
// Outstanding payments
if($orders && $total_payment): ?>


	<?= $HTML->serverMessages() ?>


	<dl class="amount">
		<dt class="amount">Skyldigt beløb</dt>
		<dd class="amount"><?= formatPrice(["price" => $total_payment, "currency" => $remaining_order_price["currency"]]) ?></dd>
	</dl>


	<h2>For betaling af:</h2>
	<ul class="orders">
	<? foreach($orders as $order): 
		$full_order = $SC->getOrders(["order_id" => $order["id"]]); ?>
		<li>
			<h3>Ordrenummer: <a href="/medlemshjaelp/betaling/<?= $full_order["order_no"] ?>"><?= $full_order["order_no"] ?></a> <?= ($full_order["comment"] ? (" – " . $full_order["comment"]) : "") ?>, <?= formatPrice($order["price"]) ?></h3>
			<ul class="orderitems">
			<? foreach($full_order["items"] as $order_item): ?>
				<li><?= $order_item["quantity"] ?> x <?= $order_item["name"] ?></li>
				<? endforeach; ?>
			</ul>
		</li>
		<li>
			<ul class="actions">
				<?= $HTML->oneButtonForm("Annullér ordre", "/medlemshjaelp/butik/cancelOrder/".$order["order_no"]."/$user_id", [
					"wrapper" => "li.cancel",
					"success-location" => "/medlemshjaelp/butik/$user_id"

				]) ?>
				<li><a href="/medlemshjaelp/betaling/<?= $full_order["order_no"] ?>" class="button primary">Betal ordre</a></li>
			</ul>
		</li>
	<? endforeach; ?>
	</ul>

<? 
// No payments
elseif($user_id > 1): ?>

	<h2>Intet at se her</h2>
	<p>Medlemmet har ingen udeståender.</p>

<? 
// User not logged in
else:

	$model = new User();
	$username = stringOr(getPost("username"));
	?>

	<h2>Leder du efter betalingssiden?</h2>
	<p>Du skal først logge ind på din konto.</p>


	<?= $SC->formStart("?login=true", array("class" => "login labelstyle:inject")) ?>
		<?= $model->input("login_forward", ["type" => "hidden", "value" => $this->url]); ?>


		<?= $HTML->serverMessages() ?>


		<fieldset>
			<?= $model->input("username", array("required" => true, "value" => $username, "label" => "Brugernavn", "hint_message" => "Brug dit medlemsnr., email eller telefonnummer som brugernavn", "error_message" => "Det ligner ikke et gyldigt brugernavn",)); ?>
			<?= $model->input("password", array("required" => true, "label" => "Adgangskode", "hint_message" => "Skriv din adgangskode","error_message" => "Ugyldig adgangskode",)); ?>
		</fieldset>

		<ul class="actions">
			<?= $model->submit("Log ind", array("class" => "primary", "wrapper" => "li.login")) ?>
		</ul>
	<?= $model->formEnd() ?>
	

<? endif;?>

</div>