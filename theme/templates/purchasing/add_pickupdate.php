<?php

global $IC;
global $PC;
global $DC;

$this->pageTitle("Tilføj afhentningsdag");


$available_wednesdays = [];

for ($i = 0; $i <= 12; $i++) { 
	
	$date = date("Y-m-d", strtotime("Wednesday +$i weeks"));
	
	if(!$PC->getPickupdate(["pickupdate" => $date])) {
	
		$available_wednesdays[] = $date;
	
	}
}

$available_wednesdays = array_combine($available_wednesdays, $available_wednesdays);

$departments = $DC->getDepartments();

?>

<div class="scene add_pickupdate i:add_pickupdate">
	<h1>Opret ny afhentningsdag</h1>

	<?= $HTML->serverMessages(["type" => "error"]) ?>

	<div class="c-wrapper">
		<div class="c-one-half">

		<?= $PC->formStart("savePickupdate", array("class" => "i:defaultNew labelstyle:inject")) ?>
			<h3>Vælg dato</h3>
			<fieldset class="pickupdate">
				<?= $PC->input("pickupdate", ["type" => "select", "options" => $available_wednesdays]) ?>
			</fieldset>
			<? if($departments): ?>
			<h3>Vælg hvilke afdelinger der har åbent</h3>
			<fieldset class="departments">
				<? foreach($departments as $department): ?>
			
				<?= $PC->input("pickupdate_department_".$department["id"], ["type" => "checkbox", "label" => $department["name"], "value" => 1]) ?>
			
				<? endforeach; ?>
			</fieldset>
			<? endif; ?>
			<ul class="actions">
			<?= $PC->link("Annuller", "/indkoeb", array("class" => "button", "wrapper" => "li.cancel")) ?>
			<?= $PC->submit("Opret", ["wrapper" => "li.save", "class" => "primary"]); ?>
			</ul>

		<?= $PC->formEnd() ?>
		</div>
	</div>

</div>