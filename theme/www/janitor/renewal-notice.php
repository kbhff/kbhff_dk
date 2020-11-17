<?php
$access_item["/sendRenewalNotices"] = true;

if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();

include_once("classes/shop/supersubscription.class.php");
$model = new SuperSubscription();

$page->bodyClass("renewal_notice");
$page->pageTitle("Renewal notifications");


if(is_array($action) && count($action)) {

	// Class interface
	if(preg_match("/^sendRenewalNotices$/", $action[0])) {

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
