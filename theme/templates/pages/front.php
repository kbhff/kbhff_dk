<?php
$IC = new Items();

$page_item = $IC->getItem(array("tags" => "page:front", "status" => 1, "extend" => array("user" => true, "mediae" => true, "tags" => true)));
if($page_item) {
	$this->sharingMetaData($page_item);
}

$WBC = $IC->typeObject("weeklybag");
$weeklybag_item = $WBC->getWeeklyBag();

$post_items = $IC->getItems([
	"itemtype" => "post",
	"tags" => "on:frontpage",
	"status" => 1,
	"limit" => 12,
	"extend" => [
		"tags" => true,
		"readstate" => true,
		"user" => true,
		"mediae" => true
	]
]);
?>
<div class="scene front i:scene i:front">
	<div class="banner i:banner variant:random format:jpg"></div>

	<div class="c-wrapper">

		<div class="c-two-thirds">


		<? if($page_item): 
			$media = $IC->sliceMediae($page_item, "single_media"); ?>
			<div class="article i:article" itemscope itemtype="http://schema.org/Article">

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


				<?= $HTML->articleInfo($page_item, "/", [
					"media" => $media, 
					"sharing" => true
				]) ?>


				<? if($page_item["html"]): ?>
				<div class="articlebody" itemprop="articleBody">
					<?= $page_item["html"] ?>
				</div>
				<? endif; ?>
			</div>
		<? endif; ?>


		<? if($post_items): ?>
			<div class="news">
				<ul class="items articles">
				<? foreach($post_items as $item): 
					$media = $IC->sliceMediae($item, "mediae"); ?>
					<li class="item article id:<?= $item["item_id"] ?>" itemscope itemtype="http://schema.org/NewsArticle" data-readstate="<?= $item["readstate"] ?>">

						<? if($media): ?>
						<div class="image item_id:<?= $media["item_id"] ?> format:<?= $media["format"] ?> variant:<?= $media["variant"] ?>"></div>
						<? endif; ?>


						<?= $HTML->articleTags($item, [
							"context" => ["post"],
							"url" => "/nyheder/tag"
						]) ?>


						<h3 itemprop="headline"><a href="/nyheder/<?= $item["sindex"] ?>"><?= preg_replace("/<br>|<br \/>/", "", $item["name"]) ?></a></h3>


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
			</div>
		<?	else: ?>
			<p>Ingen nyheder.</p>
		<? endif ?>
		</div>

		<div class="c-one-third">

			<div class="c-box actions">
				<h3>Genveje</h3>
				<ul class="actions">
					<? if(session()->value("user_id") != 1): ?>
					<li><a href="https://wiki.kbhff.dk/tiki-index.php?page=Vagtplaner" class="shift">Ta' en vagt</a></li>
					<? endif; ?>
					<li><a href="/butik" class="order">Bestil en pose</a></li>
					<li><a href="/bliv-medlem" class="member">Bliv medlem</a></li>
				</ul>
			</div>


			<div class="c-box weeklybag">
			<? if($weeklybag_item): ?>
				<h3>Ugens pose - <?= $weeklybag_item["name"] ?></h3>
				<?= $weeklybag_item["html"] ?>

				<p class="readmore"><a href="/ugens-pose/<?= $weeklybag_item["sindex"] ?>">Læs mere om ugens pose</a></p>
			<? else: ?>
				<h3>Ugens pose</h3>
				<p>Ugens pose er endnu ikke oprettet.</p>

				<p class="readmore"><a href="/ugens-pose">Læs mere om ugens pose</a></p>
			<? endif; ?>
			</div>

			<div class="c-box newsletter i:newsletter">
				<h3>Tilmeld Nyhedsbrev</h3>
		
				<form action="//kbhff.us15.list-manage.com/subscribe/post?u=d2a926649ebcf316af87a05bb&amp;id=141ae6f59f" method="post" target="_blank">
					<input type="hidden" name="b_d2a926649ebcf316af87a05bb_141ae6f59f" value="">
					<div class="field email required">
						<label for="input_email">E-mail</label>
						<input type="email" value="" name="EMAIL" id="input_email" />
					</div>

					<ul class="actions">
						<li class="submit"><input type="submit" value="Tilmeld" name="subscribe" class="button" /></li>
					</ul>
				</form>

			</div>
		</div>

	</div>



</div>
