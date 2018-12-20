<?php
$UC = new User();
$user = $UC->getKbhffUser();
$department = $UC->getUserDepartment();
?>



<div class="scene profile i:profile">
	<!-- <img class="fit-width" src="/img/deprecated/banner.jpg"	/> -->
	
	
	<div class="article">
		<h1>Brugerprofil</h1>
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
		<div class="articlebody">
			<h2>Coming soon: brugerprofil</h2>
			<p>På denne side vil man kunne bestille for brugeren, rette oplysninger m.m.</p>
		</div>
	</div>
</div>
