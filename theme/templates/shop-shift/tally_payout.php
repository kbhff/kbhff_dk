<?php
$IC = new Items();
global $action;
global $TC;

include_once("classes/system/department.class.php");
$DC = new Department();

$tally_id = $action[1];
$tally = $TC->getTally(["id" => $tally_id]);
$department = $DC->getDepartment(["id" => $tally["department_id"]]);
?>

<div class="scene tally_payout">
	<h1>Ny udbetaling fra kassen</h1>
	<div class="c-wrapper">

		<?= $TC->formStart("kasse/$tally_id/udbetaling/addPayout", ["class" => "labelstyle:inject add_payout"]); ?>
	
			<fieldset>
				<?= $TC->input("payout_name", ["label" => "Hvad betales der for?"]); ?>
				<?= $TC->input("payout_amount", ["label" => "Beløb"]); ?>
			</fieldset>

			<ul class="actions">
				<?= $TC->submit("Gem", ["wrapper" => "li.save", "class" => "primary"]); ?>
			</ul>
		<?= $TC->formEnd(); ?>

	</div>


</div>
