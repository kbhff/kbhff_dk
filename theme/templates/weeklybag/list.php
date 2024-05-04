<?php
global $action;
global $IC;
global $itemtype;
$model = $IC->typeObject($itemtype);

$page_item = $IC->getItem(array("tags" => "page:weekly-bag", "status" => 1, "extend" => array("user" => true, "tags" => true, "mediae" => true)));
if($page_item) {
	$this->sharingMetaData($page_item);
}


$sindex = isset($action[1]) ? $action[1] : false;
$limit = 52;

$items = $IC->paginate(array(
	"limit" => $limit, 
	"pattern" => array(
		"itemtype" => $itemtype, 
		"order" => "year DESC, week DESC",
		"status" => 1,
		"extend" => array(
			"user" => true,
			"tags" => true, 
			"mediae" => true
		)
	),
	"sindex" => $sindex
));


$WBC = $IC->typeObject("weeklybag");
$weeklybag_item = $WBC->getWeeklyBag();

?>

<div class="scene weeklybags i:scene">
	<div class="banner i:banner variant:random format:jpg"></div>


	<div class="c-wrapper">

		<div class="c-two-thirds">

		<? if($page_item):
			$media = $IC->sliceMediae($page_item, "single_media"); ?>
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
			<h1>Ugens poser</h1>
		<? endif; ?>


		<? if($items && $items["range_items"]): ?>
			<ul class="items articles">
				<? foreach($items["range_items"] as $item): ?>
				<li class="item article id:<?= $item["item_id"] ?>" itemscope itemtype="http://schema.org/NewsArticle">

					<h3 itemprop="headline"><a href="/ugens-pose/<?= $item["sindex"] ?>">Uge <?= $item["week"] ?>, <?= $item["year"] ?></a></h3>

					<?= $HTML->articleInfo($item, "/ugens-pose/".$item["sindex"]) ?>
					<p><?= $model->getWeeklyBagDate($item["week"], $item["year"]) ?></p>

				</li>
				<? endforeach; ?>
			</ul>

			<? if($items["next"] || $items["prev"]): ?>
			<div class="pagination">
				<ul>
					<? if($items["prev"]): ?>
					<li class="previous"><a href="/ugens-pose/liste/<?= $items["prev"][0]["sindex"] ?>">Forrige side</a></li>
					<? else: ?>
					<li class="previous"><a class="disabled">Forrige side</a></li>
					<? endif; ?>
					<li>Side <?= $items["current_page"] ?> af <?= $items["page_count"] ?> sider</li>
					<? if($items["next"]): ?>
						<li class="next"><a href="/ugens-pose/liste/<?= $items["next"][0]["sindex"] ?>">Næste side</a></li>
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

			<div class="c-box weeklybag">
			<? if($weeklybag_item): ?>
				<h3>Ugens pose - <?= $weeklybag_item["name"] ?></h3>
				<?= $weeklybag_item["html"] ?>
			<? else: ?>
				<h3>Ugens pose</h3>
				<p>Ugens pose er endnu ikke oprettet.</p>
			<? endif; ?>
			</div>

		</div>

	</div>
</div>
