<?php

global $action;

$UC = new SuperUser();
$user_id = $action[1];

$user = $UC->getKbhffUser(["user_id" => $user_id]);



$this->pageTitle("Udmeldelse");
?>

<div class="scene delete_user_information i:delete_user_information">
	<h2>Vil du udmelde <?=$user["firstname"] ? $user["firstname"]: $user["nickname"]?>?</h3>
	<h3>Bemærk, at denne handling er uomgørlig.</h2>

	<?= $UC->formStart("deleteUserInformation/$action[1]", array("class" => "confirm_cancellation")) ?>

		<?= $HTML->serverMessages() ?>

		<ul class="actions">
			<?= $UC->submit("Bekræft udmeldelse", array("class" => "primary", "wrapper" => "li.save")) ?>
			<li class="cancel"><a href="/medlemshjaelp/brugerprofil/<?=$action[1]?>" class="button">Annullér</a></li>
		</ul>

	<?= $UC->formEnd() ?>

</div>
