<?php
// Get instanced class depending on what methods we'll need
$model = new User();

// Title which is displayed in the browser tab/titlebar
$this->pageTitle("Verificering");

?>

<div class="scene login i:forgot">
	<h1>Nulstil adgangskode</h1>
	<h2>Bekræft at du vil nulstille din adgangskode</h2>
	<p class="validateParagraph">
		<span class='highlight'>TAK.</span> 
		Vi har nu sendt dig en mail. I mailen er en kode, som du kan indtaste her, hvorefter du vil blive bedt om at vælge en ny adgangskode.
	</p>

	<?= $model->formStart("validateCode", ["class" => "verify_code"]) ?>

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
			<?= $model->input("reset-token"); ?>
		</fieldset>

		<ul class="actions">
			<?= $model->submit("Vælg ny adgangskode", array("class" => "primary", "wrapper" => "li.reset")) ?>
		</ul>

	<?= $model->formEnd() ?>

</div>
