<?php
$IC = new Items();
global $action;

$item = $IC->getItem(array("tags" => "page:about", "status" => 1, "extend" => array("tags" => true, "user" => true, "mediae" => true, "comments" => true, "readstate" => true)));
if($item) {
	$this->sharingMetaData($item);
}

$about_subnavigation = $this->navigation("sub-about");

?>

<div class="scene about i:scene">

	<div class="banner i:banner variant:random format:jpg"></div>

	<? if($about_subnavigation && isset($about_subnavigation["nodes"])) { ?>
	<ul class="subnavigation">
		<? foreach($about_subnavigation["nodes"] as $node): ?>
		<li><a href="<?= $node["link"] ?>"><?= $node["name"] ?></a></li>
		<? endforeach;?>
	</ul>
	<? } ?>


	<div class="c-wrapper">

		<div class="c-two-thirds">

		<?	if($item):
			$media = $IC->sliceMediae($item, "single_media"); ?>

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

		</div>	


		<div class="c-one-third">

			<div class="c-box actions">
				<h3>Genveje</h3>
				<ul class="actions">
					<li><a href="http://kbhffwiki.org/tiki-index.php?page=Vagtplaner" class="shift">Ta' en vagt</a></li>
					<li><a href="/butik" class="order">Bestil en pose</a></li>
					<li><a href="/bliv-medlem" class="member">Bliv medlem</a></li>
				</ul>
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
