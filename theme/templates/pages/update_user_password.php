<?php
$UC = new User();
$user = $UC->getKbhffUser();

$this->pageTitle("Adgangskode");
?>

<div class="scene user_password i:user_password">
	<h1>Adgangskode</h1>
	<h2>Her kan du ændre din adgangskode.</h2>


	<?= $UC->formStart("updateUserPassword", ["class" => "form_password"]) ?>

<?	//print messages
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
				$UC->input("new_password", [
					"label" => "Din nye adgangskode",
					"required" => true,
					"hint_message" => "Din nye adgangskode",
					"error_message" => "",
				]),

				$UC->input("confirm_password", [
					"label" => "Gentag din nye adgangskode",
					"required" => true,
					"hint_message" => "Din nye adgangskode",
					"error_message" => "",
				]);
			?>
		</fieldset>

		<ul class="actions">
			<li class="cancel"><a href="/" class="button">Annullér</a></li>
			<?= $UC->submit("Gem", array("class" => "primary", "wrapper" => "li.save")) ?>
		</ul>

	<?= $UC->formEnd() ?>

</div>
