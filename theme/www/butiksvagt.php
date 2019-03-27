<?php


$access_item["/"] = true;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");

// get REST parameters
$action = $page->actions();

// page info
$page->bodyClass("shop_shift");
$page->pageTitle("Butiksvagt");


// standard template
$page->page(array(
	"templates" => "shop-shift/index.php",
	"type" => "admin"
));
exit();


?>
 