<div class="scene i:scene" itemscope itemtype="http://schema.org/NewsArticle">
	<h1>Signup error</h1>

<?	if(message()->hasMessages()): ?>
	<p class="errormessage">
<?		$messages = message()->getMessages(array("type" => "error"));
	foreach($messages as $message): ?>
		<?= $message ?><br>
<?		endforeach;?>
	</p>
	<p class="message">
<?		$messages = message()->getMessages(array("type" => "message"));
	foreach($messages as $message): ?>
		<?= $message ?><br>
<?		endforeach;?>
	</p>
<?	message()->resetMessages(); ?>

<?	endif; ?>

</div>