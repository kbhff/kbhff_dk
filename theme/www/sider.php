<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();
$IC = new Items();
$itemtype = "page";


$page->bodyClass("posts");
$page->pageTitle("Sider");


// /sider/#sindex#
if(count($action) == 1) {

	$page->page(array(
		"templates" => "pages/view.php"
	));
	exit();

}

// /sider
$page->page(array(
	"templates" => "pages/404.php"
));
exit();


?>
