<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();


$page->bodyClass("webshop");
$page->pageTitle("Grøntshoppen");



$page->page(array(
	"templates" => "webshop/index.php"
	)
);
exit();


?>
 