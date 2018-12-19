<?php
global $action;
global $model;
global $IC;

$IC = new Items();
$page_item = $IC->getItem(array("tags" => "page:signup", "extend" => array("user" => true, "tags" => true, "mediae" => true)));
if($page_item) {
	$this->sharingMetaData($page_item);
}

include_once("classes/system/department.class.php");
$DC = new Department();
$departments = $DC->getDepartments();


$email = $model->getProperty("email", "value");
// $name = $model->getProperty("name", "value");
?>
<div class="scene signup i:signup">


<? if($page_item && $page_item["status"]):
	$media = $IC->sliceMedia($page_item); ?>
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
	<h1>Sign up</h1>
<? endif; ?>

<?= $model->formStart("save", array("class" => "signup labelstyle:inject")) ?>

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
		<?= $model->input("firstname", array("required" => true, "label" => "Fornavn", "hint_message" => "Skriv dit fornavn her", "error_message" => "Dit fornavn må kun indeholde bogstaver.")) ?>
		<?= $model->input("lastname", array("required" => true, "label" => "Efternavn", "hint_message" => "Skriv dit efternavn her", "error_message" => "Dit efternavn må kun indeholde bogstaver.")) ?>
		<?= $model->input("email", array("required" => true, "label" => "Din email", "value" => $email, "hint_message" => "Indtast din email.", "error_message" => "Du har indtastet en ugyldig emailadresse.")); ?>
		<?= $model->input("confirm_email", array("label" => "Gentag din email", "required" => true, "hint_message" => "Indtast din email igen.", "error_message" => "De to email adresser er ikke ens.")); ?>
		<?= $model->input("password", array("label" => "password", "hint_message" => "Indtast et password.", "error_message" => "Dit password skal være mellem 8 og 20 karakterer.")); ?>
		<?= $model->input("department_id", array("type" => "select", "label" => "Vælg lokalafdeling", "options" => $HTML->toOptions($departments, "id", "name", ["add" => ["" => "Vælg afdeling"]]),)); ?>
		<?= $model->input("terms", array("label" => 'Jeg accepterer <a href="/persondata" target="_blank">vilkår og betingelser</a>')); ?>
		<?= $model->input("maillist", array("type" => "checkbox", "label" => "Vil du modtage nyhedsbreve?", "value" => "Nyheder")); ?>
	</fieldset>

	<ul class="actions">
		<?= $model->submit("Næste", array("class" => "primary", "wrapper" => "li.signup")) ?>
		<li class="reject"><a href="/bliv-medlem" class="button">Annuller</a></li>
	</ul>
<?= $model->formEnd() ?>

</div>
