<?php
global $action;
global $IC;
global $model;
global $itemtype;

$item_id = $action[1];
$item = $IC->getItem(array("id" => $item_id, "extend" => true));

?>
<div class="scene i:scene defaultEdit <?= $itemtype ?>Edit">
	<h1>Edit product</h1>
	<h2><?= strip_tags($item["name"]) ?></h2>

	<?= $JML->editGlobalActions($item) ?>

	<div class="item i:defaultEdit">
		<h2>Product details</h2>
		<?= $model->formStart("update/".$item["id"], array("class" => "labelstyle:inject")) ?>

			<fieldset>
				<?= $model->input("name", array("value" => $item["name"])) ?>
				<?= $model->input("start_availability_date", array("value" => $item["start_availability_date"])) ?>
				<?= $model->input("end_availability_date", array("value" => $item["end_availability_date"])) ?>
				<?= $model->input("product_type", array("value" => $item["product_type"], "type" => "select", "options" => $model->toOptions($model->getProductTypes(), "id", "name", ["add" => ["" => "Choose product type"]]))) ?>
				<?= $model->input("description", array("value" => $item["description"])) ?>
			</fieldset>

			<?= $JML->editActions($item) ?>

		<?= $model->formEnd() ?>
	</div>

	<?= $JML->editPrices($item); ?>

</div>
