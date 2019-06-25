<?php
$access_item["/"] = false;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");

// get REST parameters
$action = $page->actions();

// page info
$page->bodyClass("departments");
$page->pageTitle("Afdelinger");

// standard template
$page->page(array(
	"templates" => "pages/departments.php"
	)
);
exit();


?>
 