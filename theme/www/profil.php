<?php
$access_item["/"] = true;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();
$IC = new Items();
$UC = new User();


$page->bodyClass("profil");
$page->pageTitle("Min side");


// Allow accept terms
if(is_array($action) && count($action) == 1 && $action[0] == "accept" && $page->validateCsrfToken()) {

	$UC->acceptedTerms();

}
// User must always accept terms - force dialogue if user has not accepted the terms
else if(!$UC->hasAcceptedTerms()) {

	$page->page(array(
		"templates" => "profile/accept_terms.php"
	));
	exit();

}



if(is_array($action) && count($action)) {

	if($action[0] == "update" && $page->validateCsrfToken()) {
		$UC->update();
	}

}


// Fallback
$page->page(array(
	"templates" => "profile/index.php"
));
exit();


?>
 