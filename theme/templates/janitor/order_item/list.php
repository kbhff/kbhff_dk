<?php
global $action;
global $IC;
global $SC;
global $PC;
global $DC;

include_once("classes/users/superuser.class.php");
$UC = new SuperUser();

$order_items = $SC->getOrderItems(["where" => "items.itemtype REGEXP '^product.*'"]);
if($order_items) {
	foreach ($order_items as $key => $order_item) {
		
		$order_item_department_pickupdate = $SC->getOrderItemDepartmentPickupdate($order_item["id"]);
		$order_items[$key]["department_id"] = $order_item_department_pickupdate ? $order_item_department_pickupdate["department_id"] : false;
		$order_items[$key]["department"] = $order_item_department_pickupdate ? $order_item_department_pickupdate["department"] : false;
		$order_items[$key]["pickupdate_id"] = $order_item_department_pickupdate ? $order_item_department_pickupdate["pickupdate_id"] : false;
		$order_items[$key]["pickupdate"] = $order_item_department_pickupdate ? $order_item_department_pickupdate["pickupdate"] : false;
	}

	// sort order_items. 
	// order_items w/o pickupdate first. Then sort by pickupdate, then by department.
	usort($order_items, function ($a, $b) {
		
		if($a["pickupdate"] && $b["pickupdate"]) {

			if($a["pickupdate"] == $b["pickupdate"]) {

				return ($a["department"] < $b["department"]) ? 1 : -1;
			}

			return ($a["pickupdate"] < $b["pickupdate"]) ? 1 : -1;
		}
		else if($a["pickupdate"]) {
			return 1;
		}
		else if($b["pickupdate"]){
			return -1;
		}
		else {
			return 0;
		}
	});
}

?>
<div class="scene i:scene defaultList shopList">
	<h1>Ordered products</h1>
	<p><em>Note:</em> Each ordered product corresponds to an <em>order_item</em> in the database, which is part of an <em>order</em> that may contain several order_items. Products that belong to a cancelled order are not shown.</p>

	<div class="all_items i:defaultList filters">
		<? if($order_items): ?>
		<ul class="items order_items">
			<? foreach($order_items as $order_item): 
			
				$order_item_user = $order_item["user_id"] ? $UC->getUsers(["user_id" => $order_item["user_id"]]) : false;
				$order_item_user_email = $order_item["user_id"] ? $UC->getUsernames(["user_id" => $order_item["user_id"], "type" => "email"]) : false;
				$order_item_department = isset($order_item["department_id"]) && $order_item["department_id"] ? $DC->getDepartment(["id" => $order_item["department_id"]]) : false;
				$order_item_pickupdate = isset($order_item["pickupdate_id"]) && $order_item["pickupdate_id"] ? $PC->getPickupdate(["id" => $order_item["pickupdate_id"]]) : false;
			?>
			<li class="item product">
				<h3><?= $order_item["name"] ?></h3>

				<dl class="info">
					<dt class="order_no">Order item ID.</dt>
					<dd class="order_no"><?= $order_item["id"] ?></dd>;

					<dt class="order_no">Order no.</dt>
					<dd class="order_no"><?= $order_item["order_no"] ?></dd>

					<dt class="nickname">User</dt>
					<dd class="nickname"><?= $order_item_user ? $order_item_user["nickname"] : "Unknown" ?></dd>
					
					<dt class="email">User email</dt>
					<dd class="email"><?= $order_item_user_email ? $order_item_user_email["username"] : "Unknown" ?></dd>
					
					<dt class="department">Department</dt>
					<dd class="department"><?= $order_item_department ? $order_item_department["name"] : " <span style='color: #df0000'>None</span> " ?></dd>

					<dt class="pickupdate">Pickupdate</dt>
					<dd class="pickupdate"><?= $order_item_pickupdate ? $order_item_pickupdate["pickupdate"] : " <span style='color: #df0000'>None</span> " ?></dd>

				</dl>

				<ul class="actions">
					<?= $HTML->link("Edit", "/janitor/order-item/edit/".$order_item["id"], array("class" => "button", "wrapper" => "li.view")) ?>
				</ul>
			 </li>
<?			endforeach; ?>
		</ul>
<?		else: ?>
		<p>No ordered products were found</p>
<?		endif; ?>
	</div>

</div>
