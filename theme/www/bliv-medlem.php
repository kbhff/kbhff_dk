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

	// /bliv-medlem/kvittering
	if($action[0] == "kvittering") {

		$page->page(array(
			"templates" => "signup/kvittering.php"
		));
		exit();
	}

	// /bliv-medlem/addToCart (submitted from /bliv-medlem)
	else if($action[0] == "addToCart" && $page->validateCsrfToken()) {

		// Check if user is already a member
		$user_id = session()->value("user_id");
		if($user_id > 1) {

			$UC = new User();
			$membership = $UC->getMembership();
			if($membership && $membership["subscription_id"]) {

				header("Location: allerede-medlem");
				exit();

			}
		}

		// add membership to new or existing cart
		$cart = $SC->addToCart(array("addToCart"));
		// successful creation
		if($cart) {
			header("Location: tilmelding");
			exit();

		}
		// something went wrong
		else {
			message()->addMessage("Colonizing Mars! Waw. The computer is so excited it cannot process your request right now. Try again later.", array("type" => "error"));
		}

	}

	// /bliv-medlem/save (submitted from /bliv-medlem/tilmelding)
	else if($action[0] == "save" && $page->validateCsrfToken()) {

		// create new user
		$user = $model->newUser(array("newUser"));

		// successful creation
		if(isset($user["user_id"])) {

			$order = $SC->newOrderFromCart(array("newOrderFromCart", $_COOKIE["cart_reference"]));
			if($order) {

				// redirect to leave POST state
				header("Location: /butik/betaling/".$order["order_no"]);
				exit();

			}
			else {

				// redirect to leave POST state
				header("Location: /butik/kurv");
				exit();

			}

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


	// /bliv-medlem/tilmelding
	else if($action[0] == "tilmelding") {

		$page->page(array(
			"templates" => "signup/signup.php"
		));
		exit();

	}
	// /bliv-medlem/allerede-medlem
	else if($action[0] == "allerede-medlem") {

		$page->page(array(
			"templates" => "signup/already-member.php"
		));
		exit();

	}
	// view specific membership
	// /bliv-medlem/medlemsskaber/#sindex#
	else if(count($action) == 2 && $action[0] == "medlemskaber") {

		$page->page(array(
			"templates" => "signup/membership.php"
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
