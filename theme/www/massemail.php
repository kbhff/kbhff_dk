<?php


$access_item["/"] = true;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");

// get REST parameters
$action = $page->actions();

// page info
$page->bodyClass("mass_mail");
$page->pageTitle("Massemail");


// standard template
$page->page(array(
	"templates" => "mass-mail/index.php"
	)
);
exit();


?>
 