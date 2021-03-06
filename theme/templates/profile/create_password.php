<?php
// Get variables from the controller
global $action;
global $model;

?>

<div class="scene login i:create_password">

	<h1>Opret adgangskode</h1>

	<?= $HTML->serverMessages() ?>

	<div class="password">
		<h2>Adgangskode</h2>
		<p>Som ny bruger skal du oprette en adgangskode.</p>
		

		<?= $model->formStart("setPassword", ["class" => "create_password"]) ?>

			<fieldset>
				<?= $model->input("new_password", array("required" => true, "label" => "Ny adgangskode", "hint_message" => "Indtast din nye adgangskode på mindst 8 anslag", "error_message" => "Ugyldig adgangskode")) ?>
				<?= $model->input("confirm_password", array("required" => true, "label" => "Bekræft adgangskode", "hint_message" => "Indtast din nye adgangskode igen for at bekræfte", "error_message" => "Adgangskoderne er ikke ens")) ?>
			</fieldset>
			<ul class="actions">
				<?= $model->submit("Gem", array("class" => "primary", "wrapper" => "li.save")) ?>
			</ul>
		<?= $model->formEnd() ?>
	</div>

</div>