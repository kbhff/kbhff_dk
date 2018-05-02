<?php
global $action;
global $model;


$forward_url = getVar("forward_url");
if($forward_url) {
	session()->value("login_forward", $forward_url);
}


$username = stringOr(getPost("username"));
?>
<div class="scene login i:login">
	<h1>KBHFF Login</h1>

<?	if(defined("SITE_SIGNUP") && SITE_SIGNUP): ?>
	<p>Not registered yet? <a href="<?= SITE_SIGNUP ?>">Create your account now</a>.</p>
<?	endif; ?>

	<?= $model->formStart("dual", array("class" => "login")) ?>

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
			<?= $model->input("username", array(
				"required" => true, 
				"value" => $username,
				"pattern" => "^(1|[0-9]{4,5}|[\+0-9\-\.\s\(\)]{5,18}|[\w\.\-_]+@[\w\-\.]+\.\w{2,10})$",
				"label" => "Brugernavn",
				"hint_message" => "Brug dit medlemsnummer, email eller telefonnummer som brugernavn",
				"error_message" => "Det ligner ikke et gyldigt brugernavn",
			)); ?>
			<?= $model->input("password", array(
				"required" => true,
				"min" => 1
			)); ?>
		</fieldset>

		<ul class="actions">
			<?= $model->submit("Log in", array("class" => "primary", "wrapper" => "li.login")) ?>
			<li class="forgot">Did you <a href="/login/forgot">forget your password</a>?</li>
		</ul>
	<?= $model->formEnd() ?>

</div>