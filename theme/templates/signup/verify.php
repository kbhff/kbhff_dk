<?php
global $action;
global $model;

$IC = new Items();
$page_item = $IC->getItem(array("tags" => "page:bliv-medlem-verificer", "extend" => array("user" => true, "tags" => true, "mediae" => true)));
if($page_item) {
	$this->sharingMetaData($page_item);
}
$order_no = $action[1];
?>
<div class="scene signup i:signup">

	<h1>Du har nu oprettet en konto hos KBHFF!</h1>
	<h2>Vi har sendt dig en aktiveringsmail</h2>
	<p>E-mailen indeholder en verificeringskode, som du kan indsætte i feltet nedenfor.</p>
	<p>Alternativt kan du springe verificeringen over nu og verificere senere gennem et link fra aktiveringsmailen.</p>

	<?= $model->formStart("bekraeft/".$order_no, ["class" => "verify_code"]) ?>

<?	// show error messages 
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
		<?= $model->input("verification_code", array("label" => "Verificeringskode", "hint_message" => "Indtast verificeringskoden som blev sendt til din email.", "error_message" => "Forkert verificeringskode")); ?>
	</fieldset>

	<ul class="actions">
		<li class="skip"><a href="/bliv-medlem/spring-over/<?=$order_no?>" class="button">Spring over</a></li>
		<?= $model->submit("Verificér email", array("class" => "primary", "wrapper" => "li.reset")) ?>
	</ul>
	<?= $model->formEnd() ?>

</div>