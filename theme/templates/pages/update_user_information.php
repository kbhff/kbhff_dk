<?php
$UC = new User();
$user = $UC->getKbhffUser();

$this->pageTitle("Brugeroplysninger");
?>

<div class="scene user_information i:user_information">
	<h1>Brugeroplysninger</h1>
	<h2>Her kan du rette i dine brugeroplysninger.</h2>

	<?= $UC->formStart("updateUserInformation", ["class" => "form_user"]) ?>

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
			<?= 
				$UC->input("nickname", [
					"type" => "string",
					"required" => false,
					"label" => "dit kaldenavn",
					"hint_message" => "Skriv et navn du kan lide",
					"error_message" => "",
				]),
			
				$UC->input("email", [
					"type" => "email",
					"label" => "din e-mail",
					"hint_message" => "Din email du bruger til at logge ind og få beskeder med",
					"error_message" => "Ugyldig email",
				]),

				$UC->input("mobile", [
					"label" => "dit nummer"
				]);
			?>
		</fieldset>

		<ul class="actions">
			<?= $UC->submit("Opdater", array("class" => "primary", "wrapper" => "li.update")) ?>
		</ul>
	<?= $UC->formEnd() ?>

</div>