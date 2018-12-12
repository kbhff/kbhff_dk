<?php
global $IC;
global $action;
global $itemtype;

$sindex = $action[1];
$item = $IC->getItem(array("sindex" => $sindex, "extend" => array("tags" => true, "user" => true, "mediae" => true, "comments" => true, "readstate" => true)));
if($item) {
	$this->sharingMetaData($item);
}

?>

<div class="scene post i:generic">


			<? if($item):
				$media = $IC->sliceMedia($item); ?>

				<div class="article i:article id:<?= $item["item_id"] ?>" itemscope itemtype="http://schema.org/NewsArticle">

					<? if($media): ?>
					<div class="image item_id:<?= $item["item_id"] ?> format:<?= $media["format"] ?> variant:<?= $media["variant"] ?>">
						<p>Image: <a href="/images/<?= $item["item_id"] ?>/<?= $media["variant"] ?>/500x.<?= $media["format"] ?>"><?= $media["name"] ?></a></p>
					</div>
					<? endif; ?>


					<ul class="tags">
						<li><a href="/posts">Posts</a></li>
						<? if($item["tags"]): ?>
							<? foreach($item["tags"] as $item_tag): ?>
								<? if($item_tag["context"] == $itemtype): ?>
						<li><a href="/posts/tag/<?= urlencode($item_tag["value"]) ?>" itemprop="articleSection"><?= $item_tag["value"] ?></a></li>
								<? endif; ?>
							<? endforeach; ?>
						<? endif; ?>
					</ul>

	<div class="c-wrapper">
					<h1 itemprop="headline"><?= $item["name"] ?></h1>
					<div class="c-one-half">

					<ul class="info">
						<li class="published_at" itemprop="datePublished" content="<?= date("Y-m-d", strtotime($item["published_at"])) ?>"><?= date("Y-m-d, H:i", strtotime($item["published_at"])) ?></li>
						<li class="modified_at" itemprop="dateModified" content="<?= date("Y-m-d", strtotime($item["modified_at"])) ?>"></li>
						<li class="author" itemprop="author"><?= $item["user_nickname"] ?></li>
						<li class="main_entity share" itemprop="mainEntityOfPage" content="<?= SITE_URL ?>/posts/<?= $item["sindex"] ?>"></li>
						<li class="publisher" itemprop="publisher" itemscope itemtype="https://schema.org/Organization">
							<ul class="publisher_info">
								<li class="name" itemprop="name" content="parentnode.dk"></li>
								<li class="logo" itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
									<span class="image_url" itemprop="url" content="<?= SITE_URL ?>/img/logo-large.png"></span>
									<span class="image_width" itemprop="width" content="720"></span>
									<span class="image_height" itemprop="height" content="405"></span>
								</li>
							</ul>
						</li>
						<li class="image_info" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
							<span class="image_url" itemprop="url" content="<?= SITE_URL ?>/img/logo-large.png"></span>
							<span class="image_width" itemprop="width" content="720"></span>
							<span class="image_height" itemprop="height" content="405"></span>
						</li>
					</ul>


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



			<? else: ?>


				<h1>Technology clearly doesn't solve everything on it's own.</h1>
				<h2>Technology needs humanity.</h2>
				<p>We could not find the specified post.</p>


			<? endif; ?>

		
			<div class="c-one-half">
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
					tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
					quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
					Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore
					eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident,
					sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
			</div>
		</div>
	</div>
</div>
