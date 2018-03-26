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



// news list for tags
// /posts/#sindex#
if(count($action) == 1) {

	$page->page(array(
		"templates" => "pages/post.php"
	));
	exit();

}
// /posts/tag/#tag#
// /posts/tag/#tag#/#sindex#
else if(count($action) >= 2 && $action[0] == "tag") {

	$page->page(array(
		"templates" => "pages/posts_tag.php"
	));
	exit();

}


$page->page(array(
	"templates" => "pages/posts.php"
));
exit();


?>
 