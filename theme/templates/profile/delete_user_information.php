<?php
$UC = new User();
$user = $UC->getKbhffUser();

$this->pageTitle("Udmeldelse");
?>

<div class="scene delete_user_information i:delete_user_information">
	<h1>Vil du udmeldes?</h1>
	<h2>Bemærk, at denne handling er uomgørlig.</h2>

	<?= $UC->formStart("deleteUserInformation", array("class" => "confirm_cancellation")) ?>

		<?= $HTML->serverMessages(["type" => "error"]) ?>

		<fieldset>
			<?= $UC->input("password", [
				"min" => 1,
				"required" => true, 
				"hint_message" => "Skriv din adgangskode for at bekræfte din udmeldelse.", 
				"error_message" => "Ugyldig adgangskode"
			]) ?>
		</fieldset>
		
		<ul class="actions">
			<?= $UC->submit("Farvel", array("class" => "primary", "wrapper" => "li.save")) ?>

		</ul>

	<?= $UC->formEnd() ?>


</div>
