<?php
$access_item["/"] = true;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();


$page->bodyClass("purchasing");
$page->pageTitle("Indkøb");



$page->page(array(
	"templates" => "purchasing/index.php"
	)
);
exit();


?>
 