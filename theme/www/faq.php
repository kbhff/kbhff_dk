<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();
$IC = new Items();
$itemtype = "qna";


$page->bodyClass("faq");
$page->pageTitle("Spørgsmål og svar");


// /faq/#sindex#
if(count($action) == 1) {

	$page->page(array(
		"templates" => "faq/view.php"
	));
	exit();

}

// /faq
$page->page(array(
	"templates" => "faq/list.php"
));
exit();


?>
