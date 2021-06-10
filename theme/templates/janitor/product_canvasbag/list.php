<?php
global $action;
global $IC;
global $model;
global $itemtype;


$items = $IC->getItems(array("itemtype" => $itemtype, "extend" => ["dependencies" => true]));

?>
<div class="scene i:scene defaultList <?= $itemtype ?>List">
	<h1>Canvas bags (product)</h1>

	<ul class="actions">
		<?= $JML->listNew(array("label" => "New canvas bag")) ?>
	</ul>

	<div class="all_items i:defaultList filters"<?= $HTML->jsData(["search"]) ?>>
<?		if($items): ?>
		<ul class="items">

<?			foreach($items as $item): ?>
			<li class="item item_id:<?= $item["id"] ?>">
				<h3><?= strip_tags($item["name"]) ?></h3>

				<?= $JML->listActions($item) ?>
			 </li>
<?			endforeach; ?>

		</ul>
<?		else: ?>
		<p>No canvas bags.</p>
<?		endif; ?>
	</div>

</div>
