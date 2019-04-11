<?php
// Get variables from the controller
global $action;
global $model;

// Get the saved reset-token in current browser session
$reset_token = session()->value("temp-reset-token");

?>
<div class="scene i:scene">
	<h1>Opdatér adgangskode</h1>

	<div class="password">
		<h2>Adgangskode</h2>

		<?= $model->formStart("resetPassword", array("class" => "reset_password")) ?>
			<?
				// Send reset token along with the rest of the form
				print $model->input("reset-token", array("type" => "hidden", "value" => $reset_token))
			?>

			<fieldset>
				<?= $model->input("new_password", array("required" => true, "label" => "Ny adgangskode", "hint_message" => "Skriv dit nye adgangskode – mere end 8 tegn", "error_message" => "Ugyldig adgangskode")) ?>
				<?= $model->input("confirm_password", array("required" => true, "label" => "Gentag ny adgangskode", "hint_message" => "Skriv din nye adgangskode igen for at bekræfte", "error_message" => "Dine adgangskoder stemmer ikke overens.")) ?>
			</fieldset>

			<ul class="actions">
				<?= $model->submit("Bekræft ny adgangskode", array("class" => "primary", "wrapper" => "li.save")) ?>
			</ul>
		<?= $model->formEnd() ?>

	</div>

</div>
