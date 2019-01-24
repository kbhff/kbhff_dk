<?php
// Get variables from the controller
global $action;
global $model;

?>

<div class="scene login i:create_password">
	<h1>Opret password</h1>

<? // if($model->checkResetToken($reset_token)): ?>

	<div class="password">
		<h2>Password</h2>

		<?= $model->formStart("setPassword", ["class" => "create_password"]) ?>

			<fieldset>
				<?= $model->input("new_password", array("required" => true)) ?>
				<?= $model->input("confirm_password", array("required" => true)) ?>
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