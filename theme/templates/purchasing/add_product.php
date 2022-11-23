<?php

global $DC;
global $IC;
$model = $IC->typeObject("product");

$this->pageTitle("Tilføj nyt produkt");

?>

<div class="scene add_product i:add_edit_product">
	<h1>Opret nyt produkt</h1>
	<h2>Produktoplysninger</h2>

	<?= $HTML->serverMessages(); ?>
	
	<?= $model->formStart("addNewProduct", ["class" => "labelstyle:inject add", "enctype" => "multipart/form-data"]); ?>

		<div class="c-wrapper">
			<div class="c-one-half">

				<fieldset class="details">
					<?= $model->input("name", ["label" => "Produktnavn", "hint_message" => "Giv produktet et navn", "error_message" => "Produktet skal have et navn"]); ?>
					<?= $model->input("price_1", ["label" => "Pris 1 (Frivillig-medlem)", "required" => true, "hint_message" => "Hvad skal produktet koste for Frivillig-medlemmer?", "error_message" => "Angiv en pris."]); ?>
					<?= $model->input("price_2", ["label" => "Pris 2 (Støttemedlem)", "required" => true, "hint_message" => "Hvad skal produktet koste for Støttemedlemmer?", "error_message" => "Angiv en pris."]); ?>
					<?= $model->input("description", ["label" => "Produktbeskrivelse", "hint_message" => "Beskriv produktet", "error_message" => "Produktet skal have en beskrivelse."]); ?>
					<?= $model->input("product_type", ["label" => "Produkttype", "options" => ["" => "Vælg type", "productweeklybag" => "Ugens pose", "productseasonalbag" => "Sæsonpose", "productcanvasbag" => "Lærredspose", "productassorted" => "Løssalg"], "hint_message" => "Vælg produkttype", "error_message" => "Du skal vælge en produkttype."]); ?>
				</fieldset>


			</div>
			<div class="c-one-half">

				<h3>Produktbillede</h3>
				<fieldset class="media">
					<?= $model->input("single_media", ["label" => "Produktbillede", "hint_message" => "Tryk her for at vælge et billede, eller træk et billede ind på det grå felt. Størrelse mindst 960x960 px. Tilladte formater: PNG og JPG.", "error_message" => "Billedet lever ikke op til kravene.",]); ?>
				</fieldset>

				<h3>Tilgængelighed fra producent</h3>
				<fieldset class="availability">
					<!-- <p>Angiv herunder hvornår produktet skal være tilgængeligt i webshoppen.Første mulige afhentningsdag er den første onsdag, der ligger mindst 7 dage efter den valgte startdato.</p> -->
					<?= $model->input("start_availability_date", ["label" => "Fra og med dato", "hint_message" => "Hvornår bliver produktet tilgængeligt fra producenten?.",
							"error_message" => "Angiv hvornår produktet bliver tilgængeligt fra producenten."]); ?>
					<p class="first_pickupdate">Første mulige afhentningsdag: <span>-</span></p>
					<?= $model->input("end_availability_date", ["label" => "Til og med dato (kan udelades)", "hint_message" => "Hvornår ophører produktet med at være tilgængeligt fra producenten? Kan udelades.", "error_message" => "Angiv hvornår produktet udløber."]); ?>
					<p class="last_pickupdate">Sidste mulige afhentningsdag: <span>-</span></p>
				</fieldset>

			</div>
		</div>

		<ul class="actions">
			<?= $model->link("Annuller", "/indkoeb", array("class" => "button", "wrapper" => "li.cancel")) ?>
			<?= $model->submit("Opret", ["wrapper" => "li.save", "class" => "primary"]); ?>
		</ul>

	<?= $model->formEnd(); ?>

</div>
