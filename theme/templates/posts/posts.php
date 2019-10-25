<?php
global $action;
global $IC;
global $itemtype;


// get post tags for listing
$items = $IC->getItems(array("itemtype" => $itemtype, "status" => 1, "extend" => array("tags" => true, "user" => true, "mediae" => true)));

$sindex = isset($action[1]) ? $action[1] : false;
$limit = 20;

$items = $IC->paginate(array(
	"limit" => $limit, 
	"pattern" => array(
		"itemtype" => $itemtype, 
		// "order" => "status DESC",
		"status" => 1,
		"extend" => array(
			"user" => true,
			"tags" => true, 
			"mediae" => true
		)
	),
	"sindex" => $sindex
));

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
						$media = $IC->sliceMediae($item, "single_media"); ?>
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

			<? if($items["next"] || $items["prev"]): ?>
			<div class="pagination">
				<ul>
					<? if($items["prev"]): ?>
					<li class="previous"><a href="/nyheder/liste/<?= $items["prev"][0]["sindex"] ?>">Forrige side</a></li>
					<? else: ?>
					<li class="previous"><a class="disabled">Forrige side</a></li>
					<? endif; ?>
					<li>Side <?= $items["current_page"] ?> af <?= $items["page_count"] ?> sider</li>
					<? if($items["next"]): ?>
						<li class="next"><a href="/nyheder/liste/<?= $items["next"][0]["sindex"] ?>">Næste side</a></li>
					<? else: ?>	
					<li class="next"><a class="disabled">Næste side</a></li>
					<? endif; ?>
				</ul>
			</div>
			<? endif; ?>

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
						<li class="selected"><a href="/nyheder">Alle artikler</a></li>
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
