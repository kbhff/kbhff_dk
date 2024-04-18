<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();
$SC = new Shop();
$model = new User();
$MC = new Member();

$page->bodyClass("signup");
$page->pageTitle("Bliv medlem");



// test if clearing cart cookie is necessary
// (If user has cart_reference cookie from other user session and tries to signup new user
// then session is redirected to login. On shop/checkout link to signup will add clear_cookie param to avoid endless loop)
$clear_cookie = $_GET["clear_cookie"];
if($clear_cookie) {
	// Clear cart reference from session
	session()->reset("cart_reference");

	// Delete cart reference cookie
	setcookie("cart_reference", "", time() - 3600, "/");
}



if($action) {
	
	# /bliv-medlem/addToCart
	if($action[0] == "addToCart" && security()->validateCsrfToken()) {

		// user is already a user
		$user_id = session()->value("user_id");
		if($user_id > 1) {

			// user is a member with a subscription
			$membership = $MC->getMembership();
			if($membership && $membership["subscription_id"]) {

				//redirect to leave POST state
				header("Location: allerede-medlem");
				exit();

			}
		}
		// add signupfee to new or existing cart
		$cart = $SC->addToCart(array("addToCart"));
		// if successful creation
		if($cart) {

			if($cart["user_id"]) {
				// redirect to leave POST state
				header("Location: /butik/betal");
			}
			else {
				// redirect to leave POST state
				header("Location: tilmelding");
			}
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
	
	# /bliv-medlem/tilmelding
	else if($action[0] == "tilmelding") {

		$user_id = session()->value("user_id");
		if($user_id > 1) {

			// check if user is already a member
			$membership = $MC->getMembership();
			if($membership && $membership["subscription_id"]) {

				header("Location: allerede-medlem");
				exit();

			}

		}

		$cart = $SC->getCart();

		// cart already has user, redirect to shop checkout
		if($cart && $cart["user_id"]) {
			header("Location: /butik/betal");
			exit();
		}

		$page->page(array(
			"templates" => "signup/signup.php"
		));
		exit();

	}

	// user is already a member with a subscription 
	# /bliv-medlem/allerece-medlem
	else if($action[0] == "allerede-medlem") {

		$page->page(array(
			"templates" => "signup/already-member.php"
		));
		exit();

	}

	elseif($action[0] == "resetSessionBeforeSignup") {

		session()->reset();

		header("Location: /bliv-medlem/");
		exit();

	}

	# /bliv-medlem/save
	else if($action[0] == "save" && security()->validateCsrfToken()) {

			
		// create new user
		$user = $model->newUser(array("newUser"));

		// if successful creation
		if(isset($user["user_id"])) {

			if(getPost("maillist")) {
				$model->addToMailchimp([
					"email_address" => $user["email"],
					"status" => "pending"
				]);
			}

			// redirect to leave POST state
			header("Location: verificer");
			exit();
			
		}

		// if user exists
		else if(isset($user["status"]) && $user["status"] == "USER_EXISTS") {
			
			if($SC->deleteSignupfeesAndMembershipsFromCart()) {
				message()->addMessage("Det ser ud til at du allerede er registreret som bruger. Prøv at logge ind.", array("type" => "error"));
				// redirect to leave POST state
				header("Location: /login");
				exit(); 
			}
			// if($SC->deleteItemtypeFromCart(array("signupfee", $_COOKIE["cart_reference"]))) {
			// 
			// }	
		}
		// something went wrong
		else {
			
			
			message()->addMessage("Der skete en fejl under oprettelsen. Prøv igen.", array("type" => "error"));
			// redirect to leave POST stage
			header("Location: tilmelding");
			exit();
		}

	}

	# /bliv-medlem/verificer
	else if($action[0] == "verificer") {

		$page->page(array(
			"templates" => "signup/verify.php"
		));
		exit();

	}
	
	# /bliv-medlem/spring-over 
	else if($action[0] == "spring-over") {

		// redirect to leave POST state
		header("Location: /butik/betal");
		exit();
	}

	# /bliv-medlem/bekraeft
	else if($action[0] == "bekraeft") {

		if (count($action) == 1 && security()->validateCsrfToken()) {

			$username = session()->value("signup_email");
			$verification_code = getPost("verification_code");

			// Check if user is already verified. If not, verify and enable user
			$result = $model->confirmUsername($username, $verification_code);

			// user has already been verified
			if($result && isset($result["status"]) && $result["status"] == "USER_VERIFIED") {
				
				if(session()->value("user_id") > 1) {
					message()->addMessage("Du er allerede verificeret!");
					header("Location: /profil");
					exit();
				}
				else {
					message()->addMessage("Du er allerede verificeret! Prøv at logge ind", array("type" => "error"));
					// redirect to leave POST state
					header("Location: /login");
					exit();
				}
				
			}

			// verification code is valid
			else if($result) {	

				// check if there is a cart
				$cart = $SC->getCart();

				// cart exists
				if($cart) {
					
					// redirect to leave POST state
					// to checkout and confirm order
					message()->addMessage("Dit brugernavn er verificeret.", array("type" => "message"));
					header("Location: /butik/betal");
					exit();
					
				}
				// no cart - go to cart
				else {
					message()->addMessage("Dit brugernavn er verificeret", array("type" => "message"));
					header("Location: /butik/kurv");
					exit();
				}
			
			}

			// invalid verification code
			else {
				message()->addMessage("Forkert verificeringskode. Prøv igen!", array("type" => "error"));
				// redirect to leave POST state
				header("Location: /bliv-medlem/verificer");
				exit();

			}
		}

		else if(count($action) == 2) {

			# /bliv-medlem/bekraeft/fejl 
			if($action[1] == "fejl") {
	
				$page->page(array(
					"templates" => "signup/confirmation_failed.php"
				));
				exit();
			}
	
			// /bliv/medlem/bekraeft/kvittering
			else if($action[1] == "kvittering") {
	
				$page->page(array(
					"templates" => "signup/confirmed.php"
				));
				exit();
			}
		}

		# /bliv-medlem/bekraeft/#email/#verification_code# (submitted from link in email)
		else if(count($action) == 3) {
			
			$username = $action[1];
			$verification_code = $action[2];
			
			// Infer user_id from username 
			$user_id = $model->getLoginUserId($username);
			
			if($user_id) {			

				session()->value("temp-username", $username);				
				$has_password = $model->loginUserHasPassword($user_id);


				$result = $model->confirmUsername($username, $verification_code);
				
				// user is already verified
				if($result && isset($result["status"]) && $result["status"] == "USER_VERIFIED") {
					
					// user has password
					if($has_password) {
						
						if(session()->value("user_id") > 1) {
							
							message()->addMessage("Du er allerede verificeret!");
							header("Location: /profil");
							exit();
						}
						else {
							message()->addMessage("Du er allerede verificeret! Prøv at logge ind", array("type" => "error"));
							// redirect to leave POST state
							header("Location: /login");
							exit();
						}
						
					}

					// user has no password
					else {

						// redirect to leave POST state
						header("Location: /login/opret-password");
						exit();

					}
				}

				// verification code is valid -> receipt
				else if($result) {
				
					// user has password
					if($has_password) {
						
						message()->addMessage("Du har verificeret din e-mailadresse.");

						// redirect to leave POST state
						header("Location: /bliv-medlem/bekraeft/kvittering");
						exit();
							
					} 
					// user has no password
					else {
						
					
	
						// redirect to leave POST state
						header("Location: /login/opret-password");
						exit();
					}
					
				
				

				}

				// verification code is not valid -> error
				else {
					// redirect to leave POST state
					header("Location: /bliv-medlem/bekraeft/fejl");
					exit();
				}

				

			}

			// no such user
			else {
				message()->addMessage("Bruger eksisterer ikke.", array("type" => "error"));
				
				// redirect to leave POST state
				header("Location: /bliv-medlem/bekraeft/fejl");
				exit();
			}



			


		}

		else {
			// /verificer
			header("Location: /bliv-medlem/verificer");
			exit();
		}

	}

	// view specific membership
	# /bliv-medlem/medlemskaber/#sindex#
	else if(count($action) == 2 && $action[0] == "medlemskaber") {

		$page->page(array(
			"templates" => "signup/membership.php"
		));
		exit();
	}
}

// plain signup directly
# /bliv-medlem
$page->page(array(
	"templates" => "signup/signupfees.php"
));

?>
