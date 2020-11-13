<?php
include_once("classes/system/department.class.php");
$DC = new Department();
$UC = new SuperUser();
global $action;

$user_id = $action[1];
$user = $UC->getKbhffUser(["user_id" => $user_id]);

$membership_renewal_value = $UC->getUserRenewalOptOut($user_id) ? 0 : 1;

$this->pageTitle("Fornyelse");
?>

<div class="scene update_department i:update_department">
	<h1>Fornyelse</h1>
	<h2>Automatisk eller ej?</h2>
	<div class="info">
		<p>Som udgangspunkt bliver et medlemskab af KBHFF fornyet og betalt automatisk én gang om året. Hvis medlemmet fravælger automatisk fornyelse, vil han/hun kunne bestille grøntsager m.m. frem til fornyelsesdatoen, hvorefter medlemskabet gøres inaktivt. 
		</p><p> Som inaktivt medlem kan man stadig se sine fremtidige bestillinger og vagter, men man kan ikke lave nye bestillinger før man har genaktiveret og betalt sit medlemskab.</p>
		<? if(!$membership_renewal_value && $user['membership'] && !$user['membership']['subscription_id']): ?>
			<p class="obs"><strong>OBS!</strong> Dette medlem har tidligere fravalgt automatisk fornyelse af sit medlemskab. Hvis automatisk fornyelse tilvælges nu, hvor fornyelsesdatoen er overskredet, vil der blive oprettet en ordre på <strong>1 stk. kontingent</strong>, som skal betales før medlemmet kan bestille grøntsager.</p>
		<? endif; ?>
	</div>


	<?= $UC->formStart("updateUserMembershipRenewal/$user_id", ["class" => "form_renewal"]) ?> 

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
			<li class="cancel"><a href="/medlemshjaelp/brugerprofil/<?= $user_id ?>" class="button">Annullér</a></li>
			<?= $UC->submit("Opdater", array("class" => "primary", "wrapper" => "li.save")) ?>
		</ul>

	<?= $UC->formEnd() ?>

</div>