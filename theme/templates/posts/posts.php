<?php
global $action;
global $IC;
global $itemtype;



// List extension (page > 1)
if(count($action) === 2) {
	$page = $action[1];
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
		"extend" => [
			"tags" => true, 
			"user" => true, 
			"mediae" => true
		]
	],
	"page" => $page,
	"limit" => 12
]);



// Get categories
$categories = $IC->getTags(array("context" => $itemtype, "order" => "value"));


?>

<div class="scene posts i:articles">
	<div class="banner i:banner variant:3 format:jpg"></div>


	<div class="c-wrapper">

		<div class="c-two-thirds posts">

			<h1>Nyheder</h1>

		<? if($items && $items["range_items"]): ?>
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
				<? endforeach; ?>
			</ul>


			<?= $HTML->pagination($items, [
				"base_url" => "/nyheder",
			]) ?>


		<? else: ?>
			<p>Ingen nyheder</p>
		<? endif; ?>

		</div>


		<div class="c-one-third">

			<div class="c-box">
				<h3>Kategorier</h3>
			<? if($categories): ?>
				<div class="categories">
					<ul class="tags">
						<li class="selected"><a href="/nyheder">Alle nyheder</a></li>
						<? foreach($categories as $tag): ?>
						<li><a href="/nyheder/tag/<?= urlencode($tag["value"]) ?>"><?= $tag["value"] ?></a></li>
						<? endforeach; ?>
					</ul>
				</div>
			<? endif; ?>
			</div>

		</div>

	</div>
</div>
