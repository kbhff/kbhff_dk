<?php
global $action;
global $IC;
global $model;
global $itemtype;

?>
<div class="scene i:scene defaultNew">
	<h1>New weekly bag</h1>

	<ul class="actions">
		<?= $JML->newList(array("label" => "List")) ?>
	</ul>

	<?= $model->formStart("save/".$itemtype, array("class" => "i:defaultNew labelstyle:inject")) ?>
		<fieldset>
			<?= $model->input("name") ?>
			<?= $model->input("week", ["value" => date("W", strtotime("MONDAY NEXT WEEK"))]) ?>
			<?= $model->input("year", ["value" => date("Y", strtotime("WEDNESDAY NEXT WEEK"))]) ?>
		</fieldset>

		<?= $JML->newActions() ?>
	<?= $model->formEnd() ?>

</div>
