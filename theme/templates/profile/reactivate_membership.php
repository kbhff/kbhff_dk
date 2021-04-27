<?php
include_once("classes/system/department.class.php");
$DC = new Department();
$UC = new User();
$MC = new Member();
$IC = new Items();
$SC = new Shop();
$user = $UC->getKbhffUser();
$departments = $DC->getDepartmentsAcceptSignups();
$user_department = $UC->getUserDepartment();

$membership_renewal_value = $UC->getRenewalOptOut() ? 0 : 1;

$member = $MC->getMembership();
$memberships = $IC->getItems(array("itemtype" => "membership", "status" => 1, "extend" => array("subscription_method" => true, "prices" => true)));

$membership_options = array();
foreach($memberships as $membership) {
	// do not include current membership
	if($membership["item_id"] != $member["item_id"])  {
		$price = $SC->getPrice($membership["item_id"]);
		$membership_options[$membership["item_id"]] = strip_tags($membership["name"])." (".formatPrice($price).")";
		
	}
}

$this->pageTitle("Genaktiver medlemskab");
?>

<div class="scene update_userinfo_form i:update_userinfo_form">
	<h1>Genaktivering af medlemskab</h1>
	<? if($user["membership"] && !$user["membership"]["subscription_id"]): ?>	
	<h2>Velkommen igen</h2>
	<p>Når du vælger et medlemskab og trykker 'Genaktiver', vil der blive oprettet en ordre på 1 stk. kontingent, og du vil blive ført til betalingssiden.</p>

	<?= $UC->formStart("reactivateMembership", ["class" => "form_reactivate"]) ?> 

		<?= $HTML->serverMessages(["type" => "error"]) ?>

		<fieldset>
			
			<?= $UC->input("item_id", array(
				"label" => "Vælg et medlemskab",
				"type" => "select",
				"options" => $membership_options,
				"value" => $member["item_id"]
			)) ?>

		</fieldset>

		<ul class="actions">
			<li class="cancel"><a href="/profil" class="button">Annullér</a></li>
			<?= $UC->submit("Genaktiver", array("class" => "primary", "wrapper" => "li.save")) ?>
		</ul>

	<?= $UC->formEnd() ?>

	<? else: ?>
	<p>Dit medlemskab er ikke inaktivt, så hvad laver du her, ven?</p>

	<? endif; ?>

</div>