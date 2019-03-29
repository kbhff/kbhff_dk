<?php
include_once("classes/system/department.class.php");
$DC = new Department();
$UC = new User();
$departments = $DC->getDepartmentsAcceptSignups();
$user_department = $UC->getUserDepartment();

$this->pageTitle("Afdelinger");
?>

<div class="scene update_department i:update_department">
	<h1>Afdelinger</h1>
	<h2>Her kan du skifte din lokale afdeling.</h2>

	<?= $UC->formStart("updateUserDepartment", ["class" => "form_department"]) ?> 

<?	// print error messages
	if(message()->hasMessages(array("type" => "error"))): ?>
		<p class="errormessage">
<?		$messages = message()->getMessages(array("type" => "error"));
		message()->resetMessages();
		foreach($messages as $message): ?>
			<?= $message ?><br>
<?		endforeach;?>
		</p>
<?	endif; ?>

		<fieldset>
			<?= $UC->input("department_id", [
				"type" => "select", 
				"options" => $DC->toOptions($departments, "id", "name", ["add" => ["" => "Vælg afdeling"]]),
				"value" => $user_department["id"]
				]); 
			?>
		</fieldset>

		<ul class="actions">
			<li class="cancel"><a href="/" class="button">Annullér</a></li>
			<?= $UC->submit("Opdater", array("class" => "primary", "wrapper" => "li.save")) ?>
		</ul>

	<?= $UC->formEnd() ?>

</div>