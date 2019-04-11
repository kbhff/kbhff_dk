<?php
global $action;

$UC = new SuperUser();
$user_id = $action[1];

$user = $UC->getKbhffUser(["user_id" => $user_id]);

$this->pageTitle("Adgangskode");
?>

<div class="scene user_password i:user_password">
	<h1>Adgangskode</h1>
	<h2>Her kan medlemmet ændre sin adgangskode.</h2>


	<?= $UC->formStart("updateUserPassword/$action[1]", ["class" => "form_password"]) ?>

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

		<fieldset>
			<?=
				$UC->input("new_password", [
					"label" => "Din nye adgangskode",
					"required" => true,
					"hint_message" => "Indtast din nye adgangskode på mere end 8 anslag",
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
			<li class="cancel"><a href="/medlemshjaelp/brugerprofil/<?=$action[1]?>" class="button">Annullér</a></li>
			<?= $UC->submit("Gem", array("class" => "primary", "wrapper" => "li.save")) ?>
		</ul>

	<?= $UC->formEnd() ?>

</div>
