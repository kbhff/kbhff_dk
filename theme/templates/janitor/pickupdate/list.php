<?php
global $action;
global $model;

$pickupdates = $model->getPickupdates();

?>
<div class="scene i:scene defaultList pickupdateList">
	<h1>Pickup dates</h1>

	<ul class="actions">
		<?= $JML->listNew(array("label" => "New pickup date")) ?>
	</ul>

	<div class="all_items i:defaultList filters"<?= $HTML->jsData(["search"]) ?>>
<?		if($pickupdates): ?>
		<ul class="items pickupdates">

<?			foreach($pickupdates as $pickupdate): ?>
			<li class="item pickupdate pickupdate_id:<?= $pickupdate["id"] ?>">
				<h3><?= strip_tags($pickupdate["pickupdate"]) ?></h3>

				<?= $JML->listActions($pickupdate, ["modify" => [
					"delete"=>[
						"url"=>"/janitor/pickupdate/deletePickupdate/".$pickupdate["id"]
					]]]) ?>
			 </li>
<?			endforeach; ?>

		</ul>
<?		else: ?>
		<p>No pickup dates.</p>
<?		endif; ?>
	</div>

</div>
