<?php

global $IC;
global $PC;

$this->pageTitle("Tilføj afhentningsdag");


$available_wednesdays = [];

for ($i = 0; $i <= 12; $i++) { 
	
	$date = date("Y-m-d", strtotime("Wednesday +$i weeks"));
	
	if(!$PC->getPickupdate(["pickupdate" => $date])) {
	
		$available_wednesdays[] = $date;
	
	}
}

$available_wednesdays = array_combine($available_wednesdays, $available_wednesdays);

?>

<div class="scene add_pickupdate i:add_pickupdate">
	<h1>Opret ny afhentningsdag</h1>
	<?= $HTML->serverMessages(["type" => "error"]) ?>

	<div class="c-wrapper">
	<?= $PC->formStart("savePickupdate", array("class" => "i:defaultNew labelstyle:inject")) ?>
		<fieldset>
			<?= $PC->input("pickupdate", ["type" => "select", "options" => $available_wednesdays]) ?>
		</fieldset>
		<ul class="actions">
		<?= $PC->link("Annuller", "/indkoeb", array("class" => "button", "wrapper" => "li.cancel")) ?>
		<?= $PC->submit("Opret", ["wrapper" => "li.save", "class" => "primary"]); ?>
		</ul>

	<?= $PC->formEnd() ?>
	</div>
	

</div>