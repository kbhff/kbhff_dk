<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();


$page->bodyClass("member_help");
$page->pageTitle("Member help");



$page->page(array(
	"templates" => "member-help/index.php"
	)
);
exit();


?>
 