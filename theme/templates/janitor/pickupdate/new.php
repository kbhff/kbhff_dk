<?php
global $action;
global $model;

?>
<div class="scene i:scene defaultNew">
	<h1>New pickup date</h1>

	<ul class="actions">
		<?= $JML->newList(array("label" => "List")) ?>
	</ul>

	<?= $model->formStart("savePickupdate", array("class" => "i:defaultNew labelstyle:inject")) ?>
		<fieldset>
			<?= $model->input("pickupdate") ?>
			<?= $model->input("comment") ?>
		</fieldset>

		<?= $JML->newActions() ?>
	<?= $model->formEnd() ?>

</div>
