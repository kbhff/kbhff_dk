<?
global $UC;

?>
<div class="scene i:member_help_accept_terms accept_terms">

	<h1>Samtykke om brug af persondata</h1>

	<p>Achtung! You're are entering an area with sensitive information and by proceeding you agree to handling this information responsibly.</p>


	<!-- create form with a single checkbox and acccept/cancel buttons -->
	<?= $UC->formStart("accept", array("class" => "accept")) ?>

		<fieldset>
			<?= $UC->input("terms", ["label"=>"Det er jeg indforstået med.", "hint_message"=>"", "error_message"=>"Du skal acceptere retningslinjerne for at fortsætte."]) ?>
		</fieldset>

		<ul class="actions">
			<?= $UC->submit("Fortsæt", array("class" => "primary", "wrapper" => "li.accept")) ?>
			<li class="reject"><a href="/profil" class="button">Nej, gå tilbage til Min Side</a></li>
		</ul>

	<?= $UC->formEnd() ?>

</div>
