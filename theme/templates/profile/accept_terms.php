<?
global $UC;
	
?>
<div class="scene accept_terms i:accept_terms">

	<h1>Accepter vilkår for KBHFF</h1>

	<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
		

	<?= $UC->formStart("accept", array("class" => "accept")) ?>

		<fieldset>
			<?= $UC->input("terms") ?>
		</fieldset>

		<ul class="actions">
			<?= $UC->submit("Fortsæt", array("class" => "primary", "wrapper" => "li.accept")) ?>
		</ul>

	<?= $UC->formEnd() ?>

</div>