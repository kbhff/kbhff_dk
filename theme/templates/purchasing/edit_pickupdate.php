<?php

global $IC;
global $PC;
global $DC;
global $action;

$this->pageTitle("Rediger afhentningsdag");

$pickupdate_id = $action[1];

$pickupdate = $PC->getPickupdate(["id" => $pickupdate_id]);
$departments = $DC->getDepartments();
$pickupdate_departments = $DC->getPickupdateDepartments($pickupdate_id);
?>

<div class="scene edit_pickupdate i:edit_pickupdate">
	<h1>Rediger afhentningsdag: <?= $pickupdate["pickupdate"] ?></h1>
	<p>Her kan du redigere, hvilke afdelinger der har åben på denne afhentningsdag. Fjern fluebenet ved en afdeling for at markere den som lukket, og tryk på Gem. Hvis du lukker en afdeling, hvortil der er knyttet bestillinger på den givne dato, vil IT-gruppen vil adviseret og tage sig af det.</p>
	
	<?= $PC->formStart("updatePickupdateDepartments/".$pickupdate_id, array("class" => "i:defaultNew labelstyle:inject")) ?>
		<fieldset>
			<? foreach($departments as $department): 
				$department_open = arrayKeyValue($pickupdate_departments, "id", $department["id"]);	
			?>
			
			<?= $PC->input("pickupdate_department_".$department["id"], ["type" => "checkbox", "label" => $department["name"], "value" => $department_open !== false ? 1 : 0]) ?>
			
			<? endforeach; ?>
		</fieldset>
		<ul class="actions">
		<?= $PC->link("Annuller", "/indkoeb", array("class" => "button", "wrapper" => "li.cancel")) ?>
		<?= $PC->submit("Gem", ["wrapper" => "li.save", "class" => "primary"]); ?>
		</ul>

	<?= $PC->formEnd() ?>

	<h3>Slet afhentningsdag</h3>
	<p><span class="warning">Bemærk!</span> Dette sletter afhentningsdagen fra systemet og påvirker således alle afdelinger.</p>
	<p>Hvis du sletter en afhentningsdag, hvortil der er knyttet bestillinger, vil IT-gruppen blive adviseret og tage sig af det.</p>
	<ul class="actions">
		<?= $HTML->oneButtonForm("Slet afhentningsdag", "/janitor/pickupdate/deletePickupdate/".$pickupdate_id, [
			"confirm-value" => "Bekræft sletning",
			"wait-value" => "Vent ...",
			"wrapper" => "li.delete", 
			"success-location" => "/indkoeb"
			]) 
		?>
	</ul>
	

</div>