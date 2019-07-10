<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();
$IC = new Items();
$itemtype = "post";


$page->bodyClass("posts");
$page->pageTitle("Artikler");


// /artikler/#sindex#
if(count($action) == 1) {

	$page->page(array(
		"templates" => "posts/post.php"
	));
	exit();

}

// /artikler/tag/#tag#
else if(count($action) >= 2 && $action[0] == "tag") {

	$page->page(array(
		"templates" => "posts/posts_tag.php"
	));
	exit();

}

// /artikler
// overview of posts
$page->page(array(
	"templates" => "posts/posts.php"
));
exit();


?>
