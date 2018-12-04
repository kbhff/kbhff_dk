<?php
global $action;
global $model;

$IC = new Items();
$page_item = $IC->getItem(array("tags" => "page:signupfees", "extend" => array("user" => true, "tags" => true, "mediae" => true, "signupfee" => true)));
if($page_item) {
	$this->sharingMetaData($page_item);
}
$signupfees = $IC->getItems(array("itemtype" => "signupfee", "order" => "position ASC", "status" => 1, "extend" => array("prices" => true)));

?>

<div class="scene membership i:membership">

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
	<h1>Vælg medlemsskab</h1>
<? endif; ?>

<? if($signupfees): ?>

	<div class="signupfees">


		<?= $HTML->serverMessages() ?>


		<ul class="signupfees">
			<? foreach($signupfees as $signupfee): ?>
			<li class="signupfee"<?= $signupfee["classname"] ? " ".$signupfee["classname"] : "" ?> itemprop="offers">
				<h3><?= $signupfee["name"] ?></h3>

				<?= $HTML->frontendOffer($signupfee, SITE_URL."/bliv-medlem", $signupfee["description"]) ?>

				<?= $model->formStart("/bliv-medlem/addToCart", array("class" => "signup labelstyle:inject")) ?>
					<?= $model->input("quantity", array("value" => 1, "type" => "hidden")); ?>
					<?= $model->input("item_id", array("value" => $signupfee["item_id"], "type" => "hidden")); ?>

					<ul class="actions">
						<?= $model->link("Read more", "/bliv-medlem/".$signupfee["sindex"], array("wrapper" => "li.readmore")) ?>
						<?= $model->submit("Join", array("class" => "primary", "wrapper" => "li.signup")) ?>
					</ul>
				<?= $model->formEnd() ?>

			</li>
			<? endforeach; ?>
		</ul>
	</div>

<? endif; ?>
