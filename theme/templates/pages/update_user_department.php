<?php
	
$model = new User();

$this->pageTitle("Verificering");
?>
<div class="scene login i:forgot">
	<h1>Nulstil password</h1>
	<h2>Verficer at du vil nulstille dit password</h2>
	<p class="validateParagraph"><span class='highlight'>TAK.</span> Vi har nu sendt dig en mail, i mailen er der en kode som du kan indtaste her og lave et nyt password.</p>


	<?= $model->formStart("validateCode", ["class" => "verify_code"]) ?>

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
			<?= $model->input("reset-token"); ?>
		</fieldset>

		<ul class="actions">
			<?= $model->submit("Lav nyt password", array("class" => "primary", "wrapper" => "li.reset")) ?>
		</ul>
	<?= $model->formEnd() ?>

</div>