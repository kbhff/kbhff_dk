<?php
include_once("classes/system/department.class.php");
$DC = new Department();
$UC = new SuperUser();
$SC = new SuperShop();
global $action;
$departments = $DC->getDepartmentsAcceptSignups();
$pickupdates = $SC->getPickupdates(["after" => date("Y-m-d", strtotime("+7 days"))]);
$order_item_id = $action[1];
$user_id = $action[2];
$user_department = $UC->getUserDepartment(["user_id" => $user_id]);
$order_item = $SC->getOrderItems(["order_item_id" => $order_item_id]);
$order = $order_item ? $SC->getOrders(["order_id" => $order_item["order_id"]]) : false;
$order_item_department_pickupdate = $SC->getOrderItemDepartmentPickupdate($order_item_id);

$this->pageTitle("Ret bestilling");
?>

<? if($order && $order["user_id"] == $user_id && $order_item_department_pickupdate): ?>

<div class="scene update_userinfo_form i:update_userinfo_form">
	<h1>Ret bestilling</h1>
	<h2>Hvornår skal produkterne hentes?</h2>

	<?= $UC->formStart("setOrderItemDepartmentPickupdate/".$order_item_id, ["class" => "form_order_item_details"]) ?> 

		<?= $HTML->serverMessages(["type" => "error"]); ?>

		<fieldset>
			<?= $UC->input("pickupdate_id", [
				"type" => "select", 
				"options" => $SC->toOptions($pickupdates, "id", "pickupdate"),
				"value" => $order_item_department_pickupdate["pickupdate_id"]
				]); 
			?>
			<?= $UC->input("department_id", [
				"type" => "hidden", 
				"value" => $user_department ? $user_department["id"]: false
				]); 
			?>
		</fieldset>

		<ul class="actions">
			<li class="cancel"><a href="/medlemshjaelp/brugerprofil/<?= $user_id ?>" class="button">Annullér</a></li>
			<?= $UC->submit("Opdater", array("class" => "primary", "wrapper" => "li.save")) ?>
		</ul>

	<?= $UC->formEnd() ?>

</div>

<? else: ?>
<div>
	<h1>Fejl</h1>
	<p>Du kan ikke rette denne ordrelinje.</p>
</div>
<? endif; ?>