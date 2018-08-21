<?php
	
$model = new Model();

$this->pageTitle("Glemt password");
?>
<div class="scene login i:forgot">
	<h1>Har du glemt din kode?</h1>
	<h2>Du kan logge ind med din email-adresse.</h2>
	<p>
		Ønsker du at nulstille dit password, så indtast din e-mail nedenfor. 
		Vi sender dig en mail med dine login-oplysninger og kode til at nulstille dit password.
	</p>

	<?= $model->formStart("requestReset", ["class" => "request_password"]) ?>

<?	if(message()->hasMessages(array("type" => "error"))): ?>
		<p class="errormessage">
<?		$messages = message()->getMessages(array("type" => "error"));
		message()->resetMessages();
		foreach($messages as $message): ?>
			<?= $message ?><br>
<?		endforeach;?>
		</p>
<?	endif; ?>

		<fieldset>
			<?= $model->input("username", array(
					"type" => "string", 
					"label" => "Bruger login", 
					"required" => true, 
					"pattern" => "^(1|[0-9]{4,5}|[\+0-9\-\.\s\(\)]{5,18}|[\w\.\-_]+@[\w\-\.]+\.\w{2,10})$", 
					"hint_message" => "Email, medlemsnummer eller telefonnummer", 
					"error_message" => "Ugyldig bruger"
				));
			?>
		</fieldset>

		<ul class="actions">
			<?= $model->submit("Send mig en mail", array("class" => "primary", "wrapper" => "li.reset")) ?>
		</ul>
	<?= $model->formEnd() ?>

</div>
