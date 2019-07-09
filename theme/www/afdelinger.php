<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");

// get REST parameters
$action = $page->actions();

// page info
$page->bodyClass("departments");
$page->pageTitle("Afdelinger");


// view specific post 
// /afdelinger/#name#
if(count($action) == 1) {

	$page->page(array(
		"templates" => "departments/departments-view.php"
	));
	exit();

}


// standard template
$page->page(array(
	"templates" => "departments/departments.php"
	)
);
exit();


?>
 