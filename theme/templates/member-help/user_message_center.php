<?
global $model;
global $action;

$member_user_id = $action[1];

$UC = new SuperUser();
$user = $UC->getKbhffUser(["user_id" => $member_user_id]);

$disable_ordering_reminder = $model->getUserLogAgreement("disable_ordering_reminder", ["user_id" => $member_user_id]);
$disable_pickup_reminder = $model->getUserLogAgreement("disable_pickup_reminder", ["user_id" => $member_user_id]);

?>
<div class="scene message_center i:message_center">

	<h1>Beskedcenter</h1>

	<p>Her kan du ændre præferencer for <?=$user["firstname"] ? $user["firstname"] : $user["nickname"]?>'s modtagelse af e-mails.</p>

	<h2>E-mail</h2>

	<?= $HTML->serverMessages() ?>

	<?= $model->formStart("updateEmailAgreements/".$member_user_id, array("class" => "accept")) ?>

		<fieldset>
			<?= $model->input("ordering_reminder", ["value" => $disable_ordering_reminder ? 0 : 1]) ?>
			<?= $model->input("pickup_reminder", ["value" => $disable_pickup_reminder ? 0 : 1]) ?>
		</fieldset>

		<ul class="actions">
			<?= $model->submit("Gem e-mailpræferencer", array("class" => "primary", "wrapper" => "li.save")) ?>
		</ul>

	<?= $model->formEnd() ?>

	<!-- <h2>SMS</h2> -->

</div>
