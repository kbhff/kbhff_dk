<?php
include_once("classes/system/department.class.php");
$DC = new Department();
$UC = new SuperUser();
include_once("classes/users/supermember.class.php");
$MC = new SuperMember();
$IC = new Items();
$SC = new SuperShop();
global $action;

$user_id = $action[1];
$user = $UC->getKbhffUser(["user_id" => $user_id]);

$memberships = $IC->getItems(array("itemtype" => "membership", "status" => 1, "extend" => array("subscription_method" => true, "prices" => true)));

$membership_options = array();
foreach($memberships as $membership) {
	// do not include current membership
	if($membership["item_id"] != $user["membership"]["item_id"])  {
		$price = $SC->getPrice($membership["item_id"], ["user_id" => $user_id]);
		$membership_options[$membership["item_id"]] = strip_tags($membership["name"])." (".formatPrice($price).")";
		
	}
}

$this->pageTitle("Genaktiver medlemskab");
?>

<div class="scene update_userinfo_form i:update_userinfo_form">
	<h1>Genaktivering af medlemskab</h1>
	<? if($user["membership"] && !$user["membership"]["subscription_id"]): ?>	
	<p>Ved genaktivering vil der blive oprettet en ordre på 1 stk. kontingent, og du vil blive ført til en betalingsside, hvor medlemmet kan betale.</p>

	<?= $UC->formStart("reactivateUserMembership/$user_id", ["class" => "form_reactivate"]) ?> 

		<?= $HTML->serverMessages() ?>

		<fieldset>
			
			<?= $UC->input("item_id", array(
				"label" => "Vælg et medlemskab",
				"type" => "select",
				"options" => $membership_options,
				)) ?>

		</fieldset>

		<ul class="actions">
			<li class="cancel"><a href="/medlemshjaelp/brugerprofil/<?= $user_id ?>" class="button">Annullér</a></li>
			<?= $UC->submit("Genaktiver", array("class" => "primary", "wrapper" => "li.save")) ?>
		</ul>

	<?= $UC->formEnd() ?>

	<? else: ?>
	<p>Dette medlemskab er ikke inaktivt, så hvad laver du her?</p>

	<? endif; ?>

</div>