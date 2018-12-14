<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();


$page->bodyClass("local_admin");
$page->pageTitle("Local admin");



$page->page(array(
	"templates" => "local-admin/index.php"
	)
);
exit();


?>
 