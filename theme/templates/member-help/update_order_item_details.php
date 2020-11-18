<?php
include_once("classes/system/department.class.php");
$DC = new Department();
$UC = new SuperUser();
$SC = new SuperShop();
global $action;
$departments = $DC->getDepartmentsAcceptSignups();
$user_department = $UC->getUserDepartment();
$pickupdates = $SC->getPickupdates(["after" => date("Y-m-d", strtotime("+7 days"))]);
$order_item_id = $action[1];
$user_id = $action[2];
$order_item = $SC->getOrderItem($order_item_id);
$order = $order_item ? $SC->getOrders(["order_id" => $order_item["order_id"]]) : false;
$order_item_pickupdate = $SC->getOrderItemPickupdate($order_item_id);

$this->pageTitle("Ret bestilling");
?>

<? if($order && $order["user_id"] == $user_id && $order_item_pickupdate): ?>

<div class="scene update_userinfo_form i:update_userinfo_form">
	<h1>Ret bestilling</h1>
	<h2>Hvornår skal grøntsagerne hentes?</h2>

	<?= $UC->formStart("updateOrderItemDetails/".$order_item_id."/".$user_id, ["class" => "form_order_item_details"]) ?> 

		<?= $HTML->serverMessages(["type" => "error"]); ?>

		<fieldset>
			<?= $UC->input("pickupdate_id", [
				"type" => "select", 
				"options" => $SC->toOptions($pickupdates, "id", "pickupdate"),
				"value" => $order_item_pickupdate["id"]
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