<?
global $UC;

$disable_ordering_reminder = $UC->getUserLogAgreement("disable_ordering_reminder");
$disable_pickup_reminder = $UC->getUserLogAgreement("disable_pickup_reminder");

?>
<div class="scene message_center i:message_center">

	<h1>Beskedcenter</h1>

	<p>Her kan du ændre dine præferencer for modtagelse af e-mails.</p>

	<h2>E-mail</h2>

	<?= $UC->formStart("updateEmailAgreements", array("class" => "accept")) ?>

		<fieldset>
			<?= $UC->input("ordering_reminder", ["value" => $disable_ordering_reminder ? 0 : 1]) ?>
			<?= $UC->input("pickup_reminder", ["value" => $disable_pickup_reminder ? 0 : 1]) ?>
		</fieldset>

		<ul class="actions">
			<?= $UC->submit("Gem e-mailpræferencer", array("class" => "primary", "wrapper" => "li.save")) ?>
		</ul>

	<?= $UC->formEnd() ?>

	<!-- <h2>SMS</h2> -->

</div>
