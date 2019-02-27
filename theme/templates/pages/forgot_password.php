<?php
// Get instanced class depending on what methods we'll need
$model = new Model();

// Title which is displayed in the browser tab/titlebar
$this->pageTitle("Glemt adgangskode");
?>
<div class="scene login i:forgot">
	<h1>Glemt adgangskode?</h1>

	<p>
		Har du glemt din adgangskode? Indtast din e-mailadresse nedenfor,
		så vi sender dig en mail med en ny kode til at nulstille din adgangskode.
	</p>

	<?= $model->formStart("requestReset", ["class" => "request_password"]) ?>
	
		<?	// Display any backend generated messages
			if(message()->hasMessages(array("type" => "error"))): ?>
				<p class="errormessage">
			<?	$messages = message()->getMessages(array("type" => "error"));
				message()->resetMessages();
				foreach($messages as $message): ?>
					<?= $message ?><br>
			<?	endforeach; ?>
				</p>
		<?	endif; ?>

		<fieldset>
			<?= $model->input("username", array(
					"type" => "string",
					"label" => "Emailadresse",
					"required" => true,
					"pattern" => "^(1|[0-9]{4,5}|[\+0-9\-\.\s\(\)]{5,18}|[\w\.\-_]+@[\w\-\.]+\.\w{2,10})$",
					"hint_message" => "Skriv den emailadresse, som du har registreret hos KBHFF.",
					"error_message" => "Du skal angive en gyldig emailadresse."
				));
			?>
		</fieldset>

		<ul class="actions">
			<?= $model->submit("Send mig en mail", array("class" => "primary", "wrapper" => "li.reset")) ?>
		</ul>

	<?= $model->formEnd() ?>

</div>
