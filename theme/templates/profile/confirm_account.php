<?php
// Get variables from the controller
global $model;

// Get username we want to verify from session
$username = session()->value("temp-username");


$this->pageTitle("Bekræft din konto");
?>
<div class="scene login i:confirm_account">

	<h1>Bekræft din konto</h1>
	<p><span class='highlight'>TAK.</span> Vi har nu sendt dig en mail. I mailen er der en kode, som du kan indtaste her for at bekræfte din konto.</p>


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
					"label" => "Kode", 
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