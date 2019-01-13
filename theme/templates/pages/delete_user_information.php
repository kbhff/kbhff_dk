<?php
$UC = new User();
$user = $UC->getKbhffUser();

$this->pageTitle("Udmeldelse");
?>

<div class="scene delete_user_information i:delete_user_information">
	<h1>Vil du udmeldes?</h1>
	<h2>Bemærk, at denne handling er uomgørlig.</h2>

	<!-- start form field -->
	<?= $UC->formStart("deleteUserInformation", array("class" => "confirm_cancellation")) ?>

		<!-- print error messages -->
	<?	if(message()->hasMessages(array("type" => "error"))): ?>
		<p class="errormessage">
	<?	$messages = message()->getMessages(array("type" => "error"));
		message()->resetMessages();
		foreach($messages as $message): ?>
			<?= $message ?><br>
	<?		endforeach;?>
		</p>
	<?	endif; ?>
	
		<!-- user must enter password to confirm deletion -->
		<fieldset>
			<?= $UC->input("password", [
				"min" => 1,
				"required" => true, 
				"hint_message" => "Skriv dit password for at bekræfte din udmeldelse.", 
				"error_message" => "Ugyldigt password"
			]) ?>
		</fieldset>
		
		<!-- 'confirm' button -->
		<ul class="actions">
			<?= $UC->submit("Farvel", array("class" => "primary", "wrapper" => "li.save")) ?>

		</ul>

	<!-- end form field -->
	<?= $UC->formEnd() ?>


</div>
