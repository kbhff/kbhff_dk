<?php
global $action;
global $IC;
global $model;
global $itemtype;

?>
<div class="scene i:scene defaultNew">
	<h1>New seasonal bag (product)</h1>

	<ul class="actions">
		<?= $JML->newList(array("label" => "List")) ?>
	</ul>

	<?= $model->formStart("save/".$itemtype, array("class" => "i:defaultNew labelstyle:inject")) ?>
		<fieldset>
			<?= $model->input("name") ?>
			<?= $model->input("start_availability_date", ["type" => "date"]) ?>
			<?= $model->input("end_availability_date", ["type" => "date"]) ?>
			<?= $model->input("description") ?>
		</fieldset>

		<?= $JML->newActions() ?>
	<?= $model->formEnd() ?>

</div>
