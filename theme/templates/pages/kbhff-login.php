<?php
global $action;
global $model;


$forward_url = getVar("forward_url");
if($forward_url) {
	session()->value("login_forward", $forward_url);
}


$username = stringOr(getPost("username"), session()->value("temp-username"));
session()->reset("temp-username");
?>
<div class="scene login i:login">
	<h1>Log ind</h1>
	<p>I medlemssystemet kan du bestille varer, booke vagter og administrere dit medlemsskab. <strong>Velkommen indenfor!</strong></p>

	<p>Du kan bruge e-mailadresse, telefonnummer eller medlemsnummer som brugernavn til at logge ind.</p>

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
				"hint_message" => "Brug dit medlemsnr, email eller telefonnummer som brugernavn",
				"error_message" => "Det ligner ikke et gyldigt brugernavn",
			)); ?>
			<?= $model->input("password", array(
				"required" => true,
				"min" => 1,
				"hint_message" => "Skriv dit password (8-20 tegn)",
				"error_message" => "Ugyldigt password",
			)); ?>
		</fieldset>

		<ul class="actions">
			<?= $model->submit("Log ind", array("class" => "primary", "wrapper" => "li.login")) ?>
			<li class="forgot">Har du brug for <a href="/login/glemt">hjælp til at logge ind</a>?</li>
		</ul>
	<?= $model->formEnd() ?>

<?	if(defined("SITE_SIGNUP") && SITE_SIGNUP): ?>
	<div class="signup">
		<p>Endnu ikke medlem? <a href="<?= SITE_SIGNUP ?>">Indmeld dig her</a>.</p>
	</div>
<?	endif; ?>

</div>
