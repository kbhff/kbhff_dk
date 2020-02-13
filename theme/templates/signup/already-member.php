<?php
global $action;
global $model;

$IC = new Items();
$page_item = $IC->getItem(array("tags" => "page:already-member", "status" => 1, "extend" => array("user" => true, "tags" => true, "mediae" => true, "signupfee" => true)));
if($page_item) {
	$this->sharingMetaData($page_item);
}

?>

<div class="scene already_member i:scene">

<? if(session()->value("user_id") > 1 && session()->value("user_group_id") == 1): ?>
	<h1>Er du allerede medlem?</h1>
	<p>Det ser ud til, at du allerede – helt eller delvist – er oprettet som medlem.</p>
	<p>Hvis du allerede er oprettet som medlem, men ønsker at rette dine oplysninger, skal du ændre dem ved at logge ind på din <a href="/profil">profil</a>.</p>
	<p>Hvis du ønsker at oprette et nyt medlemskab, kan du <a href="resetSessionBeforeSignup">nulstille systemet og prøve igen</a>.</p>
	
<? else:?>
	<h1>Du er allerede medlem</h1>
	<p>Log ind og ret dit medlemskab via din <a href="/profil">profil</a>.</p>
<? endif; ?>

</div>
