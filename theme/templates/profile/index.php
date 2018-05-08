<?
global $UC;
$SC = new Shop();

$user = $UC->getUser();
$orders = $SC->getOrders();	

// check for unpaid orders
$unpaid_orders = false;
if(defined("SITE_SHOP") && SITE_SHOP) {
	include_once("classes/shop/shop.core.class.php");
	$SC = new Shop();
	$unpaid_orders = $SC->getUnpaidOrders();

}

?>
<div class="scene profile i:profile">

	<div class="item shop">

		<h2>Hej <?= $user['nickname'] ? $user['nickname'] : $user['firstname'] . " " . $user['lastname'] ?></h2>
		<h3>Bestil nu:</h3>
		<p>Liste over åbningsdage / steder / på sigt varer</p>

	</div>

	<div class="item i:editProfile">
		<h2>Bruger</h2>
		<?= $UC->formStart("update", array("class" => "labelstyle:inject")) ?>
			<fieldset>
				<?= $UC->input("nickname", array("value" => $user["nickname"])) ?>
				<?= $UC->input("firstname", array("value" => $user["firstname"])) ?>
				<?= $UC->input("lastname", array("value" => $user["lastname"])) ?>
				<?= $UC->input("email", array("value" => $user["email"])) ?>
				<?= $UC->input("mobile", array("value" => $user["mobile"])) ?>
			</fieldset>

			<ul class="actions">
				<?= $UC->submit("Update", array("class" => "primary key:s", "wrapper" => "li.save")) ?>
			</ul>
		<?= $UC->formEnd() ?>

		<h2>Konto</h2>
		member_no:<br />
		<?= $user["member_no"] ?><br />
		<br />
		Membership:<br />
		<? if($user["membership"]): ?>
		<? print_r($user["membership"]) ?><br />
		<? else: ?>
		Intet medlemskab
		<? endif; ?>
	</div>

	<div class="items orders i:itemList">
		<h2>Ordre</h2>
<?		if($orders): ?>
		<ul class="items">
<?			foreach($orders as $order): ?>
			<li class="item">
				<h3><?= $order["order_no"] ?> (<?= pluralize(count($order["items"]), "item", "items" ) ?>)</h3>

				<dl class="info">
					<dt class="created_at">Created at</dt>
					<dd class="created_at"><?= $order["created_at"] ?></dd>
					<dt class="status">Status</dt>
					<dd class="status <?= superNormalize($SC->order_statuses[$order["status"]]) ?>"><?= $SC->order_statuses[$order["status"]] ?></dd>
<?					if($order["status"] < 2): ?>
					<dt class="payment_status">Payment status</dt>
					<dd class="payment_status <?= ["unpaid", "partial", "paid"][$order["payment_status"]] ?>"><?= $SC->payment_statuses[$order["payment_status"]] ?></dd>
<?					endif; ?>
					<dt class="price">Total price</dt>
					<dd class="price"><?= formatPrice($SC->getTotalOrderPrice($order["id"])) ?></dd>
				</dl>

				<ul class="actions">
					<?= $HTML->link("View", "/janitor/admin/shop/order/edit/".$order["id"], array("class" => "button", "wrapper" => "li.edit")) ?>
				</ul>
			 </li>
<?			endforeach; ?>
		</ul>
<?		else: ?>
		<p>No orders.</p>
<?		endif; ?>
	</div>

	<div class="cancellation i:profileCancellation">
		<h2>Udmelding</h2>
		<p>
			If you cancel your account, we'll delete your personal information and your 
			membership and subscriptions from our system.
		</p>

<? if($unpaid_orders): ?>
		<p class="note system_error">
			You have <?= pluralize(count($unpaid_orders), "unpaid order", "unpaid orders")?>. 
			Settle <?= pluralize(count($unpaid_orders), "it", "them") ?> before you
			cancel your account.
		</p>

		<ul class="actions">
			<?= $HTML->link("Orders", "/profile/orders/list", array("class" => "button primary", "wrapper" => "li.orders")) ?>
		</ul>
<? else: ?>
		<?= $UC->formStart("cancel", array("class" => "cancelaccount")) ?>

			<fieldset>
				<?= $UC->input("password", array("label" => "Please type your password to confirm cancellation", "required" => true)) ?>
			</fieldset>

			<ul class="actions">
				<?= $UC->submit("Cancel account", array("class" => "secondary", "wrapper" => "li.cancelaccount")) ?>
			</ul>

		<?= $UC->formEnd() ?>
<? endif; ?>


	</div>

</div>