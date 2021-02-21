<?php

global $DC;
global $IC;
$model = $IC->typeObject("productweeklybag");

$this->pageTitle("Tilføj nyt produkt");

?>

<div class="scene add_product i:add_product">
	<h1>Opret nyt produkt</h1>
	<h2>Produktoplysninger</h2>
	
	<?= $model->formStart("addNewProduct", ["class" => "labelstyle:inject add"]); ?>
		<fieldset class="details">
			<?= $model->input("name", ["label" => "Produktnavn", "hint_message" => "Giv produktet et navn", "error_message" => "Produktet må have et navn"]); ?>
			<?= $model->input("price_1", ["type" => "number", "label" => "Pris 1 (Frivillig-medlem)", "required" => true]); ?>
			<?= $model->input("price_2", ["type" => "number", "label" => "Pris 2 (Støttemedlem)", "required" => true]); ?>
			<?= $model->input("single_media", ["label" => "Produktbillede"]); ?>
			<?= $model->input("description", ["label" => "Produktbeskrivelse"]); ?>
			<?= $model->input("product_type", ["type" => "select", "label" => "Produkttype", "options" => ["productweeklybag" => "Ugens pose", "productseasonalbag" => "Sæsonpose", "productcanvasbag" => "Lærredspose", "productassorted" => "Løssalg"], "required" => true]); ?>
		</fieldset>

		<h3>Bestilling og afhentning</h3>
		<fieldset class="availability">
			<?= $model->input("start_availability_date", ["label" => "Fra og med dato", "hint_message" => "Hvornår bliver produktet tilgængeligt for medlemmerne?.",
					"error_message" => "Angiv hvornår produktet bliver tilgængeligt for medlemmerne."]); ?>
			<?= $model->input("end_availability_date", ["label" => "Til og med dato (kan udelades)", "hint_message" => "Hvornår ophører produktet med at være tilgængelig for medlemmerne?.", "error_message" => "Angiv hvornår produktet udløber."]); ?>
		</fieldset>
		<ul class="actions">
		<?= $model->link("Annuller", "/indkoeb", array("class" => "button", "wrapper" => "li.cancel")) ?>
		<?= $model->submit("Opret", ["wrapper" => "li.save", "class" => "primary"]); ?>
		</ul>
	<?= $model->formEnd(); ?>

</div>