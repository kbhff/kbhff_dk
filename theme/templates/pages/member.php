<?php
$IC = new Items();
global $action;

$item = $IC->getItem(array("tags" => "page:member", "extend" => array("tags" => true, "user" => true, "mediae" => true, "comments" => true, "readstate" => true)));
if($item) {
	$this->sharingMetaData($item);
}

$member_subnavigation = $this->navigation("sub-member");

?>

<div class="scene member i:scene">

	<? if($member_subnavigation && isset($member_subnavigation["nodes"])) { ?>
	<ul class="subnavigation">
		<? foreach($member_subnavigation["nodes"] as $node): ?>
		<li><a href="<?= $node["link"] ?>"><?= $node["name"] ?></a></li>
		<? endforeach;?>
	</ul>
	<? } ?>


	<?	if($item):
	$media = $IC->sliceMediae($item); ?>

	<div class="article i:article id:<?= $item["item_id"] ?>" itemscope itemtype="http://schema.org/NewsArticle">

	<? if($media): ?>
		<div class="image item_id:<?= $item["item_id"] ?> format:<?= $media["format"] ?> variant:<?= $media["variant"] ?>">
			<p>Image: <a href="/images/<?= $item["item_id"] ?>/<?= $media["variant"] ?>/500x.<?= $media["format"] ?>"><?= $media["name"] ?></a></p>
		</div>
	<? endif; ?>


		<h1 itemprop="headline"><?= $item["name"] ?></h1>

		<? if($item["subheader"]): ?>
		<h2 itemprop="alternativeHeadline"><?= $item["subheader"] ?></h2>
		<? endif; ?>

		<div class="articlebody" itemprop="articleBody">
			<?= $item["html"]?>
		</div>

		<? if($item["mediae"]): ?>
			<? foreach($item["mediae"] as $media): ?>
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

</div>
