<?php
$access_item["/"] = true;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();


$page->bodyClass("mass_mail");
$page->pageTitle("Massemail");



$page->page(array(
	"templates" => "mass-mail/index.php"
	)
);
exit();


?>
 