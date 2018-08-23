<?
global $UC;

?>
<div class="scene accept_terms i:accept_terms">

	<h1>Accepter vilkår for KBHFF's opbevaring og håndtering af persondata</h1>

	<p>For at leve op til persondatalovgivningen, skal vi en enkelt gang bede dig om
		at give dit samtykke til vi opbevarer persondata fra dig.
		Dette er en forudsætning fra lovgivningens side for at vi kan have dig som medlem.</p>

	<p>Vælger du at afvise samtykke vil det medføre ophør af dit medlemskab og
		den persondata vi har om dig på vores server slettes i overensstemmelse med vore retningslinjer.</p>

	<p>Læs vores <a href="/persondata">retningslinjer for behandling og opbevaring af persondata</a></p>



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
