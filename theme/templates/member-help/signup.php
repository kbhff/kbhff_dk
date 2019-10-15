<?php
global $action;
global $model;
global $IC;

$IC = new Items();
$page_item = $IC->getItem(array("tags" => "page:member_help_signup", "extend" => array("user" => true, "tags" => true, "mediae" => true)));
if($page_item) {
	$this->sharingMetaData($page_item);
}

include_once("classes/system/department.class.php");
$DC = new Department();
$departments = $DC->getDepartments();

$signupfees = $IC->getItems(array("itemtype" => "signupfee", "status" => 1, "extend" => true));
$email = $model->getProperty("email", "value");
?>
<div class="scene member_help_signup i:member_help_signup">


<? if($page_item && $page_item["status"]):
	$media = $IC->sliceMediae($page_item); ?>
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


		<?= $HTML->articleInfo($page_item, "/signup", [
			"media" => $media,
		]) ?>


		<? if($page_item["html"]): ?>
		<div class="articlebody" itemprop="articleBody">
			<?= $page_item["html"] ?>
		</div>
		<? endif; ?>
	</div>
<? else:?>
	<h1>Opret nyt medlem</h1>
<? endif; ?>
	<h2>Brugeroplysninger</h2>
	<?= $model->formStart("save", array("class" => "member_help_signup labelstyle:inject")) ?>
		<?= $model->input("quantity", array("type" => "hidden", "value" => 1)); ?>
		<div class="c-wrapper">
			<div class="c-one-half">
				
			<? if(message()->hasMessages(array("type" => "error"))): ?>
				<p class="errormessage">
			<?	$messages = message()->getMessages(array("type" => "error"));
					message()->resetMessages();
					foreach($messages as $message): ?>
					<?= $message ?><br>
			<?	endforeach;?>
				</p>
			<?	endif; ?>
			
				<fieldset>
					<?= $model->input("firstname", array("required" => true, "label" => "Fornavn", "hint_message" => "Skriv medlemmets fornavn her", "error_message" => "Fornavn er obligatorisk. Det kan kun indeholde bogstaver.")) ?>
					<?= $model->input("lastname", array("required" => true, "label" => "Efternavn", "hint_message" => "Skriv medlemmets efternavn her", "error_message" => "Efternavn er obligatorisk. Det kan kun indeholde bogstaver.")) ?>
					<?= $model->input("email", array("required" => true, "label" => "Medlemmets e-mailadresse", "value" => $email, "hint_message" => "Indtast medlemmets e-mailadresse.", "error_message" => "Du har indtastet en ugyldig e-mailadresse.")); ?>
					<?= $model->input("confirm_email", array("label" => "Gentag medlemmets e-mailadresse", "required" => true, "hint_message" => "Indtast medlemmets e-mailadresse igen.", "error_message" => "De to e-mailadresser er ikke ens.")); ?>
					<?= $model->input("item_id", array("required" => true, "type" => "select", "label" => "Vælg medlemskab", "hint_message" => "Vælg typen af medlemskab.", "error_message" => "Der skal vælges et medlemskab.", "options" => $HTML->toOptions($signupfees, "id", "name", ["add" => ["" => "Vælg medlemskab"]]),)); ?>
					<?= $model->input("department_id", array("required" => true,"type" => "select", "label" => "Vælg lokalafdeling", "options" => $HTML->toOptions($departments, "id", "name", ["add" => ["" => "Vælg afdeling"]]),)); ?>						
				</fieldset>
				
			</div>
			<div class="c-one-half c-box">
				<fieldset>
					<div class="terms">
						<h3>Godkend brug af oplysninger</h3>
						<p class="metatext">Vis denne side til personen og bed personen selv om at sætte krydset.</p>
						<p>Vi opbevarer og anvender følgende informationer om dig: Dit navn, emailadresse, telefonnumer og indkøb hos os.
						<p>Vi videregiver ikke disse oplysninger til nogen tredjepart, men du er selv ansvarlig for de persondata, du skriver på foreningens offentlige sider, eksempelvis wikien.</p>
						<p>Det er en forudsætning for din indmeldelse, at du giver dit samtykke til disse betingelser.</p>
					</div>
					<?= $model->input("terms", array("label" => 'Jeg accepterer <a href="/persondata" target="_blank">KBHFF\'s vilkår og betingelser</a>.', "required" => true, "hint_message" => "Nødvendigt for at blive medlem af KBHFF.", "error_message" => "Man kan ikke være medlem, hvis man ikke accepterer KBHFF's betingelser.")); ?>
					<?= $model->input("maillist", array("type" => "checkbox", "label" => "Jeg vil gerne modtage KBHFF's nyhedsbrev.", "value" => "Nyheder")); ?>

				</fieldset>
				
			</div>
			<ul class="actions">
				<li class="reject"><a href="/medlemshjaelp" class="button">Annuller</a></li>
				<?= $model->submit("Næste", array("class" => "primary", "wrapper" => "li.member_help_signup")) ?>
			</ul>
		</div>
	<?= $model->formEnd() ?>

</div>
