<?php
$IC = new Items();
global $action;
$sindex = $action[0];


$item = $IC->getItem(array("sindex" => $sindex, "itemtype" => "page", "status" => 1, "extend" => array("tags" => true, "user" => true, "mediae" => true, "comments" => true,)));
if($item) {
	$this->sharingMetaData($item);
}


?>

<div class="scene page i:scene">
	<div class="banner i:banner variant:random format:jpg"></div>

	<div class="c-wrapper">

		<div class="c-two-thirds">

		<?	if($item):
			$media = $IC->sliceMediae($item, "single_media"); ?>

			<div class="article i:article id:<?= $item["item_id"] ?>" itemscope itemtype="http://schema.org/Article">

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
				<? if(session()->value("user_id") != 1): ?>
					<li class="shift"><a href="/medlem/tag-en-vagt" class="button primary">Ta' en vagt</a></li>
					<li class="order"><a href="/butik" class="button primary">Bestil en pose</a></li>
				<? else: ?>
					<li class="login"><a href="/login" class="button primary">Login</a></li>
					<li class="member"><a href="/bliv-medlem" class="button primary">Bliv medlem</a></li>
				<? endif; ?>
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
