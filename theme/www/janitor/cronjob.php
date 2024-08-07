<?php
$access_item["/sendRenewalNotices"] = true;
$access_item["/sendOrderingReminders"] = true;
$access_item["/sendPickupReminders"] = true;
$access_item["/sendTallyNotClosedReminders"] = true;
$access_item["/cancelUnpaidOrders"] = true;
$access_item["/sendCancellationWarnings"] = true;
$access_item["/removeExceededDeadlineCartItems"] = true;

$access_item["/cancelUnpaidRenewalOrdersFromLastYear"] = true;

$access_item["/sendDeletionWarningToInactiveUsers"] = true;
$access_item["/deleteInactiveUsers"] = true;

$access_item["/sendCompleteSignupReminder"] = true;
$access_item["/deleteIncompleteSignups"] = true;

if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();

$page->bodyClass("cronjob");
$page->pageTitle("Cronjobs");


if(is_array($action) && count($action)) {

	if(preg_match("/^cancelUnpaidOrders$/", $action[0])) {

		include_once("classes/shop/supershop.class.php");
		$SC = new SuperShop();

		$output = new Output();
		$output->screen($SC->cancelUnpaidOrders($action));
		exit();

	}
	
	else if(preg_match("/^sendCancellationWarnings$/", $action[0])) {

		include_once("classes/shop/supershop.class.php");
		$SC = new SuperShop();

		$output = new Output();
		$output->screen($SC->sendOrderCancellationWarnings($action));
		exit();

	}

	else if(preg_match("/^sendRenewalNotices$/", $action[0])) {

		include_once("classes/shop/supersubscription.class.php");
		$SuperSubscriptionClass = new SuperSubscription();

		$output = new Output();
		$output->screen($SuperSubscriptionClass->sendRenewalNotices($action));
		exit();

	}

	else if(preg_match("/^sendPickupReminders$/", $action[0])) {

		include_once("classes/users/superuser.class.php");
		$UC = new SuperUser();

		$output = new Output();
		$output->screen($UC->sendPickupReminders($action));
		exit();

	}

	else if(preg_match("/^sendOrderingReminders$/", $action[0])) {

		include_once("classes/users/superuser.class.php");
		$UC = new SuperUser();

		$output = new Output();
		$output->screen($UC->sendOrderingReminders($action));
		exit();

	}
	
	else if(preg_match("/^sendTallyNotClosedReminders$/", $action[0])) {

		include_once("classes/shop/tally.class.php");
		$TC = new Tally();

		$output = new Output();
		$output->screen($TC->sendTallyNotClosedReminders($action));
		exit();

	}

	else if(preg_match("/^removeExceededDeadlineCartItems$/", $action[0])) {

		include_once("classes/shop/supershop.class.php");
		$SC = new SuperShop();

		$output = new Output();
		$output->screen($SC->removeExceededDeadlineCartItems($action));
		exit();

	}

	else if(preg_match("/^cancelUnpaidRenewalOrdersFromLastYear$/", $action[0])) {

		include_once("classes/shop/supershop.class.php");
		$SC = new SuperShop();

		$output = new Output();
		$output->screen($SC->cancelUnpaidRenewalOrdersFromLastYear($action));
		exit();

	}

	else if(preg_match("/^sendDeletionWarningToInactiveUsers$/", $action[0])) {

		include_once("classes/users/superuser.class.php");
		$UC = new SuperUser();

		$output = new Output();
		$output->screen($UC->sendDeletionWarningToInactiveUsers($action));
		exit();

	}

	else if(preg_match("/^deleteInactiveUsers$/", $action[0])) {

		include_once("classes/users/superuser.class.php");
		$UC = new SuperUser();

		$output = new Output();
		$output->screen($UC->deleteInactiveUsers($action));
		exit();

	}

	else if(preg_match("/^sendCompleteSignupReminder$/", $action[0])) {

		include_once("classes/users/superuser.class.php");
		$UC = new SuperUser();

		$output = new Output();
		$output->screen($UC->sendCompleteSignupReminder($action));
		exit();

	}

	else if(preg_match("/^deleteIncompleteSignups$/", $action[0])) {

		include_once("classes/users/superuser.class.php");
		$UC = new SuperUser();

		$output = new Output();
		$output->screen($UC->deleteIncompleteSignups($action));
		exit();

	}


}

$page->page(array(
	"templates" => "pages/404.php"
));

?>
