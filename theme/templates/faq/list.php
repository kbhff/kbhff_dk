<?php
global $action;
global $IC;
global $itemtype;

include_once("classes/items/taglist.class.php");
$TC = new Taglist();

$taglist = $TC->getTaglist(["handle" => "faq"]);
$categories = $taglist["tags"];
// debug([$taglist, $categories]);


?>

<div class="scene faq i:faq">
	<div class="banner i:banner variant:random format:jpg"></div>

	<h1>Spørgsmål og svar</h1>


<? foreach($categories as $category): ?>
	<h2><?= $category["value"] ?></h2>
	<? 
	$items = $IC->getItems(array("itemtype" => $itemtype, "status" => 1, "tags" => "faq:".$category["value"], "order" => "position ASC", "extend" => true));

	if($items): ?>
	<ul class="items questions">
		<? foreach($items as $item): ?>
		<li class="item question id:<?= $item["item_id"] ?>" itemscope itemtype="http://schema.org/Article">

			<h3 itemprop="headline"><a href="/faq/<?= $item["sindex"] ?>"><?= $item["question"] ?></a></h3>

			<?= $HTML->articleInfo($item, "/faq/".$item["sindex"]) ?>

		</li>
		<? endforeach; ?>
	</ul>

	<? else: ?>
	<p>Ingen spørgsmål og svar</p>
	<? endif; ?>
<? endforeach ?>
</div>
