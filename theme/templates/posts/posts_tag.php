<?php
global $action;
global $IC;
global $itemtype;

$selected_tag = urldecode($action[1]);
$items = $IC->getItems(array("itemtype" => $itemtype, "status" => 1, "tags" => $itemtype.":".addslashes($selected_tag), "extend" => array("tags" => true, "user" => true, "mediae" => true)));

$categories = $IC->getTags(array("context" => $itemtype, "order" => "value"));

?>

<div class="scene posts tag i:articles">


	<div class="banner i:banner variant:2 format:jpg"></div>


	<div class="c-wrapper">

		<div class="c-two-thirds posts">

			<h1>Artikler om <?= $selected_tag ?></h1>



		<?	if($items): ?>
			<ul class="items articles">
				<? foreach($items as $item):
					$media = $IC->sliceMedia($item); ?>
				<li class="item article id:<?= $item["item_id"] ?>" itemscope itemtype="http://schema.org/NewsArticle"
					data-readstate="<?= $item["readstate"] ?>"
					>

					<? if($media): ?>
					<div class="image item_id:<?= $media["item_id"] ?> format:<?= $media["format"] ?> variant:<?= $media["variant"] ?>"></div>
					<? endif; ?>


					<?= $HTML->articleTags($item, [
						"context" => [$itemtype],
						"url" => "/artikler/tag"
					]) ?>


					<h3 itemprop="headline"><a href="/artikel/<?= $item["sindex"] ?>"><?= $item["name"] ?></a></h3>


					<?= $HTML->articleInfo($item, "/artikel/".$item["sindex"], [
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

			<h2>Technology needs humanity.</h2>
			<p>We could not find any posts with the selected tag.</p>

		<? endif; ?>


		</div>


		<div class="c-one-third">

			<div class="c-box">
				<h3>Kategorier</h3>

			<? if($categories): ?>
				<div class="categories">
					<ul class="tags">
						<li><a href="/artikler">Alle artikler</a></li>
					<? foreach($categories as $tag): ?>
						<li<?= ($selected_tag == $tag["value"] ? ' class="selected"' : '') ?>><a href="/artikler/tag/<?= urlencode($tag["value"]) ?>"><?= $tag["value"] ?></a></li>
						<? endforeach; ?>
					</ul>
				</div>
			<? endif; ?>
			</div>

		</div>

	</div>

</div>
