<?php
global $IC;
global $action;
global $itemtype;
global $model;


$IC = new Items();
$related_items = false;

// Use special property, fixed_url_identifier to identify topic
$fixed_url_identifier = $action[1];
$sql = "SELECT item_id FROM ".SITE_DB.".item_signupfee WHERE fixed_url_identifier = '$fixed_url_identifier' LIMIT 1";

$query = new Query;
if($query->sql($sql)) {
	$item_id = $query->result(0, "item_id");
	$item = $IC->getItem(array("id" => $item_id, "status" => 1, "extend" => array("tags" => true, "user" => true, "mediae" => true, "comments" => true, "prices" => true, "readstate" => true, "subscription_method" => true)));
}

// attempt look up by sindex, for fallback purposes
else {
	$item = $IC->getItem(array("sindex" => $action[1], "status" => 1, "extend" => array("tags" => true, "user" => true, "mediae" => true, "readstate" => true, "prices" => true, "subscription_method" => true)));
}


if($item) {
	$this->sharingMetaData($item);

	// find previous and next items 
	$next = $IC->getNext($item["item_id"], array("itemtype" => $item["itemtype"], "tags" => $item["tags"], "order" => "position ASC", "status" => 1, "extend" => true));
	$prev = $IC->getPrev($item["item_id"], array("itemtype" => $item["itemtype"], "tags" => $item["tags"], "order" => "position ASC", "status" => 1, "extend" => true));

	// set related pattern
	$related_pattern = array("itemtype" => $item["itemtype"], "tags" => $item["tags"], "exclude" => $item["id"]);
	// add base pattern properties
	$related_pattern["limit"] = 3;
	$related_pattern["extend"] = array("tags" => true, "user" => true, "mediae" => true, "prices" => true, "subscription_method" => true, "readstate" => true);

	// get related memberships
	$related_items = $IC->getRelatedItems($related_pattern);
}
	
?>

<div class="scene membership i:membership">


<? if($item):
	$media = $IC->sliceMediae($item, "single_media"); ?>

	<div class="article i:article id:<?= $item["item_id"] ?> service" itemscope itemtype="http://schema.org/Article" data-csrf-token="<?= session()->value("csrf") ?>">

		<? if($media): ?>
		<div class="image item_id:<?= $item["item_id"] ?> format:<?= $media["format"] ?> variant:<?= $media["variant"] ?>"></div>
		<? endif; ?>


		<?= $HTML->articleTags($item, ["context" => false]) ?>


		<h1 itemprop="headline"><?= $item["name"] ?></h1>


		<?= $HTML->articleInfo($item, "/bliv-medlem/".$item["fixed_url_identifier"], [
			"media" => $media,
			"sharing" => true
		]) ?>



		<div class="c-wrapper">
			<div class="c-two-thirds">
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
	
			<div class="c-one-third c-box">
				<h2>Meld dig ind</h2>
				<? $signupfees = $IC->getItems(array("itemtype" => "signupfee", "extend" => array("prices" => true))); 
				foreach($signupfees as $i => $signupfee):
					if($signupfee["associated_membership_id"] == $item["id"]): ?>
				<ul class="offer" itemscope itemtype="http://schema.org/Offer">
					<li class="name" itemprop="name" content="<?= $signupfee["name"] ?>"></li>
					<li class="currency" itemprop="priceCurrency" content="<?= $this->currency() ?>"></li>
					<li class="subscription_price"><h3>Indmeldelsesgebyr:</h3></li>
				<? // if signupfee has an offer, show the price, else show the default price or 'free'.
				if($signupfee["prices"]) {
						$offer_key = arrayKeyValue($signupfee["prices"], "type", "offer");
						$default_key = arrayKeyValue($signupfee["prices"], "type", "default");

					if($offer_key !== false) { ?>
					<li class="price default"><?= formatPrice($signupfee["prices"][$default_key]).(isset($signupfee["subscription_method"]) && $signupfee["subscription_method"] && $signupfee["prices"][$default_key]["price"] ? ' / '.$signupfee["subscription_method"]["name"] : '') ?></li>
					<li class="price offer" itemprop="price" content="<?= $signupfee["prices"][$offer_key]["price"]?>"><?= formatPrice($signupfee["prices"][$offer_key]).(isset($signupfee["subscription_method"]) && $signupfee["subscription_method"] && $signupfee["prices"][$default_key]["price"] ? ' / '.$signupfee["subscription_method"]["name"] : '') ?></li>
				<? }
					else if($signupfee["prices"][$default_key]["price"]) { ?>
					<li class="price" itemprop="price" content="<?= $signupfee["prices"][$default_key]["price"]?>"><?= formatPrice($signupfee["prices"][$default_key]).(isset($signupfee["subscription_method"]) && $signupfee["subscription_method"] && $signupfee["prices"][$default_key]["price"] ? ' / '.$signupfee["subscription_method"]["name"] : '') ?></li>
				<? }
					else { ?>
					<li class="price" itemprop="price" content="<?= $signupfee["prices"][$default_key]["price"] ?>">Free</li>
				<? } ?>
					<li class="url" itemprop="url" content="<?$url?>"></li>
			<? }
						
				 ?>
			
				</ul>
				<? endif;
			endforeach;?>
				
				
				<h3>Årligt kontingent:</h3>
				<?= $HTML->frontendOffer($item, SITE_URL."/bliv-medlem") ?>
				
				
			
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
			<h2>Andre medlemskaber <a href="/bliv-medlem">(oversigt)</a></h2>
			<ul class="items membership">
			<?	foreach($related_items as $item):
				$media = $IC->sliceMediae($item, "single_media"); ?>
				<li class="item membership item_id:<?= $item["item_id"] ?>" itemscope itemtype="http://schema.org/NewsArticle" data-readstate="<?= $item["readstate"] ?>">
			
					<h3 itemprop="headline"><a href="/bliv-medlem/medlemskaber/<?= $item["fixed_url_identifier"] ?>"><?= strip_tags($item["name"]) ?></a></h3>

					<h4>Årligt kontingent:</h4>
					<?= $HTML->frontendOffer($item, SITE_URL."/bliv-medlem") ?>


					<?= $HTML->articleInfo($item, "/bliv-medlem/medlemskaber/".$item["fixed_url_identifier"], [
						"media" => $media
					]) ?>
					
					<h4>Indmeldelsesgebyr:</h4>
					<?foreach($signupfees as $i => $signupfee):
						if($signupfee["associated_membership_id"] == $item["id"]): ?>
					<ul class="offer" itemscope itemtype="http://schema.org/Offer">
						<li class="name" itemprop="name" content="<?= $signupfee["name"] ?>"></li>
						<li class="currency" itemprop="priceCurrency" content="<?= $this->currency() ?>"></li>
						
					<? // if signupfee has an offer, show the price, else show the default price or 'free'.
					if($signupfee["prices"]) {
							$offer_key = arrayKeyValue($signupfee["prices"], "type", "offer");
							$default_key = arrayKeyValue($signupfee["prices"], "type", "default");

						if($offer_key !== false) { ?>
						<li class="price default"><?= formatPrice($signupfee["prices"][$default_key]).(isset($signupfee["subscription_method"]) && $signupfee["subscription_method"] && $signupfee["prices"][$default_key]["price"] ? ' / '.$signupfee["subscription_method"]["name"] : '') ?></li>
						<li class="price offer" itemprop="price" content="<?= $signupfee["prices"][$offer_key]["price"]?>"><?= formatPrice($signupfee["prices"][$offer_key]).(isset($signupfee["subscription_method"]) && $signupfee["subscription_method"] && $signupfee["prices"][$default_key]["price"] ? ' / '.$signupfee["subscription_method"]["name"] : '') ?></li>
					<? }
						else if($signupfee["prices"][$default_key]["price"]) { ?>
						<li class="price" itemprop="price" content="<?= $signupfee["prices"][$default_key]["price"]?>"><?= formatPrice($signupfee["prices"][$default_key]).(isset($signupfee["subscription_method"]) && $signupfee["subscription_method"] && $signupfee["prices"][$default_key]["price"] ? ' / '.$signupfee["subscription_method"]["name"] : '') ?></li>
					<? }
						else { ?>
						<li class="price" itemprop="price" content="<?= $signupfee["prices"][$default_key]["price"] ?>">Free</li>
					<? } ?>
						<li class="url" itemprop="url" content="<?$url?>"></li>
					<? }
							
					 ?>

					</ul>
					<? endif;
				endforeach;?>

					<? if($item["description"]): ?>
					<div class="description" itemprop="description">
						<p><?= nl2br($item["description"]) ?></p>
					</div>
					<? endif; ?>

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
