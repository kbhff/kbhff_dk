<?php

global $DC;
global $IC;
$model = $IC->typeObject("product");

$this->pageTitle("Tilføj nyt produkt");

?>

<div class="scene add_product i:add_edit_product">
	<h1>Opret nyt produkt</h1>
	<h2>Produkttype og navn</h2>

	<?= $HTML->serverMessages(); ?>
	

	<div class="c-wrapper">
		<div class="c-one-half">

			<?= $model->formStart("addNewProduct", ["class" => "labelstyle:inject basics", "enctype" => "multipart/form-data"]); ?>

				<fieldset class="details">
					<?= $model->input("product_type", [
						"label" => "Produkttype", 
						"required" => true,
						"options" => [
							"" => "Vælg type", 
							"productweeklybag" => "Ugens pose", 
							"productseasonalbag" => "Sæsonpose", 
							"productcanvasbag" => "Lærredspose", 
							"productassorted" => "Løssalg"
						], 
						"hint_message" => "Vælg produkttype", 
						"error_message" => "Du skal vælge en produkttype."
					]); ?>
					<?= $model->input("name", [
						"label" => "Produktnavn", 
						"hint_message" => "Giv produktet et navn", 
						"error_message" => "Produktet skal have et navn"
					]); ?>
					<?//= $model->input("price_1", ["label" => "Pris 1 (Frivillig-medlem)", "required" => true, "hint_message" => "Hvad skal produktet koste for Frivillig-medlemmer?", "error_message" => "Angiv en pris."]); ?>
					<?//= $model->input("price_2", ["label" => "Pris 2 (Støttemedlem)", "required" => true, "hint_message" => "Hvad skal produktet koste for Støttemedlemmer?", "error_message" => "Angiv en pris."]); ?>
					<? /*= $model->input("description", [
						"label" => "Produktbeskrivelse", 
						"hint_message" => "Beskriv produktet", 
						"error_message" => "Produktet skal have en beskrivelse."
					]);*/ ?>
				</fieldset>

				<ul class="actions">
					<?= $model->link("Annuller", "/indkoeb", array("class" => "button", "wrapper" => "li.cancel")) ?>
					<?= $model->submit("Opret", ["wrapper" => "li.save", "class" => "primary"]); ?>
				</ul>

			<?= $model->formEnd(); ?>

		</div>

	</div>

</div>
