
<?php
global $action;
global $model;

$tallies = $model->getTallies();

?>

<div class="scene i:scene defaultList tallyList">
	<h1>Tallies</h1>

	<ul class="actions">
		<?= $JML->listNew(array("label" => "New tally")) ?>
	</ul>

	<div class="all_items i:defaultList filters"<?= $HTML->jsData(["search"]) ?>>
<?		if($tallies): ?>
		<ul class="items">
<?			foreach($tallies as $tally): ?>
			<li class="item item_id:<?= $tally["id"] ?>">
				<h3><?= strip_tags($tally["name"]) ?></h3>


				<?= $JML->listActions($tally, ["modify"=>[
					"delete"=>[
						"url"=>"/janitor/tally/deleteTally/".$tally["id"]
					]]]); ?>
			 </li>
<?			endforeach; ?>
		</ul>
<?		else: ?>
		<p>No tallies.</p>
<?		endif; ?>
	</div>

</div>
