<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();
$model = new User();


$page->bodyClass("signup");
$page->pageTitle("Bliv medlem");


if(is_array($action) && count($action)) {

	// /signup/receipt
	if($action[0] == "receipt") {

		$page->page(array(
			"templates" => "signup/receipt.php"
		));
		exit();
	}

	// /signup/confirm/email|mobile/#email|mobile#/#verification_code#
	else if($action[0] == "confirm" && count($action) == 4) {

		session()->value("signup_type", $action[1]);
		session()->value("signup_username", $action[2]);

		if($model->confirmUser($action)) {

			// redirect to leave POST state
			header("Location: /signup/confirm/receipt");
			exit();

		}
		else {

			// redirect to leave POST state
			header("Location: /signup/confirm/error");
			exit();

		}
		exit();
	}
	else if($action[0] == "confirm" && $action[1] == "receipt") {

		$page->page(array(
			"templates" => "signup/confirmed.php"
		));
		exit();
	}
	else if($action[0] == "confirm" && $action[1] == "error") {

		$page->page(array(
			"templates" => "signup/confirmation_failed.php"
		));
		exit();
	}

	// /signup/save
	else if($action[0] == "save" && $page->validateCsrfToken()) {

		// create new user
		$user = $model->newUser(array("newUser"));

		// successful creation
		if(isset($user["user_id"])) {

			// redirect to leave POST state
			header("Location: receipt");
			exit();

		}

		// user exists
		else if(isset($user["status"]) && $user["status"] == "USER_EXISTS") {
			message()->addMessage("Sorry, the computer says you either have a bad memory or a bad conscience!", array("type" => "error"));
		}
		// something went wrong
		else {
			message()->addMessage("Sorry, computer says no!", array("type" => "error"));
		}

	}

	// post username, maillist_id and verification_token
	else if($action[0] == "unsubscribe" && $page->validateCsrfToken()) {

		// successful creation
		if($model->unsubscribeUserFromMaillist(["unsubscribe", "unsubscribeUserFromMaillist"])) {

			// redirect to leave POST state
			header("Location: /signup/unsubscribed");
			exit();

		}

		$page->page(array(
			"templates" => "signup/unsubscribe.php"
		));
		exit();

	}
	// /signup/unsubscribe/#maillist_id#/#username#/#verification_code#
	else if($action[0] == "unsubscribe") {

		$page->page(array(
			"templates" => "signup/unsubscribe.php"
		));
		exit();

	}
	// /signup/unsubscribed
	else if($action[0] == "unsubscribed") {

		$page->page(array(
			"templates" => "signup/unsubscribed.php"
		));
		exit();

	}

}

// plain signup directly
// /signup
$page->page(array(
	"templates" => "signup/signup.php"
));

?>
