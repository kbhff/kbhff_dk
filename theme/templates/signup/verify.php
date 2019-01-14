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
	<h2>Vi har sendt dig en aktiveringsmail</h2>
	<p>E-mailen indeholder en verifikationskode, som du kan indsætte i feltet nedenfor.</p>
	<p>Alternativt kan du springe verificeringen over nu og verificere senere gennem et link fra aktiveringsmailen.</p>

	<?= $model->formStart("bekraeft", ["class" => "verify_code"]) ?>

<?	// show any error messages 
if(message()->hasMessages(array("type" => "error"))): ?>
	<p class="errormessage">
<?		$messages = message()->getMessages(array("type" => "error"));
		message()->resetMessages();
		foreach($messages as $message): ?>
			<?= $message ?><br>
<?		endforeach;?>
	</p>
<?	endif; ?>

	<fieldset>
		<?= $model->input("verification_code", array("label" => "Verifikationskode", "hint_message" => "Indtast verifikationskoden som blev sendt til din email.", "error_message" => "Forkert verifikationskode")); ?>
	</fieldset>

	<ul class="actions">
		<li class="skip"><a href="spring-over" class="button">Spring over</a></li>
		<?= $model->submit("Verificer email", array("class" => "primary", "wrapper" => "li.reset")) ?>
	</ul>
	<?= $model->formEnd() ?>

</div>