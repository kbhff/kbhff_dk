<?php
include_once("classes/system/department.class.php");
$DC = new Department();
$UC = new User();
$SC = new Shop();
global $action;
$departments = $DC->getDepartmentsAcceptSignups();
$user_department = $UC->getUserDepartment();
$pickupdates = $SC->getPickupdates(["after" => date("Y-m-d", strtotime("+7 days"))]);
$order_item_id = $action[1];
$order_item = $SC->getOrderItems(["order_item_id" => $order_item_id]);
$order = $order_item ? $SC->getOrders(["order_id" => $order_item["order_id"]]) : false;
$order_item_department_pickupdate = $SC->getOrderItemDepartmentPickupdate($order_item_id);
$user_id = session()->value("user_id");

$this->pageTitle("Ret bestilling");
?>

<? if($order && $order["user_id"] == $user_id && $order_item_department_pickupdate): ?>

<div class="scene update_order_item_department_pickupdate_form i:update_order_item_department_pickupdate_form">
	<h1>Ret bestilling</h1>
	<h2>Hvornår vil du hente din vare?</h2>

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
				"value" => $user_department["id"]
				]); 
			?>
		</fieldset>

		<ul class="actions">
			<li class="cancel"><a href="/profil" class="button">Annullér</a></li>
			<?= $UC->submit("Opdater", array("class" => "primary", "wrapper" => "li.save")) ?>
		</ul>

	<?= $UC->formEnd() ?>

</div>
<? else: ?>
<div class="scene">
	<h1>Fejl</h1>
	<p>Du kan ikke rette i denne ordrelinje.</p>
</div>
<? endif; ?>