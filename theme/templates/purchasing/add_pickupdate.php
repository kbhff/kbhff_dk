<?php

global $IC;
global $PC;

$this->pageTitle("Tilføj afhentningsdag");

?>

<div class="scene add_pickupdate i:add_pickupdate">
	<h1>Opret ny afhentningsdag</h1>
	
	<?= $PC->formStart("savePickupdate", array("class" => "i:defaultNew labelstyle:inject")) ?>
		<fieldset>
			<?= $PC->input("pickupdate") ?>
		</fieldset>
		<ul class="actions">
		<?= $PC->link("Annuller", "/indkoeb", array("class" => "button", "wrapper" => "li.cancel")) ?>
		<?= $PC->submit("Opret", ["wrapper" => "li.save", "class" => "primary"]); ?>
		</ul>

	<?= $PC->formEnd() ?>

</div>