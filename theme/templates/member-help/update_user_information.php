<?php

global $action;

$UC = new SuperUser();
$user_id = $action[1];

$user = $UC->getKbhffUser(["user_id" => $user_id]);

$this->pageTitle("Brugeroplysninger");
?>

<div class="scene user_information i:user_information">
	<h1>Brugeroplysninger</h1>
	<h2>Her kan du rette i medlemmets brugeroplysninger.</h2>

	<?= $UC->formStart("updateUserInformation/$action[1]", ["class" => "form_user"]) ?>

		<?= $HTML->serverMessages() ?>

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
				<li class="cancel"><a href="/medlemshjaelp/brugerprofil/<?=$action[1]?>" class="button">Anullér</a></li>
			<?= $UC->submit("Opdater", array("class" => "primary", "wrapper" => "li.save")) ?>
		</ul>

	<?= $UC->formEnd() ?>

</div>