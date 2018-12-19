<?php

global $action;
global $model;

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
				<?= $model->input("email", array("value" => $department["email"])) ?>
				<?= $model->input("opening_hours", array("class" => "autoexpand short", "value" => $department["opening_hours"])) ?>
				<?= $model->input("mobilepay_id", array("value" => $department["mobilepay_id"])) ?>
				<?= $model->input("accepts_signup", array("checked" => "true", "value" => $department["accepts_signup"])) ?>
			</fieldset>

			<?= $JML->editActions($department) ?>

		<?= $model->formEnd() ?>
	</div>

</div>
