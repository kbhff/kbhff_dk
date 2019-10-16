<?php
global $action;
global $IC;
global $itemtype;

$items = $IC->getItems(array("itemtype" => $itemtype, "order" => "position DESC", "extend" => array("tags" => true)));



?>

<div class="scene faq i:faq">
	<div class="banner i:banner variant:random format:jpg"></div>

	<h1>Spørgsmål og svar</h1>

	<? if($items): ?>
		<ul class="items questions">
			<? foreach($items as $item): ?>
			<li class="item question id:<?= $item["item_id"] ?>" itemscope itemtype="http://schema.org/Article">

				<h2 itemprop="headline"><a href="/faq/<?= $item["sindex"] ?>"><?= $item["question"] ?></a></h2>

				<?= $HTML->articleInfo($item, "/faq/".$item["sindex"]) ?>

			</li>
			<? endforeach; ?>
		</ul>

	<? else: ?>
		<p>Ingen spørgsmål og svar</p>
	<? endif; ?>

</div>
