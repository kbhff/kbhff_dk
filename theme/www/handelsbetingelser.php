<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();


$page->bodyClass("businessterms");
$page->pageTitle("Handelsbetingelser");

// /persondata
// show business terms directly 
$page->page(array(
	"templates" => "pages/business_terms.php",
));
exit();


?>
