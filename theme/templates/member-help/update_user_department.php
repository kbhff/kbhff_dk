<?php
global $model;
global $action;
global $IC;
global $itemtype;

include_once("classes/system/department.class.php");
$DC = new Department();
$UC = new SuperUser();
$IC = new Items();

$user_id = $action[1];
$departments = $DC->getDepartments();
$user_department = $UC->getUserDepartment(["user_id" => $user_id]);
$user = $UC->getKbhffUser(["user_id" => $user_id]);


$this->pageTitle("Afdelinger");
?>

<div class="scene update_department i:update_department">
	<h1>Afdelinger</h1>
	<h2>Her kan du skifte lokal afdeling for <?=$user["firstname"] ? $user["firstname"]: $user["nickname"]?>.</h2>

	<?= $UC->formStart("updateUserDepartment/$action[1]", ["class" => "form_department"]) ?> 
	
	<? if(message()->hasMessages()): ?>
	<div class="messages">
	<?
	$all_messages = message()->getMessages();
	message()->resetMessages();
	foreach($all_messages as $type => $messages):
		foreach($messages as $message): ?>
		<p class="<?= $type ?>"><?= $message ?></p>
		<? endforeach;?>
	<? endforeach;?>
	</div>
	<? endif; ?>
	
	
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