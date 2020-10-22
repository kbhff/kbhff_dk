<?php
global $IC;
global $action;
global $itemtype;
global $model;


$IC = new Items();
$query = new Query;

$related_items = false;

// Use special property, fixed_url_identifier to identify topic
$fixed_url_identifier = $action[1];

$item = $IC->getItem(array("status" => 1, "itemtype" => "membership", "where" => "membership.fixed_url_identifier = '$fixed_url_identifier'", "extend" => array("mediae" => true, "prices" => true, "subscription_method" => true)));

if($item) {

	$this->sharingMetaData($item);

	// Get signupfee for this membership to be able to show signup price
	$signupfee_item = $IC->getItem(["status" => 1, "itemtype" => "signupfee", "where" => "signupfee.associated_membership_id=".$item["item_id"], "extend" => ["prices" => true]]);

	// set related pattern
	$related_pattern = array("itemtype" => $item["itemtype"], "exclude" => $item["id"]);
	// add base pattern properties
	$related_pattern["limit"] = 3;
	$related_pattern["extend"] = array("mediae" => true, "prices" => true, "subscription_method" => true);

	// get related memberships
	$related_items = $IC->getRelatedItems($related_pattern);
}
	
?>

<div class="scene membership i:membership">


<? if($item):
	$media = $IC->sliceMediae($item, "single_media"); 
	
	?>

	<div class="c-wrapper">
		<div class="c-two-thirds">

			<div class="article i:article id:<?= $item["item_id"] ?> service" itemscope itemtype="http://schema.org/Article" data-csrf-token="<?= session()->value("csrf") ?>">

				<? if($media): ?>
				<div class="image item_id:<?= $item["item_id"] ?> format:<?= $media["format"] ?> variant:<?= $media["variant"] ?>"></div>
				<? endif; ?>


				<h1 itemprop="headline"><?= $item["name"] ?></h1>


				<?= $HTML->articleInfo($item, "/bliv-medlem/".$item["fixed_url_identifier"], [
					"media" => $media,
					"sharing" => true
				]) ?>


				<div class="articlebody" itemprop="articleBody">
					<?= $item["html"] ?>
				
				</div>
		
				<?
				$mediae = $IC->filterMediae($item, "mediae");
				if($mediae): ?>
					<? foreach($mediae as $media): ?>
				<div class="image item_id:<?= $item["item_id"] ?> format:<?= $media["format"] ?> variant:<?= $media["variant"] ?>">
					<p>Image: <a href="/images/<?= $item["item_id"] ?>/<?= $media["variant"] ?>/500x.<?= $media["format"] ?>"><?= $media["name"] ?></a></p>
				</div>
					<? endforeach; ?>
				<? endif; ?>
			</div>
		</div>
	
		<div class="c-one-third">
			<div class="c-box">
				<h2>Meld dig ind</h2>
			
				<h3>Indmeldelsesgebyr:</h3>
				<?= $HTML->frontendOffer($item, SITE_URL."/bliv-medlem") ?>
				<h3>Årligt kontingent:</h3>
				<?= $HTML->frontendOffer($signupfee_item, SITE_URL."/bliv-medlem") ?>
			
			
		
				<?= $model->formStart("/bliv-medlem/addToCart", array("class" => "signup labelstyle:inject")) ?>
					<?= $model->input("quantity", array("value" => 1, "type" => "hidden")); ?>
					<?= $model->input("item_id", array("value" => $item["item_id"], "type" => "hidden")); ?>
	
					<ul class="actions">
						<?= $model->submit("Tilmeld", array("class" => "primary", "wrapper" => "li.signup")) ?>
					</ul>
				<?= $model->formEnd() ?>
			</div>
		</div>
	</div>


	<? // list of other memberships
	if($related_items): ?>
	<div class="related">
		<h2><a href="/bliv-medlem">Andre medlemskaber</a></h2>
		<ul class="items membership">
		<?	foreach($related_items as $item):
				$media = $IC->sliceMediae($item, "single_media");
				$related_signupfee_item = $IC->getItem(["status" => 1, "itemtype" => "signupfee", "where" => "signupfee.associated_membership_id=".$item["item_id"], "extend" => ["prices" => true]]);
		?>

			<li class="item membership item_id:<?= $item["item_id"] ?>" itemscope itemtype="http://schema.org/NewsArticle" data-readstate="<?= $item["readstate"] ?>">

				<h3 itemprop="headline"><a href="/bliv-medlem/medlemskaber/<?= $item["fixed_url_identifier"] ?>"><?= strip_tags($item["name"]) ?></a></h3>

				<? if($item["introduction"]): ?>
				<div class="description" itemprop="description">
					<?= nl2br($item["introduction"]) ?>
				</div>
				<? endif; ?>

				<h4>Indmeldelsesgebyr:</h4>
				<?= $HTML->frontendOffer($item, SITE_URL."/bliv-medlem") ?>

				<h4>Årligt kontingent:</h4>
				<?= $HTML->frontendOffer($related_signupfee_item, SITE_URL."/bliv-medlem") ?>

				<ul class="actions">
					<?= $model->link("Læs mere her", "/bliv-medlem/medlemskaber/".$item["fixed_url_identifier"], array("wrapper" => "li.readmore")) ?>
				</ul>

			</li>
		<?	endforeach; ?>
		</ul>
	</div>
	<? endif; ?>

<? else: ?>


	<h1>Hov!</h1>
	<h2>Der skete en fejl.</h2>
	<p>Vi kunne ikke finde den ønskede side.</p>


<? endif; ?>


</div>
