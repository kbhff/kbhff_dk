<?php
global $action;
global $model;

$IC = new Items();
$page_item = $IC->getItem(array("tags" => "page:bliv-medlem-verificer", "extend" => array("user" => true, "tags" => true, "mediae" => true)));
if($page_item) {
	$this->sharingMetaData($page_item);
}

?>
<div class="scene signup i:signup">

	<h1>Du har nu oprettet en konto hos KBHFF!</h1>
	<h2>Vi har sendt dig en aktiveringsemail.</h2>
	<p>Emailen indeholder en verifikationskode, som du kan bruge i inputfeltet nedenfor.</p>
	<p>Alternativt kan du springe over verificeringen nu og verificere senere gennem et link fra aktiveringsemailen.</p>

	<?= $model->formStart("bekraeft/til-betaling", ["class" => "verify_code"]) ?>

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
			<?= $model->input("verification_code"); ?>
		</fieldset>

		<ul class="actions">
			<?= $model->submit("Verificer email", array("class" => "primary", "wrapper" => "li.reset")) ?>
			<li class="skip"><a href="spring-over" class="button">Spring over</a></li>
		</ul>
	<?= $model->formEnd() ?>

</div>
