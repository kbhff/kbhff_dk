<?php
// TODO: Explain where we get $action and $model from
global $action;
global $model;

// Get the saved reset-token in current browser session
$reset_token = session()->value("temp-reset-token");

?>
<div class="scene i:scene defaultEdit userEdit profileEdit">
	<h1>Reset password</h1>

	<div class="password">
		<h2>Password</h2>

		<!-- Form -->
		<?= $model->formStart("resetPassword", array("class" => "reset_password")) ?>
			<!-- Hidden field with the reset-token we use to validate -->
			<?= $model->input("reset-token", array("type" => "hidden", "value" => $reset_token)) ?>

			<!-- Input fields -->
			<fieldset>
				<?= $model->input("new_password", array("required" => true, "label" => "Ny adgangskode", "hint_message" => "Skriv dit nye adgangskode – 8-20 tegn", "error_message" => "Ugyldig adgangskode")) ?>
				<?= $model->input("confirm_password", array("required" => true, "label" => "Gentag ny adgangskode", "hint_message" => "Skriv dit nye password igen for at bekræfte", "error_message" => "Dine adgangskoder stemmer ikke overens.")) ?>
			</fieldset>

			<!-- Buttons -->
			<ul class="actions">
				<?= $model->submit("Bekræft ny adgangskode", array("class" => "primary", "wrapper" => "li.save")) ?>
			</ul>
		<?= $model->formEnd() ?>

	</div>

</div>
