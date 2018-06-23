<?php
	
$model = new Model();

$this->pageTitle("Verificering");
?>
<div class="scene login i:forgot">
	<h1>Nulstil password</h1>
	<h2>Verficer at du vil nulstille dit password</h2>
	<p><span class='highlight'>TAK.</span> Vi har nu sendt dig en mail, i mailen er der en kode som du kan indtaste her og lave et nyt password.</p>


	<?= $model->formStart("validateCode", ["class" => "verify"]) ?>

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
			<?= $model->input("code", array(
					"type" => "string", 
					"label" => "Kode", 
					"required" => true, 
					"pattern" => "^(1|[0-9]{4,5}|[\+0-9\-\.\s\(\)]{5,18}|[\w\.\-_]+@[\w\-\.]+\.\w{2,10})$", 
					"hint_message" => "Din verificerings kode", 
					"error_message" => "Check at du har indtastet den samme kode fra e-mailen"
				));
			?>
		</fieldset>

		<ul class="actions">
			<?= $model->submit("Lav nyt password", array("class" => "primary", "wrapper" => "li.reset")) ?>
		</ul>
	<?= $model->formEnd() ?>

</div>