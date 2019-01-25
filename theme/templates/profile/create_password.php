<?php
global $action;
global $model;


print "user ".session()->value("user_id");; exit;

?>
<div class="scene login i:create_password">

	<h1>Opret adgangskode</h1>

<? // if($model->checkResetToken($reset_token)): ?>

	<div class="password">
		<h2>Adgangskode</h2>
		<p>Som ny bruger skal du oprette en adgangskode. Dette vil også verificere din konto.</p>
		

		<?= $model->formStart("setPasswordAndConfirmAccount", ["class" => "create_password"]) ?>

			<fieldset>
				<?= $model->input("new_password", array("required" => true, "label" => "Ny adgangskode")) ?>
				<?= $model->input("confirm_password", array("required" => true, "label" => "Bekræft adgangskode")) ?>
			</fieldset>
			<ul class="actions">
				<?= $model->submit("Gem", array("class" => "primary", "wrapper" => "li.save")) ?>
			</ul>
		<?= $model->formEnd() ?>
	</div>

<? /* else: ?>

	<p>Your request is invalid. Resetting your password must be completed within 15 minutes.</p>

<? endif; */ ?>

</div>