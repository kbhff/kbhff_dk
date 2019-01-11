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



if($action) {


	// /bliv-medlem/addToCart (submitted from /bliv-medlem)
	if($action[0] == "addToCart" && $page->validateCsrfToken()) {

		// Check if user is already a user and then check if user is a member with a subscription
		$user_id = session()->value("user_id");
		if($user_id > 1) {

			$UC = new User();
			$membership = $UC->getMembership();
			// user is a member with a subscription
			if($membership && $membership["subscription_id"]) {
				//redirect to leave POST state
				header("Location: allerede-medlem");
				exit();

			}
		}
		// add membership to new or existing cart
		$cart = $SC->addToCart(array("addToCart"));
		// if successful creation
		if($cart) {
			// redirect to leave POST state
			header("Location: tilmelding");
			exit();

		}
		// something went wrong
		else {
			message()->addMessage("Der skete en fejl! Prøv igen senere.", array("type" => "error"));
		}
	}

	// user is already a member with a subsription (submitted from /bliv-medlem/allerede-medlem)
	else if($action[0] == "allerede-medlem") {

		$page->page(array(
			"templates" => "signup/already-member.php"
		));
		exit();

	}


	// membership was successfully added to cart (submitted from /bliv-medlem/tilmelding)
	else if($action[0] == "tilmelding") {

		$page->page(array(
			"templates" => "signup/signup.php"
		));
		exit();

		}


	// /bliv-medlem/save (submitted from /bliv-medlem/tilmelding)
	else if($action[0] == "save" && $page->validateCsrfToken()) {

		// create new user
		$user = $model->newUser(array("newUser"));

		// if successful creation
		if(isset($user["user_id"])) {

			// redirect to leave POST state
			header("Location: verificer");
			exit();
		}

		// if user exists
		else if(isset($user["status"]) && $user["status"] == "USER_EXISTS") {

		
			message()->addMessage("Det ser ud til at du allerede er registreret som bruger. Prøv at logge ind.", array("type" => "error"));
			
			// redirect to leave POST state
			header("Location: /login");
			exit();
		}
		// something went wrong
		else {
			
			
			message()->addMessage("Der skete en fejl under oprettelsen. Prøv igen.", array("type" => "error"));
			// redirect to leave POST stage
			header("Location: tilmelding");
			exit();
		}

	}

	//bliv-medlem/verificer (submitted from bliv-medlem/save)
	else if($action[0] == "verificer") {

		$page->page(array(
			"templates" => "signup/verify.php"
		));
		exit();

	}
		// bliv-medlem/spring-over (submitted from bliv-medlem/verificer)
	else if($action[0] == "spring-over") {

		// Converts cart to order and updates cookie with cart-reference. 
		$order = $SC->newOrderFromCart(array("newOrderFromCart", $_COOKIE["cart_reference"]));
		
		// if successful order creation
		if($order) {

			// redirect to leave POST state
			header("Location: /butik/betaling/".$order["order_no"]);
			exit();
		}
		// Something went wrong
		else {
			message()->addMessage("Det ser ud til at der er sket en fejl.", array("type" => "error"));

			// redirect to leave POST state
			header("Location: /butik/kurv");
			exit();
		}

	}

	// bliv-medlem/bekraeft (submitted from bliv-medlem/verificer)
	else if($action[0] == "bekraeft") {

		if (count($action) == 1 && $page->validateCsrfToken()) {

			// Check if user is already verified. If not, verify and enable user
			$result = $model->confirmUser($action);

			// user has already been verified
			if($result && isset($result["status"]) && $result["status"] == "USER_VERIFIED") {
				message()->addMessage("Du er allerede verificeret! Prøv at logge ind.", array("type" => "error"));
				
				// redirect to leave POST state
				header("Location: /login");
				exit();
			}

			// code is valid and user is verified and enabled.
			else if($result) {
				// convert cart to order and update cookies with cart-refernce
				$order = $SC->newOrderFromCart(array("newOrderFromCart", $_COOKIE["cart_reference"]));
				// if successful order creation
				if($order) {

					// redirect to leave POST state
					header("Location: /butik/betaling/".$order["order_no"]);
					exit();
				}

				else {
					
					// Something went wrong
					message()->addMessage("Det ser ud til at der er sket en fejl.", array("type" => "error"));
					// redirect to leave POST state
					header("Location: /butik/kurv");
					exit();
				}

			}

			// code is not valid and user is not verified and enabaled.
			else {
				message()->addMessage("Forkert verificeringskode. Prøv igen!", array("type" => "error"));
				// redirect to leave POST state
				header("Location: /bliv-medlem/verificer");
				exit();

			}
		}

		// /bliv-medlem/bekraeft/#email|#verification_code# (submitted from link in email)
		else if(count($action) == 3) {

			// session()->value("signup_type", $action[1]);
			// session()->value("signup_username", $action[2]);

			// Confirm user returns either true, false or an object
			// Check if user is already verified. If not, verify and enable user
			$result = $model->confirmUser($action);

			// user han already been verified
			if($result && isset($result["status"]) && $result["status"] == "USER_VERIFIED") {
				message()->addMessage("Du er allerede verificeret. Pøv at logge ind.", array("type" => "error"));
				
				// redirect to leave POST state
				header("Location: /login");
				exit();
			}

			// code is valid and user is verfified and enabled
			else if($result) {
				// redirect to leave POST state
				header("Location: /bliv-medlem/bekraeft/kvittering");
				exit();

			}
			// code is not valid and user is not verified and enabled.
			else {
				// redirect to leave POST state
				header("Location: /bliv-medlem/bekraeft/fejl");
				exit();
			}
		}
		
		// bliv-medlem/bekreaft/fejl (submitted from bliv-medlem/bekraeft/#email|#verification_code#)
		else if($action[1] == "fejl") {

			$page->page(array(
				"templates" => "signup/confirmation_failed.php"
			));
			exit();
		}

		// bliv/medlem/bekraeft/kvittering (submitted from /bliv-medlem/bekraeft/#email|#verification_code#)
		else if($action[1] == "kvittering") {

			$page->page(array(
				"templates" => "signup/confirmed.php"
			));
			exit();
		}
	}

	// view specific membership
	// /bliv-medlem/medlemskaber/#sindex#
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
