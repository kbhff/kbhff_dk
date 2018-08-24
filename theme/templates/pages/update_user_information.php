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
					"label" => "dit kaldenavn",
					"value" => $user["nickname"],
					"required" => false,
					"hint_message" => "Skriv dit kaldenavn (kan også bare være dit navn)",
					"error_message" => "",
				]),
			
				$UC->input("firstname", [
					"type" => "string",
					"label" => "dit fornavn",
					"value" => $user["firstname"],
					"required" => false,
					"hint_message" => "Skriv dit fornavn",
					"error_message" => "",
				]),

				$UC->input("lastname", [
					"type" => "string",
					"label" => "dit efternavn",
					"value" => $user["lastname"],
					"required" => false,
					"hint_message" => "Skriv dit efternavn",
					"error_message" => "",
				]),

				$UC->input("email", [
					"type" => "email",
					"label" => "din e-mail",
					"value" => $user["email"],
					"hint_message" => "Din email du bruger til at logge ind og få beskeder med",
					"error_message" => "Ugyldig email",
				]),

				$UC->input("mobile", [
					"label" => "dit nummer",
					"value" => $user["mobile"],
				]);
			?>
		</fieldset>

		<ul class="actions">
			<?= $UC->submit("Opdater", array("class" => "primary", "wrapper" => "li.save")) ?>
		</ul>
	<?= $UC->formEnd() ?>

</div>