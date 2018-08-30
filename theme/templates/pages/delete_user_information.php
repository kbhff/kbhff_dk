<?php
$UC = new User();
$user = $UC->getKbhffUser();

$this->pageTitle("Udmeldelse");
?>

<div class="scene delete_user_information i:delete_user_information">
	<h1>Vil du udmeldes?</h1>
	<h2>Bemærk, at denne handling er uomgørlig.</h2>

	<?= $UC->formStart("deleteUserInformation", array("class" => "confirm_cancellation")) ?>	
			<fieldset>
				<?= $UC->input("password", [
					"required" => true, 
					"type" => "password", 
					"label" => "Password", 
					"hint_message" => "Skriv dit password for at bekræfte din udmeldelse.", 
					"error_message" => "Ugyldigt password"
				]) ?>
			</fieldset>
			<ul class="actions">
				<?= $UC->submit("Farvel!", array("class" => "primary", "wrapper" => "li.save")) ?>

			</ul>
		<?= $UC->formEnd() ?>


</div>
