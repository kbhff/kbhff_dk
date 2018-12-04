<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();
$SC = new Shop();
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


	else if($action[0] == "addToCart" && $page->validateCsrfToken()) {

		// Check if user is already a member
		$user_id = session()->value("user_id");
		if($user_id > 1) {

			$UC = new User();
			$membership = $UC->getMembership();
			if($membership && $membership["subscription_id"]) {

				header("Location: already-member");
				exit();

			}
		}

		// add membership to new or existing cart
		$cart = $SC->addToCart(array("addToCart"));
		// successful creation
		if($cart) {
			header("Location: signup");
			exit();

		}
		// something went wrong
		else {
			message()->addMessage("Colonizing Mars! Waw. The computer is so excited it cannot process your request right now. Try again later.", array("type" => "error"));
		}

	}



	// THIS SECTION HAS NOT BEEN UPDATED YET
	// START OLD SECTION



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

	$UC = new User();
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



	// END OLD SECTION
	// BELOW THIS LINE IS NEW STUFF



	// /bliv-medlem/signup
	else if($action[0] == "signup") {

		$page->page(array(
			"templates" => "signup/signup.php"
		));
		exit();

	}

}

// plain signup directly
// /bliv-medlem
$page->page(array(
	"templates" => "signup/signupfees.php"
));

?>
