<?php
include_once("classes/system/department.class.php");
$DC = new Department();
$UC = new User();
$user = $UC->getKbhffUser();
$departments = $DC->getDepartmentsAcceptSignups();
$user_department = $UC->getUserDepartment();

$membership_renewal_value = $UC->getRenewalOptOut() ? 0 : 1;

$this->pageTitle("Fornyelse");
?>

<div class="scene update_department i:update_department">
	<h1>Fornyelse</h1>
	<h2>Automatisk eller ej?</h2>
	<div class="info">
		<p>Som udgangspunkt bliver dit medlemskab af KBHFF fornyet og betalt automatisk én gang om året. Hvis du fravælger automatisk fornyelse, vil du kunne bestille grøntsager m.m. frem til fornyelsesdatoen, hvorefter dit medlemskab gøres inaktivt. 
		</p><p> Som inaktivt medlem kan du stadig se dine fremtidige bestillinger og vagter, men du kan ikke lave nye bestillinger før du har genaktiveret og betalt dit medlemskab.</p>
		<? if(!$membership_renewal_value && $user['membership'] && !$user['membership']['subscription_id']): ?>
			<p class="obs"><strong>OBS!</strong> Du har tidligere fravalgt automatisk fornyelse af dit medlemskab. Hvis du tilvælger automatisk fornyelse nu, hvor fornyelsesdatoen er overskredet, vil der blive oprettet en ordre på <strong>1 stk. kontingent</strong>, som skal betales før du kan bestille grøntsager.</p>
		<? endif; ?>
	</div>


	<?= $UC->formStart("updateMembershipRenewal", ["class" => "form_renewal"]) ?> 

<?	// print error messages
	if(message()->hasMessages(array("type" => "error"))): ?>
		<p class="errormessage">
<?		$messages = message()->getMessages(array("type" => "error"));
		message()->resetMessages();
		foreach($messages as $message): ?>
			<?= $message ?><br>
<?		endforeach;?>
		</p>
<?	endif; ?>

		<fieldset>
			
			<?= $UC->input("membership_renewal", [
				"value" => $membership_renewal_value
				]); 
			?>
			
		</fieldset>

		<ul class="actions">
			<li class="cancel"><a href="/profil" class="button">Annullér</a></li>
			<?= $UC->submit("Opdater", array("class" => "primary", "wrapper" => "li.save")) ?>
		</ul>

	<?= $UC->formEnd() ?>

</div>