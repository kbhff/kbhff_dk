<?php
global $IC;
global $action;
global $itemtype;

$sindex = $action[0];


$item = $IC->getItem(array("sindex" => $sindex, "extend" => true));
if($item) {
	$this->sharingMetaData($item);
}

$items = $IC->getItems(array("itemtype" => $itemtype, "order" => "position DESC", "extend" => array("tags" => true)));

?>

<div class="scene faq i:faq">
	<div class="banner i:banner variant:random format:jpg"></div>


<?	if($item): ?>

	<div class="article i:article id:<?= $item["item_id"] ?>" itemscope itemtype="http://schema.org/Article">

		<h1 itemprop="headline"><?= $item["question"] ?></h1>

		<div class="articlebody" itemprop="articleBody">
			<?= $item["answer"]?>
		</div>

	</div>



<? else: ?>


	<h1>Hov!</h1>
	<h2>Der skete en fejl.</h2>
	<p>Vi kunne ikke finde den ønskede side.</p>


<? endif; ?>


	<? if($items): ?>
	<h2>Flere spørgsmål og svar</h2>
	<ul class="items questions">
		<? foreach($items as $item): ?>
		<li class="item question id:<?= $item["item_id"] ?>" itemscope itemtype="http://schema.org/Article">

			<h2 itemprop="headline"><a href="/faq/<?= $item["sindex"] ?>"><?= $item["question"] ?></a></h2>

			<?= $HTML->articleInfo($item, "/faq/".$item["sindex"]) ?>

		</li>
		<? endforeach; ?>
	</ul>
	<? endif; ?>



</div>
