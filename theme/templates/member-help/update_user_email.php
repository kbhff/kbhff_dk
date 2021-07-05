<?php

global $action;

$UC = new SuperUser();
$user_id = $action[1];

$user = $UC->getKbhffUser(["user_id" => $user_id]);

$this->pageTitle("Angiv e-mailadresse");
?>

<div class="scene user_information i:user_information">
	<h1>Manglende e-mailadresse</h1>
	<h2>Vi har brug for at kunne kontakte <?= $user["nickname"] ?>.</h2>

	<p>Medlemmet har ikke knyttet nogen e-mailadresse til sin konto. I det nye KBHFF-system er en e-mailadresse imidlertid nødvendig, så bed medlemmet udfylde nedenstående felt.</p>

	<p>Når medlemmet har angivet en e-mailadresse, sender vi en mail med et bekræftelseslink, som skal bruges til at aktivere e-mailadressen.</p>
	<?= $UC->formStart("/medlemshjaelp/updateEmail/$user_id", ["class" => "form_user"]) ?>

		<?= $HTML->serverMessages(); ?>

		<fieldset>
			<?= 
				
				$UC->input("email", [
					"type" => "email",
					"label" => "Email",
					"required" => true,
					"hint_message" => "Den e-mailadresse, som vi kan kontakte medlemmet på.",
					"error_message" => "Ugyldig e-mail",
				])

			?>
		</fieldset>

		<ul class="actions">
			<li class="cancel"><a href="/medlemshjaelp" class="button">Annullér</a></li>
			<?= $UC->submit("Gem", array("class" => "primary", "wrapper" => "li.save")) ?>
		</ul>

	<?= $UC->formEnd() ?>

</div>