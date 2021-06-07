<?php
global $action;
global $IC;
global $UC;
global $DC;
global $model;

$user = $UC->getKbhffUser(["user_id" => session()->value("user_id")]);
$department = $user ? $user["department"] : false;

$departments = $DC->getDepartments();

$send_to_department_members = $this->checkpermissions("/massemail", "/sendToDepartmentMembers", ["/sendToDepartmentMembers" => true]);
$send_to_all_members = $this->checkpermissions("/massemail", "/sendToAllMembers", ["/sendToAllMembers" => true]);


?>
<div class="scene mass_mail i:scene" itemscope itemtype="http://schema.org/NewsArticle">
	<h1>Massemail</h1>
	<? if($send_to_department_members && $send_to_all_members): ?>
	<p>Her kan du udsende beskeder til KBHFF's medlemmer. Du kan vælge at sende en mail til alle medlemmer eller blot til medlemmerne af en specifik afdeling.</p>
	<? else: ?>
	<p>Her kan du udsende beskeder til medlemmerne af din afdeling (<?= $department["name"] ?>).</p>
	<? endif; ?>
	
	<div class="item message i:defaultEdit i:collapseHeader">
		<?= $model->formStart("sendKbhffMessage", array("class" => "labelstyle:inject")) ?>

			<? if($send_to_department_members && $send_to_all_members): ?>
			<fieldset>
				<h3>Vælg modtagere</h3>
				<?= $model->input("department_id", ["options" => $model->toOptions($departments, "id", "name", ["add" => [
					"all_departments" => "Alle afdelinger",
					"all_departments_all_members" => "Alle afdelinger (inkl. inaktive medlemmer)"
				]])]) ?>
			</fieldset>
			<? endif; ?>

			<fieldset>
				<h3>Indhold</h3>
				<?= $model->input("name", ["label" => "Emne"]) ?>
				<?= $model->input("description", ["class" => "autoexpand short", "label" => "Kort tekst til mail-preview"]) ?>
				<?= $model->input("html", ["label" => "Indhold"]) ?>
			</fieldset>

			<ul class="actions">
				<?= $model->submit("Send", array("class" => "primary", "wrapper" => "li.submit")) ?>
			</ul>

		<?= $model->formEnd() ?>
	</div>


</div>