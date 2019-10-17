<?php
$IC = new Items();
global $action;
$sindex = $action[0];


$item = $IC->getItem(array("sindex" => $sindex, "extend" => true));
if($item) {
	$this->sharingMetaData($item);
}


// get post tags for listing
$weeklybags = $IC->getItems(array("itemtype" => "weeklybag", "status" => 1, "limit" => 10, "extend" => true));
$WBC = $IC->typeObject("weeklybag");

?>

<div class="scene weeklybag i:scene">
	<div class="banner i:banner variant:random format:jpg"></div>

	<div class="c-wrapper">

		<div class="c-two-thirds">

			<?	if($item): ?>

			<div class="article i:article id:<?= $item["item_id"] ?>" itemscope itemtype="http://schema.org/Article">


				<h1 itemprop="headline">Ugens pose: <?= $item["name"] ?></h1>

				<div class="articlebody" itemprop="articleBody">
					<?= $item["full_description"]?>
				</div>

			</div>

		</div>

		<div class="c-one-third">

			<div class="c-box weeklybag">
			<? if($weeklybags): ?>
				<ul>
					<? foreach($weeklybags as $weeklybag): ?>
						<li>
							<h3><a href="/ugens-pose/<?= $weeklybag["sindex"] ?>"><?= $weeklybag["name"] ?></a></h3>
							<p><?= $WBC->getWeeklyBagDate($weeklybag["week"], $weeklybag["year"]) ?></p>
						</li>

					<? endforeach; ?>
				</ul>
			<? endif; ?>
			</div>

			<div class="c-box recipes">
				<h3>Opskrifter</h3>
				<p>Find opskrifter på <a href="http://opskrifter.kbhff.dk/" target="_blank">Kbhff's helt egen opskriftsside</a>.</p>
			</div>

		</div>


	</div>

<? else: ?>


	<h1>Hov!</h1>
	<h2>Der skete en fejl.</h2>
	<p>Vi kunne ikke finde den ønskede side.</p>


<? endif; ?>


</div>