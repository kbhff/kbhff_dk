<?php
$UC = new User();
$user = $UC->getKbhffUser();

$this->pageTitle("Angiv e-mailadresse");
?>

<div class="scene user_information i:user_information">
	<h1>Manglende e-mailadresse</h1>
	<h2>Vi har brug for at kunne kontakte dig.</h2>

	<p>Vi kan se, at du ikke har knyttet nogen e-mailadresse til din konto. I det nye KBHFF-system er en e-mailadresse imidlertid nødvendig, så vi vil bede dig udfylde nedenstående felt.</p>

	<p>Når du har angivet en e-mailadresse, sender vi en mail med et bekræftelseslink, som skal bruges til at aktivere e-mailadressen.</p>
	<?= $UC->formStart("/profil/updateEmail", ["class" => "form_user"]) ?>

		<?= $HTML->serverMessages(); ?>

		<fieldset>
			<?= 
				
				$UC->input("email", [
					"type" => "email",
					"label" => "Email",
					"required" => true,
					"hint_message" => "Den e-mailadresse, som vi kan kontakte dig på.",
					"error_message" => "Ugyldig e-mail",
				])

			?>
		</fieldset>

		<ul class="actions">
			<li class="cancel"><a href="/" class="button">Annullér</a></li>
			<?= $UC->submit("Gem", array("class" => "primary", "wrapper" => "li.save")) ?>
		</ul>

	<?= $UC->formEnd() ?>

</div>