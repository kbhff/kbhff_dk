<?
global $UC;

?>
<div class="scene accept_terms i:accept_terms">

	<h1>Samtykke om brug af persondata</h1>

	<p>I forbindelse med overgangen til det nye system vil vi en enkelt gang bede dig om
	 dit samtykke til at vi opbevarer og behandler den persondata, du har givet os.
		Dette er fra lovgivningens side en forudsætning for, at vi kan have dig som medlem.</p>

	<p>Vi opbevarer følgende informationer: Navn, emailadresse, telefonnummer og dine indkøb.</P>

	<p>Afviser du dette samtykke, vil det medføre ophør af dit medlemskab, og
		persondata vi har om dig slettes fra vores server i overensstemmelse med vore retningslinjer.</p>

	<p>Læs mere i vore <a href="/persondata">retningslinjer for behandling og opbevaring af persondata</a></p>


	<!-- create form with a single checkbox and acccept/cancel buttons -->
	<?= $UC->formStart("accept", array("class" => "accept")) ?>

		<fieldset>
			<?= $UC->input("terms", ["label"=>"Hello", "hint_message"=>"blah", "error_message"=>"Blah"]) ?>
		</fieldset>

		<ul class="actions">
			<?= $UC->submit("Fortsæt", array("class" => "primary", "wrapper" => "li.accept")) ?>
			<li class="reject"><a href="/profil/opsig" class="button">Nej, meld mig ud</a></li>
		</ul>

	<?= $UC->formEnd() ?>

</div>
