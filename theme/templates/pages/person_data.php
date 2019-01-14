<?php
$IC = new Items();
global $action;

$item = $IC->getItem(array("tags" => "page:persondata", "extend" => array("tags" => true, "user" => true, "mediae" => true, "comments" => true, "readstate" => true)));
if($item) {
	$this->sharingMetaData($item);
}

?>

<div class="scene post i:generic">

<? // print the page-article created in the backend
	if($item):
	$media = $IC->sliceMedia($item); ?>

	<div class="article i:article id:<?= $item["item_id"] ?>" itemscope itemtype="http://schema.org/NewsArticle">

<? // print media if uploaded
	if($media): ?>
		<div class="image item_id:<?= $item["item_id"] ?> format:<?= $media["format"] ?> variant:<?= $media["variant"] ?>">
			<p>Image: <a href="/images/<?= $item["item_id"] ?>/<?= $media["variant"] ?>/500x.<?= $media["format"] ?>"><?= $media["name"] ?></a></p>
		</div>
		<? endif; ?>


		<h1 itemprop="headline"><?= $item["name"] ?></h1>

		<div class="articlebody" itemprop="articleBody">
			<?= $item["html"]?>
		</div>

<? // print all mediae uploaded
	if($item["mediae"]): ?>
			<? foreach($item["mediae"] as $media): ?>
		<div class="image item_id:<?= $item["item_id"] ?> format:<?= $media["format"] ?> variant:<?= $media["variant"] ?>">
			<p>Image: <a href="/images/<?= $item["item_id"] ?>/<?= $media["variant"] ?>/500x.<?= $media["format"] ?>"><?= $media["name"] ?></a></p>
		</div>
			<? endforeach; ?>
		<? endif; ?>


	</div>



<? else: ?>


	<h1>Technology clearly doesn't solve everything on it's own.</h1>
	<h2>Technology needs humanity.</h2>
	<p>We could not find the specified post.</p>


<? endif; ?>


</div>
