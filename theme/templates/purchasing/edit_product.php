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

$first_possible_pickupdate = date("d.m.Y", strtotime($product["start_availability_date"]." Wednesday"));
$last_possible_pickupdate = $product["end_availability_date"] ? date("d.m.Y", strtotime($product["end_availability_date"]." last Wednesday")) : false;

$file_input_value = $IC->filterMediae($product, "single_media");

$this->pageTitle("Rediger produkt");

?>

<div class="scene edit_product i:add_edit_product">
	<h1>Rediger produkt</h1>
	<h2>Produktoplysninger</h2>
	
	<?= $model->formStart("updateProduct/".$product["id"], ["class" => "labelstyle:inject update"]); ?>

		<div class="c-wrapper">
			<div class="c-one-half">

				<fieldset class="details">
					<?= $model->input("name", ["label" => "Produktnavn", "hint_message" => "Giv produktet et navn", "error_message" => "Produktet må have et navn", "value" => $product["name"]]); ?>
					<?= $model->input("price_1", ["type" => "number", "label" => "Pris 1 (Frivillig-medlem)", "required" => true, "value" => $product_price_1_key !== false ? $product["prices"][$product_price_1_key]["price"] : false]); ?>
					<?= $model->input("price_2", ["type" => "number", "label" => "Pris 2 (Støttemedlem)", "required" => true, "value" => $product_price_2_key !== false ? $product["prices"][$product_price_2_key]["price"] : false]); ?>
					<?= $model->input("description", ["label" => "Produktbeskrivelse", "value" => $product["description"]]); ?>
				</fieldset>

			</div>
			<div class="c-one-half">

				<h3>Produktbillede</h3>
				<fieldset class="media">
					<?= $model->input("single_media", ["label" => "Produktbillede", "hint_message" => "Tryk her for at vælge et billede, eller træk et billede ind på det grå felt. Størrelse mindst 960x960 px. Tilladte formater: PNG og JPG.", "error_message" => "Billedet lever ikke op til kravene.", "value" => $file_input_value]); ?>
				</fieldset>

				<h3>Tilgængelighed fra producent</h3>
				<fieldset class="availability">
					<?= $model->input("start_availability_date", ["label" => "Fra og med dato", "hint_message" => "Hvornår bliver produktet tilgængeligt fra producenten?",
							"error_message" => "Angiv hvornår produktet bliver tilgængeligt fra producenten.", "value" => $product["start_availability_date"]]); ?>
					<p class="first_pickupdate">Første mulige afhentningsdag: <span><?= $first_possible_pickupdate ?: "-" ?></span></p>
					<?= $model->input("end_availability_date", ["label" => "Til og med dato (kan udelades)", "hint_message" => "Hvornår ophører produktet med at være tilgængelig fra producenten? Kan udelades.", "error_message" => "Angiv hvornår produktet udløber.", "value" => $product["end_availability_date"] ?: false]); ?>
					<p class="last_pickupdate">Sidste mulige afhentningsdag: <span><?= $last_possible_pickupdate ?: "-" ?></span></p>

				</fieldset>
			</div>
		</div>

		<ul class="actions">
			<?= $UC->link("Annuller", "/indkoeb", array("class" => "button", "wrapper" => "li.cancel")) ?>
			<?= $model->submit("Gem", ["wrapper" => "li.save", "class" => "primary"]); ?>
		</ul>

	<?= $model->formEnd(); ?>

</div>