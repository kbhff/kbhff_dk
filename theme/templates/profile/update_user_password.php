<?php
$UC = new User();
$user = $UC->getKbhffUser();

$this->pageTitle("Adgangskode");
?>

<div class="scene user_password i:user_password">
	<h1>Adgangskode</h1>
	<h2>Her kan du ændre din adgangskode.</h2>

	<? if(message()->hasMessages()): ?>
	<div class="messages">
	<?
	$all_messages = message()->getMessages();
	message()->resetMessages();
	foreach($all_messages as $type => $messages):
		foreach($messages as $message): ?>
		<p class="<?= $type ?>"><?= $message ?></p>
		<? endforeach;?>
	<? endforeach;?>
	</div>
	<? endif; ?>
		
	<?= $UC->formStart("updateUserPassword", ["class" => "form_password password i:resetPassword"]) ?>

		<fieldset>
			<?=
			
				$UC->input("old_password", [
					"label" => "Din nuværende adgangskode",
					"required" => true,
					"hint_message" => "Indtast din nuværende adgangskode",
					"error_message" => "",
				]),
				
				$UC->input("new_password", [
					"label" => "Din nye adgangskode",
					"required" => true,
					"hint_message" => "Indtast din nye adgangskode på mindst 8 anslag",
					"error_message" => "",
				]),

				$UC->input("confirm_password", [
					"label" => "Gentag din nye adgangskode",
					"required" => true,
					"hint_message" => "Indtast din nye adgangskode igen for at bekræfte",
					"error_message" => "Adgangskoderne er ikke ens",
				]);
			?>
		</fieldset>

		<ul class="actions">
			<li class="cancel"><a href="/" class="button">Annullér</a></li>
			<?= $UC->submit("Gem", array("class" => "primary", "wrapper" => "li.save")) ?>
		</ul>

	<?= $UC->formEnd() ?>

</div>
