<?
global $UC;

?>
<div class="scene accept_terms i:accept_terms">

	<h1>Accepter vilkår for KBHFF's opbevaring og håndtering af persondata</h1>

	<p>For at leve op til persondatalovgivning, skal vi en enkelt gang bede dig om
		at give din samtykkeerklæring til hvordan vi opbevarer dine personlige data.
		Dette er en forudsætning for at du kan fortsætte som medlem.</p>

	<p>Vælger du at afvise samtykke vil det medføre ophør af dit medlemskab og
		dine personlige data slettes i overensstemmelse med vore retningslinjer.</p>

	<p>Læs vores <a href="/persondata">retningslinjer for behandling og opbevaring af persondata</a></p>



	<?= $UC->formStart("accept", array("class" => "accept")) ?>

		<fieldset>
			<?= $UC->input("terms") ?>
		</fieldset>

		<ul class="actions">
			<?= $UC->submit("Fortsæt", array("class" => "primary", "wrapper" => "li.accept")) ?>
			<?= $UC->button("Afvis", array("wrapper" => "li.reject")) ?>
		</ul>

	<?= $UC->formEnd() ?>

</div>
