<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();
$IC = new Items();
$itemtype = "weeklybag";


$page->bodyClass("weeklybag");
$page->pageTitle("Ugens pose");


// /sider/#sindex#
if(count($action) == 1) {

	$page->page(array(
		"templates" => "weeklybag/view.php"
	));
	exit();

}

// /sider
$page->page(array(
	"templates" => "weeklybag/list.php"
));
exit();


?>
