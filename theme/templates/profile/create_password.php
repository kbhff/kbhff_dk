<?php
// Get variables from the controller
global $action;
global $model;

?>

<div class="scene login i:create_password">

	<h1>Opret adgangskode</h1>

	<?	// Display any backend generated messages
		if(message()->hasMessages()): ?>
		
			<p class="errormessage">
		<?	$messages = message()->getMessages(array("type" => "error"));
			foreach($messages as $message): ?>
				<?= $message ?><br>
		<?	endforeach;?>
			</p>

			<p class="message">
		<?	$messages = message()->getMessages(array("type" => "message"));
			foreach($messages as $message): ?>
				<?= $message ?><br>
		<?	endforeach; ?>
			</p>

			<? message()->resetMessages(); ?>
	<?	endif; ?>

	<div class="password">
		<h2>Adgangskode</h2>
		<p>Som ny bruger skal du oprette en adgangskode.</p>
		

		<?= $model->formStart("setPassword", ["class" => "create_password"]) ?>

			<fieldset>
				<?= $model->input("new_password", array("required" => true, "label" => "Ny adgangskode", "hint_message" => "Indtast din nye adgangskode på 8-20 anslag", "error_message" => "Ugyldig adgangskode")) ?>
				<?= $model->input("confirm_password", array("required" => true, "label" => "Bekræft adgangskode", "hint_message" => "Indtast din nye adgangskode igen for at bekræfte", "error_message" => "Adgangskoderne er ikke ens")) ?>
			</fieldset>
			<ul class="actions">
				<?= $model->submit("Gem", array("class" => "primary", "wrapper" => "li.save")) ?>
			</ul>
		<?= $model->formEnd() ?>
	</div>

</div>