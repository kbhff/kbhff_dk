<?php
$UC = new User();
$user = $UC->getKbhffUser();

$this->pageTitle("Kodeord");
?>

<div class="scene user_password i:user_password">
	<h1>Kodeord</h1>
	<h2>Her kan du ændre dit kodeord.</h2>

	<?= $UC->formStart("updateUserPassword", ["class" => "form_user"]) ?>

<?	if(message()->hasMessages()): ?>
		<p class="errormessage">
<?		$messages = message()->getMessages(array("type" => "error"));
		foreach($messages as $message): ?>
			<?= $message ?><br>
<?		endforeach;?>
		</p>
		<p class="errormessage">
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
					"label" => "Dit nye kodeord",
					"required" => true,
					"hint_message" => "Dit nye kodeord",
					"error_message" => "",
				]),

				$UC->input("confirm_password", [
					"label" => "Gentag dit nye kodeord",
					"required" => true,
					"hint_message" => "Dit nye kodeord",
					"error_message" => "",
				]);
			?>
		</fieldset>

		<ul class="actions">
			<?= $UC->submit("Gem", array("class" => "primary", "wrapper" => "li.save")) ?>
			<li class="cancel"><a href=".." class="button">Anullér</a></li>
		</ul>
	<?= $UC->formEnd() ?>

</div>