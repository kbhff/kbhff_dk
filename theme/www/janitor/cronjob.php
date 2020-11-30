<?php
$access_item["/sendRenewalNotices"] = true;
$access_item["/cancelUnpaidOrders"] = true;

if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();

$page->bodyClass("cronjob");
$page->pageTitle("Cronjobs");


if(is_array($action) && count($action)) {

	// Class interface
	if(preg_match("/^cancelUnpaidOrders$/", $action[0])) {

		include_once("classes/shop/supershop.class.php");
		$SC = new SuperShop();

		$output = new Output();
		$output->screen($SC->cancelUnpaidOrders($action));
		exit();

	}

	// Class interface
	else if(preg_match("/^sendRenewalNotices$/", $action[0])) {

		include_once("classes/shop/supersubscription.class.php");
		$SuperSubscriptionClass = new SuperSubscription();

		$output = new Output();
		$output->screen($SuperSubscriptionClass->sendRenewalNotices($action));
		exit();

	}

}

$page->page(array(
	"templates" => "pages/404.php"
));

?>
