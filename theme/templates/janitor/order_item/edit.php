<?php
global $action;
global $SC;
global $DC;
global $PC;
global $IC;

include_once("classes/users/superuser.class.php");
$UC = new SuperUser();

$order_item_id = $action[1];
$order_item = $SC->getOrderItems(["order_item_id" => $order_item_id]);

if($order_item) {

	$departments = $DC->getDepartments();
	$upcoming_pickupdates = $PC->getPickupdates(["after" => date("Y-m-d")]);

	$order_item_department_pickupdate = $SC->getOrderItemDepartmentPickupdate($order_item_id);
	$order_item_department_id = $order_item_department_pickupdate ? $order_item_department_pickupdate["department_id"] : "";
	$order_item_pickupdate_id = $order_item_department_pickupdate ? $order_item_department_pickupdate["pickupdate_id"] : "";
	
	$order_item_user = $order_item["user_id"] ? $UC->getUsers(["user_id" => $order_item["user_id"]]) : false;
	$order_item_user_email = $order_item["user_id"] ? $UC->getUsernames(["user_id" => $order_item["user_id"], "type" => "email"]) : false;
	
}



?>
<div class="scene i:scene defaultEdit shopView">
	<h1>Edit order item</h1>
<? if($order_item): ?>

	<h2><?= $order_item["name"] ?></h2>

	<ul class="actions i:defaultEditActions">
		<?= $HTML->link("Order item list", "/janitor/order-item/list", array("class" => "button", "wrapper" => "li.list")) ?>
		<?= $HTML->link("Order", "/janitor/admin/shop/order/edit/".$order_item["order_id"], array("class" => "button", "wrapper" => "li.order")) ?>
	</ul>

	<div class="basics">
		<h2>Details</h2>

		<dl class="info">
			
			<dt class="order_item_id">Order item ID</dt>
			<dd class="order_item_id"><?= $order_item_id ?></dd>
			
			<dt class="order_no">Order no.</dt>
			<dd class="order_no"><?= $order_item["order_no"] ?></dd>

			<dt class="nickname">User</dt>
			<dd class="nickname"><?= $order_item_user ? $order_item_user["nickname"] : "Unknown" ?></dd>
			
			<dt class="email">User email</dt>
			<dd class="email"><?= $order_item_user_email ? $order_item_user_email["username"] : "Unknown" ?></dd>
					
		</dl>

	</div>
	<div class=" item i:defaultEdit i:collapseHeader">
		<h2>Change pickup date and pickup department</h2>

		<div class="togglable_content">
			<?= $SC->formStart("setOrderItemDepartmentPickupdate/".$order_item_id, ["class" => "labelstyle:inject form"]); ?>
				<fieldset>
					<?= $SC->input("department_id", ["type" => "select", "required" => true, "value" => $order_item_department_id, "options" => $SC->toOptions($departments, "id", "name", ["add" => ["" => "Choose new pickup department"]])]); ?>
					<?= $SC->input("pickupdate_id", ["type" => "select", "required" => true, "value" => $order_item_pickupdate_id, "options" => $SC->toOptions($upcoming_pickupdates, "id", "pickupdate", ["add" => ["" => "Choose new pickup date"]])]); ?>
				</fieldset>
			
			<ul class="actions">
			<?= $SC->submit("Update", ["wrapper" => "li.save", "class"=>"primary"]); ?>
			</ul>
			
			<?= $SC->formEnd(); ?>
		</div>

	</div>

<? else: ?>

	<p>Order item does not exist.</p>

<? endif; ?>
	

</div>
