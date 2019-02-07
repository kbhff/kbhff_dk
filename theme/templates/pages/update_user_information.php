<?php
$UC = new User();
$user = $UC->getKbhffUser();

$this->pageTitle("Brugeroplysninger");
?>

<div class="scene user_information i:user_information">
	<h1>Brugeroplysninger</h1>
	<h2>Her kan du rette i dine brugeroplysninger.</h2>

	<?= $UC->formStart("updateUserInformation", ["class" => "form_user"]) ?>

		
<?	// print messages
	if(message()->hasMessages()): ?>
		<p class="errormessage">
<?		$messages = message()->getMessages(array("type" => "error"));
		foreach($messages as $message): ?>
			<?= $message ?><br>
<?		endforeach;?>
		</p>
		<p class="message">
<?		$messages = message()->getMessages(array("type" => "message"));
		foreach($messages as $message): ?>
			<?= $message ?><br>
<?		endforeach;?>
		</p>
<?	message()->resetMessages(); ?>
<?	endif; ?>

		<fieldset>
			<?= 
				$UC->input("nickname", [
					"type" => "string",
					"label" => "Kaldenavn",
					"value" => $user["nickname"],
					"required" => false,
					"hint_message" => "Skriv dit kaldenavn (det kan også bare være dit navn)",
					"error_message" => "",
				]),
			
				$UC->input("firstname", [
					"type" => "string",
					"label" => "Fornavn",
					"value" => $user["firstname"],
					"required" => true,
					"hint_message" => "Skriv dit fornavn",
					"error_message" => "Fornavn er påkrævet",
				]),

				$UC->input("lastname", [
					"type" => "string",
					"label" => "Efternavn",
					"value" => $user["lastname"],
					"required" => true,
					"hint_message" => "Skriv dit efternavn",
					"error_message" => "Efternavn er påkrævet",
				]),

				$UC->input("email", [
					"type" => "email",
					"label" => "Email",
					"value" => $user["email"],
					"required" => true,
					"hint_message" => "Den e-mailadresse, som vi kan kontakte dig på.",
					"error_message" => "Ugyldig e-mail",
				]),

				$UC->input("mobile", [
					"type" => "string",
					"label" => "Mobil",
					"value" => $user["mobile"],
					"hint_message" => "Skriv dit mobiltelefonnummer, hvis vi må sende dig påmindelser pr. SMS",
					"error_message" => "Ugyldigt nummer",
				]);
			?>
		</fieldset>

		<ul class="actions">
			<li class="cancel"><a href="/" class="button">Anullér</a></li>
			<?= $UC->submit("Opdater", array("class" => "primary", "wrapper" => "li.save")) ?>
		</ul>

	<?= $UC->formEnd() ?>

</div>