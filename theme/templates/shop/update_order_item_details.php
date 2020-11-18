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
$order_item_pickupdate = $SC->getOrderItemPickupdate($order_item_id);

$this->pageTitle("Ret bestilling");
?>

<div class="scene update_userinfo_form i:update_userinfo_form">
	<h1>Ret bestilling</h1>
	<h2>Hvornår vil du hente dine grøntsager?.</h2>

	<?= $UC->formStart("updateOrderItemDetails/".$order_item_id, ["class" => "form_order_item_details"]) ?> 

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
			<li class="cancel"><a href="/" class="button">Annullér</a></li>
			<?= $UC->submit("Opdater", array("class" => "primary", "wrapper" => "li.save")) ?>
		</ul>

	<?= $UC->formEnd() ?>

</div>