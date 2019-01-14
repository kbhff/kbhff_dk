<?php
// Get instanced class depending on what methods we'll need
$model = new User();

// Title which is displayed in the browser tab/titlebar
$this->pageTitle("Verificering");

?>

<div class="scene login i:forgot">
	<h1>Nulstil password</h1>
	<h2>Verificér at du vil nulstille dit password</h2>
	<p class="validateParagraph">
		<span class='highlight'>TAK.</span> 
		Vi har nu sendt dig en mail. I mailen er en kode, 
		som du kan indtaste her og hvorefter du vil blive bedt om at vælge et nyt password.
	</p>

	<?= $model->formStart("validateCode", ["class" => "verify_code"]) ?>

		<? if(message()->hasMessages(array("type" => "error"))): ?>
				<p class="errormessage">
			<?	$messages = message()->getMessages(array("type" => "error"));
				message()->resetMessages();
				foreach($messages as $message): ?>
					<?= $message ?><br>
			<?	endforeach; ?>
				</p>
		<? endif; ?>

		<fieldset>
			<?= $model->input("reset-token"); ?>
		</fieldset>

		<ul class="actions">
			<?= $model->submit("Vælg nyt password", array("class" => "primary", "wrapper" => "li.reset")) ?>
		</ul>

	<?= $model->formEnd() ?>

</div>
