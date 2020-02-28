<?php

global $action;
global $model;

$IC = new Items();
$products = $IC->getItems(["itemtype" => "product", "extend" => true]);

$department_id = $action[1];
$department = $model->getDepartment(array("id" => $department_id));
?>
<div class="scene i:scene defaultEdit departmentEdit">
	<h1>Edit department</h1>
	<h2><?= strip_tags($department["name"]) ?></h2>

	<ul class="actions">
		<?= $JML->newList(array("label" => "List")) ?>
	</ul>

	<div class="item i:defaultEdit">
		<h2>Post content</h2>
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

	<div class="products i:collapseHeader ">
		<h2>Products</h2>
		<ul class="products">
			<? if($products): ?>
				<? foreach($products as $product): ?>
			<li class="product product_id:<?= $product["id"] ?>"></li>
				<ul class="info">
					<li class="name"><?= $product["name"] ?></li>
				</ul>
			
				<? endforeach; ?>
			<? endif; ?>
	
		</ul>

	</div>

</div>
