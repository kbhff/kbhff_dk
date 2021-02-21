<?php
include_once("classes/system/department.class.php");
$DC = new Department();
$UC = new User();
$user = $UC->getKbhffUser();

$IC = new Items();
$model = $IC->typeObject("productweeklybag");
global $action;

$item_id = $action[1];
$product = $IC->getItem(array("id" => $item_id, "extend" => ["mediae" => true, "prices" => true]));
$product_price_1_key = $product["prices"] !== false ? arrayKeyValue($product["prices"], "type", "frivillig") : false;
$product_price_2_key = $product["prices"] !== false ? arrayKeyValue($product["prices"], "type", "stoettemedlem") : false;

$this->pageTitle("Rediger produkt");

?>

<div class="scene edit_product i:edit_product">
	<h1>Rediger produkt</h1>
	<h2>Produktoplysninger</h2>
	
	<?= $model->formStart("updateProduct/".$product["id"], ["class" => "labelstyle:inject update"]); ?>
		<fieldset class="details">
			<?= $model->input("name", ["label" => "Produktnavn", "hint_message" => "Giv produktet et navn", "error_message" => "Produktet må have et navn", "value" => $product["name"]]); ?>
			<?= $model->input("price_1", ["type" => "number", "label" => "Pris 1 (Frivillig-medlem)", "required" => true, "value" => $product_price_1_key !== false ? $product["prices"][$product_price_1_key]["price"] : false]); ?>
			<?= $model->input("price_2", ["type" => "number", "label" => "Pris 2 (Støttemedlem)", "required" => true, "value" => $product_price_2_key !== false ? $product["prices"][$product_price_2_key]["price"] : false]); ?>
			<?= $model->input("single_media", ["label" => "Produktbillede"]); ?>
			<?= $model->input("description", ["label" => "Produktbeskrivelse", "value" => $product["description"]]); ?>
		</fieldset>

		<h3>Bestilling og afhentning</h3>
		<fieldset class="availability">
			<?= $model->input("start_availability_date", ["label" => "Fra og med dato", "hint_message" => "Hvornår bliver produktet tilgængeligt for medlemmerne?.",
					"error_message" => "Angiv hvornår produktet bliver tilgængeligt for medlemmerne.", "value" => $product["start_availability_date"]]); ?>
			<?= $model->input("end_availability_date", ["label" => "Til og med dato (kan udelades)", "hint_message" => "Hvornår ophører produktet med at være tilgængelig for medlemmerne?.", "error_message" => "Angiv hvornår produktet udløber.", "value" => $product["end_availability_date"] ?: false]); ?>
		</fieldset>
		<ul class="actions">
		<?= $UC->link("Annuller", "/indkoeb", array("class" => "button", "wrapper" => "li.cancel")) ?>
		<?= $model->submit("Gem", ["wrapper" => "li.save", "class" => "primary"]); ?>
		</ul>
	<?= $model->formEnd(); ?>

</div>