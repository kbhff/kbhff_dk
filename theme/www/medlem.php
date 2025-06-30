<?php
$access_item["/"] = false;
$access_item["/tag-en-vagt"] = true;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();
$IC = new Items();
$itemtype = "page";


$page->bodyClass("member");
$page->pageTitle("Medlem");


// /sider/#sindex#
if(count($action) == 1) {

	$page->page(array(
		"templates" => "pages/view.php"
	));
	exit();

}

// /sider
$page->page(array(
	"templates" => "pages/member.php"
));
exit();


?>
