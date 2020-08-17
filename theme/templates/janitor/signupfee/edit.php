<?php
global $action;
global $IC;
global $model;
global $itemtype;

$item_id = $action[1];
$item = $IC->getItem(array("id" => $item_id, "extend" => array("tags" => true, "mediae" => true, "comments" => true, "subscription_method" => true)));

$memberships = $IC->getItems(array("itemtype" => "membership", "extend" => true));
?>
<div class="scene i:scene defaultEdit <?= $itemtype ?>Edit">
	<h1>Edit signup fee type</h1>
	<h2><?= strip_tags($item["name"]) ?></h2>

	<?= $JML->editGlobalActions($item) ?>

	<?= $JML->editSingleMedia($item) ?>

	<div class="item i:defaultEdit">
		<h2>Signup fee details</h2>
		<?= $model->formStart("update/".$item["id"], array("class" => "labelstyle:inject")) ?>

			<fieldset>
				<?= $model->input("name", array("value" => $item["name"])) ?>
				<?= $model->input("fixed_url_identifier", array("value" => $item["fixed_url_identifier"])) ?>
				<?= $model->input("associated_membership_id", array("type" => "select", "options" => $HTML->toOptions($memberships, "id", "name", ["add" => ["" => "Choose associated membership type"]]), "value" => $item["associated_membership_id"])) ?>
				<?= $model->input("description", array("class" => "autoexpand short", "value" => $item["description"])) ?>
				<?= $model->input("html", array("value" => $item["html"])) ?>
			</fieldset>

			<?= $JML->editActions($item) ?>

		<?= $model->formEnd() ?>
	</div>

	<?= $JML->editPrices($item) ?>

	<?= $JML->editTags($item) ?>

	<?= $JML->editDeveloperSettings($item) ?>

	<?= $JML->editOwner($item) ?>

</div>
