<?php
include_once("classes/system/department.class.php");
$DC = new Department();
$UC = new User();
$user = $UC->getKbhffUser();

global $action;
global $IC;
$model = $IC->typeObject("product");

$item_id = $action[1];
$product = $IC->getItem(array("id" => $item_id, "extend" => ["mediae" => true, "prices" => true, "tags" => true]));

$first_possible_pickupdate = date("d.m.Y", strtotime($product["start_availability_date"]." Wednesday"));
$last_possible_pickupdate = $product["end_availability_date"] ? date("d.m.Y", strtotime($product["end_availability_date"]." last Wednesday")) : false;

$file_input_value = $IC->filterMediae($product, "single_media");


// Get price types for memberships
$membership_price_types = $model->getMembershipPriceTypes($product);

$available_tags = $IC->getTags(["context" => "productgroup"]);


$this->pageTitle("Rediger produkt");

?>

<div class="scene add_edit_product edit_product i:add_edit_product">
	<h1>Rediger produkt</h1>

	<ul class="actions">
		<?= $HTML->link("Tilbage til listen", "/indkoeb", array("class" => "button", "wrapper" => "li.back")) ?>
		<? if($product["status"]): ?>
		<?= $model->oneButtonForm("Arkivér produkt", "/indkoeb/disableProduct/$item_id", ["wrapper" => "li.deactivate", "confirm-value" => "Bekræft", "wait-value" => "Vent...", "dom-submit" => true]) ?>
		<? else: ?>
		<?= $model->oneButtonForm("Genaktiver produkt", "/indkoeb/enableProduct/$item_id", ["wrapper" => "li.deactivate", "confirm-value" => "Bekræft", "wait-value" => "Vent...", "dom-submit" => true]) ?>
		<? endif; ?>
	</ul>


	<?= $HTML->serverMessages() ?>


	<div class="c-wrapper basics">
		<?= $model->formStart("updateProductBasics/".$product["id"], ["class" => "labelstyle:inject basics", "enctype" => "multipart/form-data"]); ?>
			<div class="c-one-half">

				<h3>Produktnavn og beskrivelse</h3>
				<fieldset class="details">
					<?= $model->input("name", [
						"label" => "Produktnavn", 
						"hint_message" => "Giv produktet et navn", 
						"error_message" => "Produktet må have et navn", 
						"value" => $product["name"]
					]); ?>
					<?= $model->input("description", [
						"label" => "Produktbeskrivelse", 
						"value" => $product["description"], 
						"hint_message" => "Beskriv produktet", 
						"error_message" => "Produktet skal have en beskrivelse."
					]); ?>
				</fieldset>

			</div>
			<div class="c-one-half">

				<h3>Produktbillede</h3>
				<fieldset class="media">
					<?= $model->input("single_media", ["label" => "Produktbillede", "hint_message" => "Tryk her for at vælge et billede, eller træk et billede ind på det grå felt. Størrelse mindst 960x960 px. Tilladte formater: PNG og JPG.", "error_message" => "Billedet lever ikke op til kravene.", "value" => $file_input_value]); ?>
				</fieldset>

			</div>
			<ul class="actions">
				<?= $model->submit("Opdater", ["wrapper" => "li.save", "class" => "primary"]); ?>
			</ul>
		<?= $model->formEnd(); ?>
	</div>


	<div class="c-wrapper c-box availability">
		<h3>Tilgængelighed fra producent</h3>
		<?= $model->formStart("updateProductAvailability/".$product["id"], ["class" => "labelstyle:inject availability", "enctype" => "multipart/form-data"]); ?>
			<div class="c-one-half">

				<fieldset class="availability_start">
					<?= $model->input("start_availability_date", [
						"label" => "Fra og med dato", 
						"hint_message" => "Hvornår bliver produktet tilgængeligt fra producenten?",
						"error_message" => "Angiv hvornår produktet bliver tilgængeligt fra producenten.", 
						"value" => $product["start_availability_date"]
					]); ?>
					<p class="first_pickupdate">Første mulige afhentningsdag: <span><?= $first_possible_pickupdate ?: "-" ?></span></p>
				</fieldset>

			</div>
			<div class="c-one-half">

				<fieldset class="availability_end">
					<?= $model->input("end_availability_date", [
						"label" => "Til og med dato (kan udelades)", 
						"hint_message" => "Hvornår ophører produktet med at være tilgængelig fra producenten? Kan udelades.", 
						"error_message" => "Angiv hvornår produktet udløber.", 
						"value" => $product["end_availability_date"] ?: false
					]); ?>
					<p class="last_pickupdate">Sidste mulige afhentningsdag: <span><?= $last_possible_pickupdate ?: "-" ?></span></p>
				</fieldset>

			</div>
			<ul class="actions">
				<?= $model->submit("Opdater", ["wrapper" => "li.save", "class" => "primary"]); ?>
			</ul>
		<?= $model->formEnd(); ?>
	</div>


	<div class="c-wrapper">
		<div class="c-one-half prices c-box">
			<?= $model->formStart("updateProductPrices/".$product["id"], ["class" => "labelstyle:inject prices", "enctype" => "multipart/form-data"]); ?>

				<h3>Priser</h3>
				<fieldset class="prices">
					<? foreach($membership_price_types as $price_type): ?>
					<?= $model->input("price[".$price_type["id"]."]", [
						"type" => "number", 
						"label" => "Pris for ".$price_type["name"], 
						"required" => true, 
						"value" => $price_type["price"] ? $price_type["price"]["price"] : false, 
						"hint_message" => "Hvad skal produktet koste for ".$price_type["name"]."-medlemmer?", 
						"error_message" => "Angiv en pris."
					]); ?>
					<? endforeach;?>
				</fieldset>

				<ul class="actions">
					<?= $model->submit("Opdater", ["wrapper" => "li.save", "class" => "primary"]); ?>
				</ul>

			<?= $model->formEnd(); ?>
		</div>

		<div class="c-one-half tags c-box">
			<?= $model->formStart("updateProductTags/".$product["id"], ["class" => "labelstyle:inject tags", "enctype" => "multipart/form-data"]); ?>

				<h3>Tags</h3>
				<fieldset class="tags">
					<? foreach($available_tags as $tag): ?>
					<?= $model->input("tag[".$tag["id"]."]", [
						"type" => "checkbox", 
						"label" => $tag["value"], 
						"value" => $product["tags"] && arrayKeyValue($product["tags"], "id", $tag["id"]) !== false ? 1 : 0, 
						"hint_message" => "Tilføj '".$tag["value"]."' tagget til produktet.", 
						"error_message" => "Der skete en fejl ved tilføjelse af tagget."
					]); ?>
					<? endforeach; ?>
				</fieldset>

				<ul class="actions">
					<?= $model->submit("Opdater", ["wrapper" => "li.save", "class" => "primary"]); ?>
				</ul>

			<?= $model->formEnd(); ?>
		</div>
	</div>

</div>