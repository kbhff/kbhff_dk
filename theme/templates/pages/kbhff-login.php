<?php
// Get variables from the controller
global $action;
global $model;

// Create instance of class
$IC = new Items();

// Get the associated Janitor page
$page_item = $IC->getItem(array("tags" => "page:login", "extend" => array("user" => true, "tags" => true, "mediae" => true)));
if($page_item) {
	$this->sharingMetaData($page_item);
}

// If forward_url exists in session or post, save it in the session
$forward_url = getVar("forward_url");
if($forward_url) {
	session()->value("login_forward", $forward_url);
}

// Get username and save it in a variable
$username = stringOr(getPost("username"), session()->value("temp-username"));
?>


<div class="scene login i:login">

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


		<?= $HTML->articleInfo($page_item, "/bliv-medlem", [
			"media" => $media,
		]) ?>


		<? if($page_item["html"]): ?>
		<div class="articlebody" itemprop="articleBody">
			<?= $page_item["html"] ?>
		</div>
		<? endif; ?>
	</div>
<? else:?>
	<h1>Log ind</h1>

<? if(message()->hasMessages()): ?>
	<? $all_messages = message()->getMessages();
	message()->resetMessages();
	foreach($all_messages as $type => $messages):
	foreach($messages as $message): ?>
	<p class="<?= $type ?>"><?= $message ?></p>
	<? endforeach;?>
<? endforeach;?>
<? endif; ?>

	<p>
		I medlemssystemet kan du bestille varer, booke vagter og administrere dit medlemskab.
		Du kan bruge e-mailadresse, telefonnummer eller medlemsnummer som brugernavn til at logge ind.
		<strong>Velkommen indenfor!</strong>
	</p>
<? endif; ?>

	<?= $model->formStart("dual", array("class" => "login labelstyle:inject")) ?>

	

		<fieldset>
			<?= $model->input("username", array(
				"required" => true,
				"value" => $username,
				"pattern" => "^(1|[0-9]{4,5}|[\+0-9\-\.\s\(\)]{5,18}|[\w\.\-_\+]+@[\w\-\.]+\.\w{2,10})$",
				"label" => "Brugernavn",
				"hint_message" => "Brug dit medlemsnr, email eller telefonnummer som brugernavn",
				"error_message" => "Det ligner ikke et gyldigt brugernavn",
			)); ?>
			<?= $model->input("password", array(
				"required" => true,
				"min" => 1,
				"label" => "Adgangskode",
				"hint_message" => "Skriv din adgangskode (8-20 tegn)",
				"error_message" => "Ugyldig adgangskode",
			)); ?>
		</fieldset>

		<ul class="actions">
			<?= $model->submit("Log ind", array("class" => "primary", "wrapper" => "li.login")) ?>
		</ul>
	<?= $model->formEnd() ?>
	
	<p class="forgot">Har du <a href="/login/glemt">glemt din adgangskode</a>?</p>


<?	if(defined("SITE_SIGNUP") && SITE_SIGNUP): ?>
		<p class="signup">Endnu ikke medlem? <a href="<?= SITE_SIGNUP ?>">Meld dig ind nu</a>.</p>
<?	endif; ?>

</div>
