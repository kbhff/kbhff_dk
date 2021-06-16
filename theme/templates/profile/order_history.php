<?php
// Get methods for user and shop data manipulation
$UC = new User();
$SC = new Shop();
$HTML = new HTML();

// Get current user and related department
$user = $UC->getKbhffUser();
$department = $UC->getUserDepartment();


$orders = $SC->getOrders();

?>



<div class="scene order_history i:order_history">

	<div class="banner i:banner variant:random format:jpg"></div>

	<?= $HTML->serverMessages(["type" => "error"]); ?>

	<div class="c-wrapper">

			<div class="section orders">
				<h2>Alle Bestillinger</h2>

			<? if($orders): ?>

				<div class="orders">
					<ul class="orders">

					<? foreach($orders as $order):
						$total_order_price = $SC->getTotalOrderPrice($order["id"]);
					 ?>

						<li class="order">
							<span class="order_date"><span class="date"><?= date("d.m.Y", strtotime($order["created_at"])) ?></span></span>
							<span class="order_no"><?= $order["order_no"] ?></span>
							<span class="total_price"><?= formatPrice($total_order_price) ?></span>
						</li>

						<? foreach($order["items"] as $order_item): ?>
						<li class="order_item">
							<span class="product"><?= $order_item["quantity"] > 1 ? $order_item["quantity"]." x " : ""?><?= $order_item["name"] ?></span>
						</li>
						<? endforeach; ?>

					<? endforeach; ?>	

					</ul>
				</div>

			<? else: ?>
				<p><?= $member_user_name ?> har ingen aktuelle grøntsagsbestillinger.</p>
			<? endif; ?>

			</div>

	</div>
</div>
