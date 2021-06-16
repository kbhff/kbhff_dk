<?php

global $action;
global $model;

$department_id = $action[1];
$department = $model->getDepartment(array("id" => $department_id));

$IC = new Items();
$products = $IC->getItems(["where" => "itemtype REGEXP '^product'", "extend" => true]);

include_once("classes/shop/pickupdate.class.php");
$PC = new Pickupdate();

$upcoming_pickupdates = $PC->getPickupdates(["after" => date("Y-m-d")]);
$department_pickupdates = $model->getDepartmentPickupdates($department_id);

?>
<div class="scene i:scene defaultEdit departmentEdit">
	<h1>Edit department</h1>
	<h2><?= strip_tags($department["name"]) ?></h2>

	<ul class="actions">
		<?= $JML->newList(array("label" => "List")) ?>
	</ul>

	<!-- <div class="products i:collapseHeader ">
		<h2>Products</h2>
		<ul class="products">
			<? if($products): ?>
				<? foreach($products as $product): ?>
				<? 
				 
				 // if product has end_availabilit date
					 // if today is in availability window
						 // show li
				 // else
					 // if today is after first availability
						 //show li 				
				
				?>	

			<li class="product product_id:<?= $product["id"] ?>"></li>
				<ul class="info">
					<li class="name"><?= $product["name"] ?></li>
				</ul>
			
				<? endforeach; ?>
			<? endif; ?>
	
		</ul>

	</div> -->




	<div class="item i:defaultEdit i:collapseHeader">
		<h2>Department details</h2>
		<?= $model->formStart("updateDepartment/".$department["id"], array("class" => "labelstyle:inject")) ?>
		
			<fieldset>
				<?= $model->input("name", array("value" => $department["name"])) ?>
				<?= $model->input("abbreviation", array("value" => $department["abbreviation"])) ?>
				<?= $model->input("address1", array("value" => $department["address1"])) ?>
				<?= $model->input("address2", array("class" => "autoexpand short", "value" => $department["address2"])) ?>
				<?= $model->input("postal", array("value" => $department["postal"])) ?>
				<?= $model->input("city", array("value" => $department["city"])) ?>

				<?= $model->inputLocation("geolocation", "latitude", "longitude", array("value_loc" => $department["geolocation"], "value_lat" => $department["latitude"], "value_lon" => $department["longitude"])) ?>
			</fieldset>

			<fieldset>
				<?= $model->input("email", array("value" => $department["email"])) ?>
				<?= $model->input("mobilepay_id", array("value" => $department["mobilepay_id"])) ?>
				<?= $model->input("opening_hours", array("class" => "autoexpand short", "value" => $department["opening_hours"])) ?>
			</fieldset>

			<fieldset>
				<?= $model->input("accepts_signup", array("checked" => "true", "value" => $department["accepts_signup"])) ?>
			</fieldset>

			<fieldset>
				<?= $model->input("description", array("value" => $department["description"])) ?>
				<?= $model->input("html", array("value" => $department["html"])) ?>
			</fieldset>


			<?= $JML->editActions($department) ?>

		<?= $model->formEnd() ?>
	</div>

	<div class="pickupdates all_items sortable i:department_pickupdates i:defaultList i:collapseHeader">
		<h2>Department pickupdates</h2>
		<? if($upcoming_pickupdates): ?>
		<p>Displaying upcoming pickupdates. A pickupdate can be removed from a department, if the department will be closed. </p>
		<ul class="items">
			<? foreach($upcoming_pickupdates as $pickupdate): 
			
			$status = arrayKeyValue($department_pickupdates, "id", $pickupdate["id"]) !== false ? "added" : "";

			?>
			<li class="item <?= $status ?>">
				<h3><?= $pickupdate["pickupdate"] ?></h3>
				<ul class="actions">
					<?= $HTML->oneButtonForm("Add", "/janitor/department/addPickupdate/".$department_id."/".$pickupdate["id"], array(
					"confirm-value" => false,
					"wrapper" => "li.add",
					"class" => "primary",
					"success-function" => "added" 
					));?>

					<?= $HTML->oneButtonForm("Remove", "/janitor/department/removePickupdate/".$department_id."/".$pickupdate["id"], array(
					"confirm-value" => "Confirm Removal",
					"wrapper" => "li.remove",
					"class" => "secondary",
					"success-function" => "removed"
					)) ?>
				</ul>
			</li>
			<? endforeach; ?>
		</ul>
		
		<? else: ?>
		<p>No upcoming pickupdates.</p>
		
		<? endif; ?>
		
		

	</div>



</div>
