<?php
global $model;
global $action;
global $IC;
global $itemtype;

include_once("classes/system/department.class.php");
$DC = new Department();
$UC = new SuperUser();
$IC = new Items();
$SC = new Shop();

$user_id = $action[1];
$departments = $DC->getDepartments();
$user_department = $UC->getUserDepartment(["user_id" => $user_id]);
$user = $UC->getKbhffUser(["user_id" => $user_id]);

// get unshipped order_items for user
$unshipped_order_items = $SC->getOrderItems(["user_id" => $user_id, "where" => "soi.shipped_by IS NULL AND pp.pickupdate > CURDATE() AND i.itemtype REGEXP '^(legacy)?product'"]);

$this->pageTitle("Afdelinger");
?>

<div class="scene update_userinfo_form i:update_userinfo_form">
	<h1>Afdelinger</h1>
	<h2>Her kan du skifte lokal afdeling for <?=$user["firstname"] ? $user["firstname"]: $user["nickname"]?>.</h2>

	<? if($unshipped_order_items): ?>
	<p class="warning"><strong>NB!</strong> Dette medlem har fremtidige bestillinger i systemet. Hvis du ændrer medlemmets afdeling, bliver disse bestillinger IKKE flyttet med til medlemmets nye afdeling, men skal afhentes i vedkommendes gamle afdeling. Hvis medlemmet ønsker at afhente dem i sin nye afdeling, skal vedkommende skrive til <a href="mailto:it@kbhff.dk">it@kbhff.dk</a>. 
	</p>
	<? endif; ?>

	<?= $UC->formStart("updateUserDepartment/$action[1]", ["class" => "form_department"]) ?> 
	
	<?= $HTML->serverMessages() ?>
	
	
	<fieldset>
		<?= $UC->input("department_id", [
			"type" => "select", 
			"options" => $DC->toOptions($departments, "id", "name", ["add" => ["" => "Vælg afdeling"]]),
			"value" => $user_department["id"],
			"class" => "department_input"
			]); 
		?>
	</fieldset>

	<ul class="actions">
		<li class="cancel"><a href="/medlemshjaelp/brugerprofil/<?=$action[1]?>" class="button">Annullér</a></li>
		<?= $UC->submit("Opdater", array("class" => "primary", "wrapper" => "li.save")) ?>
	</ul>

	<?= $UC->formEnd() ?>

</div>