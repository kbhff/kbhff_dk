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

<div class="scene tally_revenue">
	<h1>Ny indtægt</h1>
	<div class="c-wrapper">

		<?= $TC->formStart("kasse/$tally_id/andre-indtaegter/addRevenue", ["class" => "labelstyle:inject add_revenue"]); ?>
			<fieldset>
				<?= $TC->input("revenue_name"); ?>
				<?= $TC->input("revenue_amount"); ?>
			</fieldset>

			<ul class="actions">
				<?= $TC->submit("Gem", ["wrapper" => "li.save", "class" => "primary"]); ?>
			</ul>
		<?= $TC->formEnd(); ?>

	</div>

</div>
