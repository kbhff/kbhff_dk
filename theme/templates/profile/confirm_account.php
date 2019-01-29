<?php
// Get variables from the controller
global $model;

// Get username we want to verify from session
$username = session()->value("temp-username");


$this->pageTitle("Bekræft din konto");
?>
<div class="scene login i:confirm_account">

	<h1>Bekræft din konto</h1>
	<p>Du har ikke verificeret din konto og kan derfor ikke logge ind endnu. Vi har sendt dig en mail med en verificeringskode, som du skal indtaste her for at bekræfte din konto.</p>


	<?= $model->formStart("confirmAccount", ["class" => "confirm_account"]) ?>
		<?= $model->input("username", ["type" => "hidden", "value" => $username])?>

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
			<?= $model->input("verification_code", array(
					"label" => "Verificeringskode", 
					"required" => true, 
					"hint_message" => "Din verificeringskode", 
					"error_message" => "Check at du har indtastet koden fra e-mailen korrekt"
				));
			?>
		</fieldset>

		<ul class="actions">
			<?= $model->submit("Bekræft konto", array("class" => "primary", "wrapper" => "li.verify")) ?>
		</ul>
	<?= $model->formEnd() ?>

</div>