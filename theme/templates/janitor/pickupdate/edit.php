<?php
global $action;
global $model;

$pickupdate_id = $action[1];
$pickupdate = $model->getPickupdate(array("id" => $pickupdate_id));

?>
<div class="scene i:scene defaultEdit pickupdateEdit">
	<h1>Edit pickup date</h1>
	<h2><?= strip_tags($pickupdate["pickupdate"]) ?></h2>

	<?= $JML->editGlobalActions($pickupdate, [
			"modify" => [
				"delete"=>[
					"url"=>"/janitor/pickupdate/deletePickupdate/".$pickupdate["id"]
		]]]) ?>

	<div class="pickupdate i:defaultEdit">
		<h2>Pickup date details</h2>
		<?= $model->formStart("updatePickupdate/".$pickupdate["id"], array("class" => "labelstyle:inject")) ?>

			<fieldset>
				<?= $model->input("pickupdate", array("type" => "date", "value" => $pickupdate["pickupdate"])) ?>
				<?= $model->input("comment", array("value" => $pickupdate["comment"])) ?>
			</fieldset>

			<?= $JML->editActions($pickupdate) ?>

		<?= $model->formEnd() ?>
	</div>


</div>
