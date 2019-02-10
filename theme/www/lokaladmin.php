<?php


$access_item["/"] = true;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


// get REST parameters
$action = $page->actions();


// page info
$page->bodyClass("local_admin");
$page->pageTitle("Local admin");


// standard template
$page->page(array(
	"templates" => "local-admin/index.php"
	)
);
exit();


?>
 