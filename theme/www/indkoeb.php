<?php

// enable access control 
$access_item["/"] = true;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");

// get REST parameters
$action = $page->actions();

// page info
$page->bodyClass("purchasing");
$page->pageTitle("Indkøb");


// standard template
$page->page(array(
	"templates" => "purchasing/index.php"
	)
);
exit();


?>
 