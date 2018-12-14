<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();


$page->bodyClass("shop_shift");
$page->pageTitle("Butiksvagt");



$page->page(array(
	"templates" => "shop-shift/index.php"
	)
);
exit();


?>
 