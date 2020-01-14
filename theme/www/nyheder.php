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
$page->pageTitle("Nyheder");


# View specific post
# /nyheder/#sindex#
if(count($action) === 1) {

	$page->page(array(
		"templates" => "posts/post.php"
	));
	exit();
}
# View specific post (tag listed)
# /nyheder/tag/#tag#/#sindex#
else if(count($action) === 3 && $action[0] == "tag") {

	$page->page(array(
		"templates" => "posts/post_tag.php"
	));
	exit();
}

# List by tag
# /nyheder/tag/#tag#
# /nyheder/tag/#tag#/page/#sindex#
else if((count($action) === 2 && $action[0] == "tag") || (count($action) === 4 && $action[0] == "tag" && $action[2] == "page")) {

	$page->page(array(
		"templates" => "posts/posts_tag.php"
	));
	exit();

}


// /nyheder
// overview of posts
$page->page(array(
	"templates" => "posts/posts.php"
));
exit();


?>
