<?php
global $action;
global $model;
global $IC;

$SC = new Shop();

$IC = new Items();
$page_item = $IC->getItem(array("tags" => "page:signup", "status" => 1, "extend" => array("user" => true, "tags" => true, "mediae" => true)));
if($page_item) {
	$this->sharingMetaData($page_item);
}

include_once("classes/system/department.class.php");
$DC = new Department();
$departments = $DC->getDepartmentsAcceptSignups();

$email = $model->getProperty("email", "value");

$membership_item = false;
$cart = $SC->getCart();
if($cart && $cart["items"]) {

	foreach($cart["items"] as $cart_item) {

		$item = $IC->getItem(["id" => $cart_item["item_id"]]);
		if($item["itemtype"] == "signupfee") {
			$signupfee_item = $IC->getItem(["id" => $cart_item["item_id"], "extend" => ["prices" => true]]);
			$membership_item = $IC->getItem(["id" => $signupfee_item["associated_membership_id"], "extend" => ["prices" => true]]);
		}

	}

}

?>
<div class="scene signup i:signup">

<? if($membership_item): ?>

	<? if($page_item):
		$media = $IC->sliceMediae($page_item, "single_media"); ?>
		<div class="article i:article id:<?= $page_item["item_id"] ?>" itemscope itemtype="http://schema.org/Article">

			<? if($media): ?>
			<div class="image item_id:<?= $page_item["item_id"] ?> format:<?= $media["format"] ?> variant:<?= $media["variant"] ?>"></div>
			<? endif; ?>


			<?= $HTML->articleTags($page_item, [
				"context" => false
			]) ?>


			<h1 itemprop="headline"><?= $page_item["name"] ?></h1>

			<? if($page_item["subheader"]): ?>
			<h2 itemprop="alternativeHeadline"><?= $page_item["subheader"] ?></h2>
			<? endif; ?>


			<?= $HTML->articleInfo($page_item, "/bliv-medlem/tilmelding", [
				"media" => $media,
			]) ?>


			<? if($page_item["html"]): ?>
			<div class="articlebody" itemprop="articleBody">
				<?= $page_item["html"] ?>
			</div>
			<? endif; ?>
		</div>
	<? else:?>

		<h1>Meld dig ind</h1>

	<? endif; ?>

	<?= $model->formStart("save", array("class" => "signup labelstyle:inject")) ?>

		<div class="membership">
			<p>Du er i gang med at melde dig ind som:</p>
			<h2><?= $membership_item["name"] ?></h2>
			<div class="introduction">
				<?= $membership_item["introduction"] ?>
			</div>
		</div>

		<?= $HTML->serverMessages(array("type" => "error")); ?>


		<fieldset>
			<?= $model->input("firstname", array("required" => true, "label" => "Fornavn", "hint_message" => "Skriv dit fornavn her", "error_message" => "Dit fornavn må kun indeholde bogstaver.")) ?>
			<?= $model->input("lastname", array("required" => true, "label" => "Efternavn", "hint_message" => "Skriv dit efternavn her", "error_message" => "Dit efternavn må kun indeholde bogstaver.")) ?>
			<?= $model->input("email", array("required" => true, "label" => "Din email", "value" => $email, "hint_message" => "Indtast din email.", "error_message" => "Du har indtastet en ugyldig e-mailadresse.")); ?>
			<?= $model->input("confirm_email", array("label" => "Gentag din email", "required" => true, "hint_message" => "Indtast din email igen.", "error_message" => "De to email adresser er ikke ens.")); ?>
			<?= $model->input("mobile", array("label" => "Mobilnummer", "hint_message" => "Indtast dit mobilnummer.", "error_message" => "Det skal være et dansk mobilnummer.")); ?>
			<?= $model->input("password", array("required" => true, "label" => "Adgangskode", "hint_message" => "Indtast en adgangskode på mindst 8 karakterer.", "error_message" => "Din adgangskode skal være mere end 8 karakterer.")); ?>
			<?= $model->input("department_id", array("type" => "select", "required" => true, "label" => "Vælg lokalafdeling", "options" => $HTML->toOptions($departments, "id", "name", ["add" => ["" => "Vælg afdeling"]]),)); ?>
			<?= $model->input("terms", array("label" => 'Jeg accepterer <a href="/persondata" target="_blank">KBHFF\'s retningslinjer for behandling og opbevaring af persondata</a>', "hint_message" => "Nødvendigt for at blive medlem af KBHFF.", "error_message" => "Du skal acceptere KBHFF's retningslinjer for at være medlem.")); ?>
			<?= $model->input("maillist", array("type" => "checkbox", "label" => "Jeg vil gerne modtage KBHFF's nyhedsbrev.", "value" => "Nyheder")); ?>
		</fieldset>

		<ul class="actions">
			<li class="reject"><a href="/bliv-medlem" class="button">Annuller</a></li>
			<?= $model->submit("Meld mig ind", array("class" => "primary", "wrapper" => "li.signup")) ?>
		</ul>
	<?= $model->formEnd() ?>


<? else: ?>

	<h1>Meld dig ind</h1>

	<h2>Der er ikke noget medlemsskab i din kurv</h2>
	<p>Tag et kig på vores <a href="/bliv-medlem">medlemskaber</a>.</p>

<? endif; ?>

</div>
