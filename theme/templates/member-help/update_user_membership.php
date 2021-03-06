<?php
global $model;
global $action;
global $IC;
global $itemtype;

$UC = new SuperUser();
$IC = new Items();
include_once("classes/users/supermember.class.php");
$MC = new SuperMember();

$user_id = $action[1];
$user = $UC->getKbhffUser(["user_id" => $user_id]);
$membership = $MC->getMembers(["user_id" => $user_id]);
$memberships = $IC->getItems(array("itemtype" => "membership", "status" => 1, "extend" => array("subscription_method" => true, "prices" => true)));

// print_r($memberships);exit();
$this->pageTitle("Afdelinger");
?>

<div class="scene update_membership i:update_membership">
	<h1>Medlemskab</h1>
	<h2>Her kan du skifte medlemskab for <?=$user["firstname"] ? $user["firstname"]: $user["nickname"]?>.</h2>

	<?= $UC->formStart("updateUserMembership/$action[1]", ["class" => "form_membership"]) ?> 

		<?= $HTML->serverMessages() ?>

		<fieldset>
			<?= $UC->input("item_id", [
				"type" => "select",
				"label" => "Medlemstype",
				"options" => $UC->toOptions($memberships, "id", "name"),
				"value" => $membership ? $membership["item_id"] : "",
				"error_message" => "Du kan vælge blandt medlemskaber i listen",
				"hint_message" => "Vælg et medlemskab",
				"class" => "membership_input"
				]);
			?>
		</fieldset>

		<ul class="actions">
			<li class="cancel"><a href="/medlemshjaelp/brugerprofil/<?=$action[1]?>" class="button">Annullér</a></li>
			<?= $UC->submit("Opdater", array("class" => "primary", "wrapper" => "li.save")) ?>
		</ul>

	<?= $UC->formEnd() ?>

</div>