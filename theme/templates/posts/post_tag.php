<?php
global $IC;
global $action;
global $itemtype;

$sindex = $action[2];
$selected_tag = urldecode($action[1]);

$item = $IC->getItem(array("sindex" => $sindex, "status" => 1, "extend" => array("tags" => true, "user" => true, "mediae" => true, "comments" => true)));
if($item) {
	$this->sharingMetaData($item);

	// set related pattern
	$related_pattern = array("itemtype" => $item["itemtype"], "tags" => $item["tags"], "exclude" => $item["id"]);

}
else {
	// itemtype pattern for missing item
	$related_pattern = array("itemtype" => $itemtype);
}

// add base pattern properties
$related_pattern["limit"] = 4;
$related_pattern["extend"] = array("tags" => true, "user" => true, "mediae" => true);

// get related items
$related_items = $IC->getRelatedItems($related_pattern);

$categories = $IC->getTags(array("context" => $itemtype, "order" => "value"));

?>

<div class="scene post i:articles">
	<div class="banner i:banner variant:random format:jpg"></div>

	<div class="c-wrapper">
		
		<div class="c-two-thirds">

		<? if($item):
			$media = $IC->sliceMediae($item, "mediae"); ?>
			<div class="article i:article id:<?= $item["item_id"] ?><?= $item["classname"] ? " ".$item["classname"] : "" ?>" itemscope itemtype="http://schema.org/NewsArticle">
	
				<? if($media): ?>
				<div class="image item_id:<?= $item["item_id"] ?> format:<?= $media["format"] ?> variant:<?= $media["variant"] ?>">
					<p>Image: <a href="/images/<?= $item["item_id"] ?>/<?= $media["variant"] ?>/500x.<?= $media["format"] ?>"><?= $media["name"] ?></a></p>
				</div>
				<? endif; ?>
	
	
				<?= $HTML->articleTags($item, [
					"context" => [$itemtype],
					"url" => "/nyheder/tag",
					"default" => ["/nyheder", "Alle"]
				]) ?>
	
	
				<h1 itemprop="headline"><?= $item["name"] ?></h1>
	
	
				<?= $HTML->articleInfo($item, "/nyheder/".$item["sindex"], [
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


		<? else: ?>

			<h1>Hov!</h1>
			<h2>Der skete en fejl.</h2>
			<p>Vi kunne ikke finde den ønskede side.</p>

		<? endif; ?>


		<? if($related_items): ?>
			<div class="related posts">
				<h2>Relaterede artikler</h2>

				<ul class="items articles">
		<?		foreach($related_items as $item): 
					$media = $IC->sliceMediae($item); ?>
					<li class="item article item_id:<?= $item["item_id"] ?>" itemscope itemtype="http://schema.org/NewsArticle">

						<? if($media): ?>
						<div class="image item_id:<?= $media["item_id"] ?> format:<?= $media["format"] ?> variant:<?= $media["variant"] ?>"></div>
						<? endif; ?>

						<?= $HTML->articleTags($item, [
							"context" => [$itemtype],
							"url" => "/nyheder/tag"
						]) ?>

						<h3 itemprop="headline"><a href="/nyheder/<?= $item["sindex"] ?>"><?= $item["name"] ?></a></h3>

						<?= $HTML->articleInfo($item, "/nyheder/".$item["sindex"], [
							"media" => $media, 
							"sharing" => true
						]) ?>

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

		</div>

		<div class="c-one-third">
			<div class="c-box">
				<h3>Kategorier</h3>

			<? if($categories): ?>
				<div class="categories">
					<ul class="tags">
						<li><a href="/nyheder">Alle nyheder</a></li>
					<? foreach($categories as $tag): ?>
						<li<?= ($selected_tag == $tag["value"] ? ' class="selected"' : '') ?>><a href="/nyheder/tag/<?= urlencode($tag["value"]) ?>"><?= $tag["value"] ?></a></li>
						<? endforeach; ?>
					</ul>
				</div>
			<? endif; ?>
			</div>

			<!--div class="c-box">
				<h3>Example box</h3>

				<ul>
					<li>Lorem ipsum</li>
					<li>Lorem ipsum</li>
					<li>Lorem ipsum</li>
					<li>Lorem ipsum</li>
					<li>Lorem ipsum</li>
				</ul>

			</div-->
		</div>

	</div>

</div>
