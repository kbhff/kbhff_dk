<?
global $UC;

?>
<div class="scene accept_terms i:accept_terms">

	<h1>Accepter vilkår for KBHFF's opbevaring og håndtering af persondata</h1>

	<p>I forbindelse med overgangen til det ny system, vil vi en enkelt gang bede dig om
	 dit samtykke til at vi opbevarer og behandler den persondata, du har givet os.
		Dette er en forudsætning fra lovgivningens side for at vi kan have dig som medlem.</p>

	<p>Vi opbevarer følgende informationer: Navn, emailadresse, telefonnummer og dine indkøb.</P>

	<p>Afviser du dette samtykke, vil det medføre ophør af dit medlemskab og
		den persondata, vi har om dig, slettes fra vores server i overensstemmelse med vore retningslinjer.</p>

	<p>Læs mere i vore <a href="/persondata">retningslinjer for behandling og opbevaring af persondata</a></p>



	<?= $UC->formStart("accept", array("class" => "accept")) ?>

		<fieldset>
			<?= $UC->input("terms") ?>
		</fieldset>

		<ul class="actions">
			<?= $UC->submit("Fortsæt", array("class" => "primary", "wrapper" => "li.accept")) ?>
			<?= $UC->button("Nej, meld mig ud", array("wrapper" => "li.reject")) ?>
		</ul>

	<?= $UC->formEnd() ?>

</div>
