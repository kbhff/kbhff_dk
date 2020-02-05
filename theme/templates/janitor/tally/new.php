
<?php
global $action;
global $model;

include_once("classes/system/department.class.php");
$DC = new Department();
$departments = $DC->getDepartments(); 

?>
<div class="scene i:scene defaultNew">
	<h1>New tally</h1>

	<ul class="actions">
		<?= $JML->newList(array("label" => "List")) ?>
	</ul>

	<?= $model->formStart("saveTally", array("class" => "i:defaultNew labelstyle:inject")) ?>
		<fieldset>
			<?= $model->input("name") ?>
			<?= $model->input("department_id", ["options" => $HTML->toOptions($departments, "id", "name", ["add" => ["" => "Choose associated department"]])]) ?>
		</fieldset>
		<?= $JML->newActions() ?>
	<?= $model->formEnd() ?>
</div>
