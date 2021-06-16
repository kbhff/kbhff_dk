<?php
global $model;
global $action;
// Get methods for user and shop data manipulation
$UC = new SuperUser();
$SC = new SuperShop();

// Get current user and related department
$member_user_id = $action[1];

$member_user = $UC->getKbhffUser(["user_id" => $member_user_id]);

$department = $UC->getUserDepartment(["user_id" => $member_user_id]);
$member_user_name = $member_user['nickname'] ? $member_user['nickname'] : $member_user['firstname'] . " " . $member_user['lastname'];

// Get membership status
$is_member = $member_user["membership"] ? $member_user["membership"]["id"] : false;

$orders = $SC->getOrders(["user_id" => $member_user_id]);

?>



<div class="scene profile user_profile order_history i:order_history">

	<div class="c-wrapper">
		<div class="c-box obs">
			<h2 class="obs"><span class="highlight">OBS! </span>Handler på vegne af <span class="highlight"><?= $member_user['nickname'] ? $member_user['nickname'] : $member_user['firstname'] . " " . $member_user['lastname'] ?></span></h2>
		</div>
	</div>

	<h1>Alle bestillinger</h1>
	
	<div class="c-wrapper">

		<?= $model->serverMessages(["type" => "error"]) ?>

		<? if($is_member): ?>
		<div class="section orders">

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
		<? else: ?>
		<div class="section not_member">
		<h3><?= $member_user_name ?> er ikke medlem</h3>
		</div>
		<? endif; ?>

	</div>
</div>

