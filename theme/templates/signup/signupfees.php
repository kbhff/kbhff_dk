<?php
global $action;
global $model;


$IC = new Items();
$page_item = $IC->getItem(array("tags" => "page:signupfees", "extend" => array("user" => true, "tags" => true, "mediae" => true)));
if($page_item) {
	$this->sharingMetaData($page_item);
}
$signupfees = $IC->getItems(array("itemtype" => "signupfee", "order" => "position ASC", "status" => 1, "extend" => array("prices" => true)));

?>

<div class="scene signupfees i:signupfees">

	<!-- <img class="fit-width" src="/img/deprecated/banner.jpg"	/> -->

	<div class="section signupfees">


		<? if($page_item && $page_item["status"]):
			$media = $IC->sliceMedia($page_item); ?>
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
			<h1>Vælg medlemsskab</h1>
		<? endif; ?>

		<? if($signupfees): ?>

			<div class="signupfees">


				<?= $HTML->serverMessages() ?>

				<ul class="signupfees">
					<? foreach($signupfees as $signupfee): ?>
					<li class="signupfee"<?= $signupfee["classname"] ? " ".$signupfee["classname"] : "" ?> itemprop="offers">
						<h3><?= $signupfee["name"] ?></h3>

					<? $membership_item = $IC->getItem(array("id" => $signupfee["associated_membership_id"], "extend" => array("prices" => true))); ?>


					<ul class="offer" itemscope itemtype="http://schema.org/Offer">
						<li class="name" itemprop="name" content="<?= $signupfee["name"] ?>"></li>
						<li class="currency" itemprop="priceCurrency" content="<?= $this->currency() ?>"></li>
						<li class="suscribtion_price"><p>Indmeldelsesgebyr:</p></li>
						<? if($signupfee["prices"]) {
								$offer_key = arrayKeyValue($signupfee["prices"], "type", "offer");
								$default_key = arrayKeyValue($signupfee["prices"], "type", "default");

								if($offer_key !== false) { ?>
									<li class="price default"><?= formatPrice($signupfee["prices"][$default_key]).(isset($signupfee["subscription_method"]) && $signupfee["subscription_method"] && $signupfee["prices"][$default_key]["price"] ? ' / '.$signupfee["subscription_method"]["name"] : '') ?></li>
									<li class="price offer" itemprop="price" content="<?= $signupfee["prices"][$offer_key]["price"]?>"><?= formatPrice($signupfee["prices"][$offer_key]).(isset($signupfee["subscription_method"]) && $signupfee["subscription_method"] && $signupfee["prices"][$default_key]["price"] ? ' / '.$signupfee["subscription_method"]["name"] : '') ?></li>
<?
								}
								else if($signupfee["prices"][$default_key]["price"]) { ?>
									<li class="price" itemprop="price" content="<?= $signupfee["prices"][$default_key]["price"]?>"><?= formatPrice($signupfee["prices"][$default_key]).(isset($signupfee["subscription_method"]) && $signupfee["subscription_method"] && $signupfee["prices"][$default_key]["price"] ? ' / '.$signupfee["subscription_method"]["name"] : '') ?></li>
<?
								}
								else { ?>
									<li class="price" itemprop="price" content="<?= $signupfee["prices"][$default_key]["price"] ?>">Free</li>
<?
								}
?>
							<li class="url" itemprop="url" content="<?$url?>"></li>
<?
 							}


						if($signupfee["html"]) { ?>
							<li class="description" itemprop="description"><?=$signupfee["html"]?></li>
<? 							}

?>
					</ul>




						<ul class="offer" itemscope itemtype="http://schema.org/Offer">
							<li class="name" itemprop="name" content="<?= $membership_item["name"] ?>"></li>
							<li class="currency" itemprop="priceCurrency" content="<?= $this->currency() ?>"></li>
							<li class="suscribtion_price"><p>Årligt kontingent:</p></li>
							<? if($membership_item["prices"]) {
									$offer_key = arrayKeyValue($membership_item["prices"], "type", "offer");
									$default_key = arrayKeyValue($membership_item["prices"], "type", "default");

									if($offer_key !== false) { ?>
										<li class="price default"><?= formatPrice($membership_item["prices"][$default_key]).(isset($membership_item["subscription_method"]) && $membership_item["subscription_method"] && $membership_item["prices"][$default_key]["price"] ? ' / '.$membership_item["subscription_method"]["name"] : '') ?></li>
										<li class="price offer" itemprop="price" content="<?= $membership_item["prices"][$offer_key]["price"]?>"><?= formatPrice($membership_item["prices"][$offer_key]).(isset($membership_item["subscription_method"]) && $membership_item["subscription_method"] && $membership_item["prices"][$default_key]["price"] ? ' / '.$membership_item["subscription_method"]["name"] : '') ?></li>
<?
									}
									else if($membership_item["prices"][$default_key]["price"]) { ?>
										<li class="price" itemprop="price" content="<?= $membership_item["prices"][$default_key]["price"]?>"><?= formatPrice($membership_item["prices"][$default_key]).(isset($membership_item["subscription_method"]) && $membership_item["subscription_method"] && $membership_item["prices"][$default_key]["price"] ? ' / '.$membership_item["subscription_method"]["name"] : '') ?></li>
<?
									}
									else { ?>
										<li class="price" itemprop="price" content="<?= $membership_item["prices"][$default_key]["price"] ?>">Free</li>
<?
									}
?>
								<li class="url" itemprop="url" content="<?$url?>"></li>
<?
									}
?>
								
						</ul>


						<?= $model->formStart("/bliv-medlem/addToCart", array("class" => "signup labelstyle:inject")) ?>
							<?= $model->input("quantity", array("value" => 1, "type" => "hidden")); ?>
							<?= $model->input("item_id", array("value" => $signupfee["item_id"], "type" => "hidden")); ?>

							<ul class="actions">
								<?= $model->link("Læs mere her", "/bliv-medlem/".$signupfee["sindex"], array("wrapper" => "li.readmore")) ?>
								<?= $model->submit("Tilmeld", array("class" => "primary", "wrapper" => "li.signup")) ?>
							</ul>
						<?= $model->formEnd() ?>

					</li>
					<? endforeach; ?>
				</ul>
			</div>

		<? endif; ?>
		</div>
	</div>
