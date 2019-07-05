<?php
global $IC;
global $action;
global $itemtype;

$sindex = $action[0];
$item = $IC->getItem(array("sindex" => $sindex, "extend" => array("tags" => true, "user" => true, "mediae" => true, "comments" => true)));
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
$related_pattern["limit"] = 5;
$related_pattern["extend"] = array("tags" => true, "user" => true, "mediae" => true);

// get related items
$related_items = $IC->getRelatedItems($related_pattern);

$categories = $IC->getTags(array("context" => $itemtype, "order" => "value"));

?>

<div class="scene post i:articles">
	<div class="banner i:banner variant:random format:jpg"></div>

<? if($item):
	$media = $IC->sliceMedia($item); ?>

	<div class="c-wrapper">
		
		<div class="c-two-thirds article i:article id:<?= $item["item_id"] ?><?= $item["classname"] ? " ".$item["classname"] : "" ?>" itemscope itemtype="http://schema.org/NewsArticle"
			data-csrf-token="<?= session()->value("csrf") ?>"
			data-readstate="<?= $item["readstate"] ?>"
			data-readstate-add="<?= $this->validPath("/janitor/admin/profile/addReadstate/".$item["item_id"]) ?>" 
			data-readstate-delete="<?= $this->validPath("/janitor/admin/profile/deleteReadstate/".$item["item_id"]) ?>" 
			>
	
			<? if($media): ?>
			<div class="image item_id:<?= $item["item_id"] ?> format:<?= $media["format"] ?> variant:<?= $media["variant"] ?>">
				<p>Image: <a href="/images/<?= $item["item_id"] ?>/<?= $media["variant"] ?>/500x.<?= $media["format"] ?>"><?= $media["name"] ?></a></p>
			</div>
			<? endif; ?>
	
	
			<?= $HTML->articleTags($item, [
				"context" => [$itemtype],
				"url" => "/artikler/tag",
				"default" => ["/artikler", "Alle"]
			]) ?>
	
	
			<h1 itemprop="headline"><?= $item["name"] ?></h1>
	
	
			<?= $HTML->articleInfo($item, "/artikel/".$item["sindex"], [
				"media" => $media, 
				"sharing" => true
			]) ?>
	
	
			<div class="articlebody" itemprop="articleBody">
				<?= $item["html"] ?>
			</div>
	
			<? if($item["mediae"]): ?>
				<? foreach($item["mediae"] as $media): ?>
			<div class="image item_id:<?= $item["item_id"] ?> format:<?= $media["format"] ?> variant:<?= $media["variant"] ?>">
				<p>Image: <a href="/images/<?= $item["item_id"] ?>/<?= $media["variant"] ?>/500x.<?= $media["format"] ?>"><?= $media["name"] ?></a></p>
			</div>
				<? endforeach; ?>
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
						<li><a href="/artikler/tag/<?= urlencode($tag["value"]) ?>"><?= $tag["value"] ?></a></li>
						<? endforeach; ?>
					</ul>
				</div>
			<? endif; ?>
			</div>

				<div class="c-box">
					<h3>Example box</h3>

					<ul>
						<li>Lorem ipsum</li>
						<li>Lorem ipsum</li>
						<li>Lorem ipsum</li>
						<li>Lorem ipsum</li>
						<li>Lorem ipsum</li>
					</ul>

				</div>
		</div>

	</div>



<? else: ?>


	<h1>Technology clearly doesn't solve everything on it's own.</h1>
	<h2>Technology needs humanity.</h2>
	<p>We could not find the specified post.</p>


<? endif; ?>


<? if($related_items): ?>
	<div class="related posts">
		<h2>Relaterede artikler</h2>

		<ul class="items articles">
<?		foreach($related_items as $item): 
			$media = $IC->sliceMedia($item); ?>
			<li class="item article item_id:<?= $item["item_id"] ?>" itemscope itemtype="http://schema.org/NewsArticle">

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
	<?	endforeach; ?>
		</ul>
	</div>
<? endif; ?>

</div>
