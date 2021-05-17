<?php
include_once("classes/system/department.class.php");
$DC = new Department();
$UC = new User();
$SC = new Shop();
$departments = $DC->getDepartmentsAcceptSignups();
$user_department = $UC->getUserDepartment();

// get unshipped order_items for this user
$unshipped_order_items = $SC->getOrderItems(["user_id" => session()->value("user_id"), "where" => "shipped_by IS NULL"]);


$this->pageTitle("Afdelinger");
?>

<div class="scene update_userinfo_form i:update_userinfo_form">
	<h1>Afdelinger</h1>
	<h2>Her kan du skifte din lokale afdeling.</h2>

	<? if($unshipped_order_items): ?>
	<p class="warning"><span class="nb">NB!</span> Du har fremtidige bestillinger i systemet. Hvis du skifter afdeling, bliver disse bestillinger IKKE flyttet med til din nye afdeling, men skal afhentes i din gamle afdeling. Hvis du ønsker at afhente dem i din nye afdeling, så skriv til <a href="mailto:it@kbhff.dk">it@kbhff.dk</a>. 
	</p>
	<? endif; ?>

	<?= $UC->formStart("updateUserDepartment", ["class" => "form_department"]) ?> 

		<?= $HTML->serverMessages(["type" => "error"]); ?>

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