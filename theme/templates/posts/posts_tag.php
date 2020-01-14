<?php
global $action;
global $IC;
global $itemtype;

$selected_tag = urldecode($action[1]);


// List extension (page > 1)
if(count($action) === 4) {
	$page = $action[3];
}
// Default list
else {
	$page = false;
}

// Get posts
$items = $IC->paginate([
	"pattern" => [
		"itemtype" => $itemtype, 
		"status" => 1, 
		"tags" => $itemtype.":".addslashes($selected_tag), 
		"extend" => [
			"tags" => true, 
			"user" => true, 
			"mediae" => true
		]
	],
	"page" => $page,
	"limit" => 20
]);


$categories = $IC->getTags(array("context" => $itemtype, "order" => "value"));

?>

<div class="scene posts tag i:articles">


	<div class="banner i:banner variant:2 format:jpg"></div>


	<div class="c-wrapper">

		<div class="c-two-thirds posts">

			<h1>Artikler om <?= $selected_tag ?></h1>



		<?	if($items && $items["range_items"]): ?>
			<ul class="items articles">
				<? foreach($items["range_items"] as $item):
					$media = $IC->sliceMediae($item, "mediae"); ?>
				<li class="item article id:<?= $item["item_id"] ?>" itemscope itemtype="http://schema.org/NewsArticle">

					<? if($media): ?>
					<div class="image item_id:<?= $media["item_id"] ?> format:<?= $media["format"] ?> variant:<?= $media["variant"] ?>"></div>
					<? endif; ?>


					<?= $HTML->articleTags($item, [
						"context" => [$itemtype],
						"url" => "/nyheder/tag"
					]) ?>


					<h3 itemprop="headline"><a href="/nyheder/tag/<?= urlencode($selected_tag) ?>/<?= $item["sindex"] ?>"><?= $item["name"] ?></a></h3>


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
				<? endforeach; ?>
			</ul>

		<? else: ?>

			<p>Vi kunne ikke finde nogle nyheder om <?= $selected_tag ?>.</p>

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

		</div>

	</div>

</div>
