<?php
global $action;
global $model;
$UC = new User();

// get posted variables
$nickname = stringOr(getPost("nickname"));
$email = stringOr(getPost("email"));
$mobile = stringOr(getPost("mobile"));

// get current user id
$user_id = session()->value("user_id");

$user = $UC->getUser();
//print_r($user);
?>
<div class="scene shopProfile i:shopProfile">
	<h1>Profil</h1>


	<?= $HTML->serverMessages() ?>


	<div class="item">
		<h2>Dine brugeroplysninger</h2>
		<?= $UC->formStart("updateProfile", array("class" => "details labelstyle:inject")) ?>
			<fieldset>
				<?= $UC->input("firstname", array("required" => true, "value" => $user["firstname"], "label" => "Fornavn", "hint_message" => "Skriv dit fornavn her", "error_message" => "Dit fornavn må kun indeholde bogstaver.")) ?>
				<?= $UC->input("lastname", array("required" => true, "value" => $user["lastname"], "label" => "Efternavn", "hint_message" => "Skriv dit efternavn her", "error_message" => "Dit efternavn må kun indeholde bogstaver.")) ?>
				<?= $UC->input("email", array("required" => true, "value" => $user["email"], "label" => "Din email", "hint_message" => "Indtast din email.", "error_message" => "Du har indtastet en ugyldig e-mailadresse.")); ?>
				<?= $UC->input("mobile", array("value" => $user["mobile"], "label" => "Mobilnummer", "hint_message" => "Indtast dit mobilnummer.", "error_message" => "Det skal være et dansk mobilnummer.")); ?>

				<!-- <?= $UC->input("mobile", array("required" => true, )) ?> -->
			</fieldset>

			<ul class="actions">
				<?= $UC->link("Cancel", "/shop/checkout/", array("class" => "button", "wrapper" => "li.cancel")) ?>
				<?= $UC->submit("Update", array("class" => "primary key:s", "wrapper" => "li.save")) ?>
			</ul>
		<?= $UC->formEnd() ?>
	</div>

</div>