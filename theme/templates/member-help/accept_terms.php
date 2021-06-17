<?
global $UC;

?>
<div class="scene i:member_help_accept_terms accept_terms">

	<h1>Behandling af persondata</h1>

	<p>OBS! På næste side findes fortrolige oplysninger om KBHFFs medlemmer. Ved at klikke videre bekræfter du, at du vil behandle disse oplysninger fortroligt og ansvarsfuldt.</p>
	<p>Warning! You're about to enter an area with sensitive information and by proceeding you agree to handling this information responsibly.</p>


	<!-- create form with a single checkbox and acccept/cancel buttons -->
	<?= $UC->formStart("accept", array("class" => "accept")) ?>

		<fieldset>
			<?= $UC->input("terms", ["label"=>"Jeg har læst og forstået ovenstående. / I have read and agree to the above terms.", "hint_message"=>"", "error_message"=>"Du skal acceptere retningslinjerne for at fortsætte."]) ?>
		</fieldset>

		<ul class="actions">
			<?= $UC->submit("Fortsæt", array("class" => "primary", "wrapper" => "li.accept")) ?>
			<li class="reject"><a href="/profil" class="button">Nej, gå tilbage til Min Side</a></li>
		</ul>

	<?= $UC->formEnd() ?>

</div>
