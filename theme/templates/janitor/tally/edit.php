<?php

global $action;
global $model;

$tally_id = $action[1];
$tally = $model->getTally(array("id" => $tally_id));
?>
<div class="scene i:scene defaultEdit tallyEdit">
	<h1>Edit tally</h1>
	<h2><?= strip_tags($tally["name"]) ?></h2>

	<ul class="actions">
		<?= $JML->newList(array("label" => "List")) ?>
	</ul>

	<div class="item i:defaultEdit">
		<h2>Post content</h2>
		<?= $model->formStart("updateTally/".$tally["id"], array("class" => "labelstyle:inject")) ?>
		
			<fieldset>
				<?= $model->input("name", ["value" => $tally["name"]]) ?>
			</fieldset>

			<fieldset class="cash">
				<?= $model->input("start_cash") ?>
				<?= $model->input("end_cash") ?>
				<?= $model->input("deposited") ?>
			</fieldset>

			<fieldset class="misc_revenue">
				<?= $model->input("misc_cash_revenue") ?>
			</fieldset>

			<fieldset class="cash_sales">

			</fieldset>
			
			<fieldset class="comments">
				<?= $model->input("comment") ?>
			</fieldset>

			<?= $JML->editActions($tally) ?>

		<?= $model->formEnd() ?>
	</div>

</div>
