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
$page->pageTitle("Posts");



// view specific post 
// /posts/#sindex# (submitted from /posts)
if(count($action) == 1) {

	$page->page(array(
		"templates" => "pages/post.php"
	));
	exit();

}
// view specific post in a two column grid-layout
// /posts/post-grid/#sindex#
else if(count($action) == 2 && $action[0] == "post-grid") {

	$page->page(array(
		"templates" => "pages/post_grid.php"
	));
	exit();

}



// /posts/tag/#tag#
// /posts/tag/#tag#/#sindex#
// overview of tag-specific posts when clicking on relevant tag 
else if(count($action) >= 2 && $action[0] == "tag") {

	$page->page(array(
		"templates" => "pages/posts_tag.php"
	));
	exit();

}

// /posts
// overview of posts
$page->page(array(
	"templates" => "pages/posts.php"
));
exit();


?>
