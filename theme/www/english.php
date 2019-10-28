<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();
$IC = new Items();
$itemtype = "page";


$page->bodyClass("english");
$page->pageTitle("English");


// /english/#sindex#
if(count($action) == 1) {

	$page->page(array(
		"templates" => "pages/view.php"
	));
	exit();

}

// /english
$page->page(array(
	"templates" => "pages/english.php"
));
exit();


?>
