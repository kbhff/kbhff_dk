<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();
$model = new User();


$page->bodyClass("login");
$page->pageTitle("Login");


if($action) {


	// LOGIN

	// login
	if(count($action) == 1 && $action[0] == "login" && security()->validateCsrfToken()) {
		session()->reset("temp-username");

		$username = getPost("username");


		// check if user exists
		if(!$model->userExists(["email"=>$username, "mobile"=>$username, "member_no"=>$username])) {
			message()->addMessage("Det indtastede brugernavn findes ikke i vores system.", ["type"=>"error"]);
			header("Location: /login");
			exit();
		}

		// Perform Janitor login and redirect to /profil, if no other redirect location is found
		if(!session()->value("login_forward")) {

			session()->value("login_forward", "/profil");
		}
		
		// $login_status = $page->login();
		$login_status = security()->login();


		// remove any English messages added by Janitor backend
		message()->resetMessages();


		// User could not log in because user is not verified
		if ($login_status && isset($login_status["status"]) && $login_status["status"] == "NOT_VERIFIED") {

			session()->value("temp-username", $login_status["email"]);
			header("Location: /login/bekraeft-konto");
			exit();

		}
		// User could not log in because user has no password
		else if ($login_status && isset($login_status["status"]) && $login_status["status"] == "NO_PASSWORD") {
			message()->addMessage("Du kunne ikke logge ind, fordi du ikke har oprettet en adgangskode. Prøv at klikke på 'Har du glemt din adgangskode?' nedenfor for at oprette en ny adgangskode.", ["type"=>"error"]);

			// session()->value("temp-username", $login_status["email"]);
			header("Location: /login");
			exit();

		}
		// User could not be logged in – save username temporarily and redirect back to login to leave POST state
		else {

			session()->value("temp-username", getPost("username"));
			message()->addMessage("Ugyldig adgangskode", ["type"=>"error"]);
			header("Location: /login");
			exit();

		}

	}


	// LOGOFF

	// Manual dual logoff
	// login/logoff
	else if(count($action) == 1 && $action[0] == "logoff") {

		security()->logoff();

		exit();
	}



	// CONFIRM ACCOUNT

	// login/bekraeft-konto
	else if(count($action) == 1 && $action[0] == "bekraeft-konto") {

		// No confirm without a username
		if(session()->value("temp-username")) {

			$page->page(array(
				"templates" => "profile/confirm_account.php"
			));
			exit();
		}
		// return to login page
		else {

			message()->addMessage("Der skete en ukendt fejl. Prøv igen.", array("type" => "error"));
			header("Location: /login");
			exit();
		}

	}

	// login/opret-password
	else if(count($action) == 1 && $action[0] == "opret-password") {

		$username = session()->value("temp-username");
		$user_id = $model->getLoginUserId($username);

		// user is verified and logged in (is not a guest user)
		if($model->loginUserIsVerified($username, $user_id)) {

			// user has no password
			if(!$model->loginUserHasPassword($user_id)) {
				$page->page(array(
					"templates" => "profile/create_password.php"
				));
				exit();
				
			}
			
			// user already has password
			else {
				message()->addMessage("Prøvede du på at ændre din adgangskode? Det er ikke måden at gøre det på. Brug den grå box i højre side (nederst) i stedet.", array("type" => "error"));
				header("Location: /profil");
				exit();
			}		
			
		}

		message()->addMessage("Du prøvede at tilgå en side, du ikke har adgang til. Prøv at logge ind.", array("type" => "error"));
		header("Location: /login");
		exit();
	}


	// login/confirmAccount
	else if(count($action) == 1 && $action[0] == "confirmAccount" && security()->validateCsrfToken()) {

		$username = session()->value("temp-username");
		$verification_code = getPost("verification_code");
			


		// confirmUsername returns either: user_id, false or an object with status "USER_VERIFIED"
		$user_id = $model->confirmUsername($username, $verification_code);



		// user has already been verified
		if($user_id && isset($user_id["status"]) && $user_id["status"] == "USER_VERIFIED") {
			message()->addMessage("Din konto er allerede aktiveret! Prøv at logge ind.", array("type" => "error"));
			header("Location: /login");
			exit();
		}

		// Successful verification
		else if($user_id) {

			// if user has password, forward to login page
			if($model->loginUserHasPassword($user_id)) {

				message()->addMessage("Din konto er nu aktiveret og du kan logge ind.");
				header("Location: /login");
				exit();
			}
			// if user does not have password, forward to password creation page
			else {

				header("Location: /login/opret-password");
				exit();
			}

		}

		// could not verify
		else {
			session()->reset("temp-username");
			message()->addMessage("Beklager, det lykkedes ikke at aktivere din konto! Brugte du den rigtige verificeringskode?", array("type" => "error"));
			header("Location: /login");
			exit();
		}

	}


	// login/setPassword
	else if(count($action) == 1 && $action[0] == "setPassword" && security()->validateCsrfToken()) {

		$result = $model->setFirstPassword();

		// already has password
		if($result && isset($result["status"]) && $result["status"] == "HAS_PASSWORD") {
			message()->addMessage("Din konto har allerede en adgangskode! Prøv at logge ind.", array("type" => "error"));
			session()->value("temp-username", getPost("username"));
			header("Location: /login");
			exit();			
		}
		
		// password created
		else if($result) {
			message()->addMessage("Du har oprettet din adgangskode og aktiveret din bruger.");
			header("Location: /bliv-medlem/bekraeft/kvittering");
			exit();
		}
		
		// error saving password
		else {
			message()->addMessage("Koden kunne ikke gemmes", array("type" => "error"));
			header("Location: /login/opret-password");
			exit();
		}

	}



	// RESET PASSWORD
	else if($action[0] == "glemt") {

		// login/glemt
		if(count($action) == 1) {
			$page->page(array(
				"templates" => "pages/forgot_password.php"
			));
			exit();
		}

		// login/glemt/nulstilling
		else if(count($action) == 2 && $action[1] == "nulstilling") {
			$page->page(array(
				"templates" => "pages/forgot_password_code.php"
			));
			exit();
		}

		// login/glemt/nyt-password
		else if(count($action) == 2 && $action[1] == "nyt-password") {
			$page->page(array(
				"templates" => "pages/forgot_password_set_new_password.php"
			));
			exit();
		}

	}

	// login/requestReset
	else if(count($action) == 1 && $action[0] == "requestReset" && security()->validateCsrfToken()) {

		// request password reset
		if($model->requestPasswordReset($action)) {
			header("Location: glemt/nulstilling");
			exit();
		}

		// could not create reset request
		else {
			message()->addMessage("Beklager, du kan ikke nulstille password for den givne bruger!", array("type" => "error"));
			header("Location: glemt");
			exit();
		}
	}

	// login/validateCode
	else if(count($action) == 1 && $action[0] == "validateCode" && security()->validateCsrfToken()) {

		// code is valid
		$token = $model->validateCode($action);
		if($token) {
			session()->value("temp-reset-token", $token);
			header("Location: glemt/nyt-password");
			exit();
		}

		// code is not valid
		else {
			message()->addMessage("Beklager, din nulstillingskode er forkert!", array("type" => "error"));
			header("Location: glemt/nulstilling");
			exit();
		}
	}

	// login/resetPassword
	else if(count($action) == 1 && $action[0] == "resetPassword" && security()->validateCsrfToken()) {

		// creating new password
		if($model->resetPassword($action)) {
			message()->resetMessages();
			message()->addMessage("Din adgangskode blev opdateret.");
			header("Location: /login");
			exit();
		}

		// could not create new 
		else {
			message()->resetMessages();
			message()->addMessage("Ugyldig adgangskode", array("type" => "error"));
			header("Location: glemt/nyt-password");
			exit();
		}
	}
}

// If there's a user currently logged in
if(session()->value("user_group_id")>1) {
	header("Location: /profil");
	exit();
}

// Default template
$page->page(array(
	"templates" => "pages/kbhff-login.php",
	"type" => "login"
));


?>
